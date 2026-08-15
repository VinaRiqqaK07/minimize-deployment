<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\AuditLog;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pendingArtworks = Artwork::with('artist')->where('status', 'pending')->latest()->get();
        $allArtworks = Artwork::with('artist')->where('status', '!=', 'pending')->latest()->get();
        $recentOrders = Order::with(['buyer', 'artwork', 'merchandise'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('pendingArtworks', 'allArtworks', 'recentOrders'));
    }

    public function approveArtwork(Artwork $artwork)
    {
        $artwork->update(['status' => 'approved']);

        AuditLog::record('artwork.approved', $artwork, ['title' => $artwork->title]);

        return back()->with('status', 'Karya "'.$artwork->title.'" disetujui dan tayang di katalog.');
    }

    public function rejectArtwork(Artwork $artwork)
    {
        $artwork->update(['status' => 'rejected']);

        AuditLog::record('artwork.rejected', $artwork, ['title' => $artwork->title]);

        return back()->with('status', 'Karya "'.$artwork->title.'" ditolak.');
    }

    /**
     * Hapus karya secara PERMANEN (termasuk file gambarnya dari storage).
     * Beda dari reject — ini tidak bisa dibatalkan, dipakai untuk karya
     * yang melanggar aturan atau memang perlu dibersihkan total.
     */
    public function destroyArtwork(Artwork $artwork)
    {
        abort_if($artwork->status === 'sold', 422, 'Karya yang sudah terjual tidak bisa dihapus, demi menjaga riwayat transaksi.');

        if ($artwork->image_path) {
            Storage::disk(config('filesystems.default'))->delete($artwork->image_path);
        }

        $title = $artwork->title;

        AuditLog::record('artwork.deleted_by_admin', null, [
            'title' => $title,
            'artwork_id' => $artwork->id,
            'artist' => $artwork->artist->name ?? '-',
        ]);

        $artwork->delete();

        return back()->with('status', 'Karya "'.$title.'" dihapus permanen oleh admin.');
    }
}
