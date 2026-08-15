<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Merchandise;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect();

        // Halaman statis utama
        foreach (['home', 'catalog.index', 'buyers.index', 'register.buyer', 'register.artist', 'login'] as $route) {
            $urls->push([
                'loc' => route($route),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ]);
        }

        // Karya yang tayang publik
        Artwork::approved()->select('id', 'updated_at')->get()->each(function ($artwork) use ($urls) {
            $urls->push([
                'loc' => route('catalog.show', $artwork),
                'lastmod' => $artwork->updated_at->toAtomString(),
                'changefreq' => 'hourly',
                'priority' => '0.9',
            ]);
        });

        // Profil artis publik
        User::where('role', 'artist')->whereHas('artworks', fn ($q) => $q->approved())
            ->select('id', 'updated_at')->get()->each(function ($artist) use ($urls) {
                $urls->push([
                    'loc' => route('artists.show', $artist),
                    'lastmod' => $artist->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ]);
            });

        $xml = view('sitemap', compact('urls'))->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $content = "User-agent: *\n".
            "Allow: /\n".
            "Disallow: /admin\n".
            "Disallow: /dashboard\n".
            "Disallow: /cart\n".
            "Disallow: /checkout\n".
            "Disallow: /profile\n".
            "\n".
            'Sitemap: '.route('sitemap')."\n";

        return Response::make($content, 200, ['Content-Type' => 'text/plain']);
    }
}
