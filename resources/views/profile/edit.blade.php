<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Profil Saya</h2>
            <p>Kelola informasi akun, ganti kata sandi, atau hapus akun</p>
        </div>

        {{-- UPDATE INFO PROFIL --}}
        <div class="auth-box" style="margin:0 0 40px 0; max-width:600px;">
            <h1 style="font-size:1.2rem;">Informasi Profil</h1>
            <p class="subtitle">Ubah nama dan alamat email akunmu.</p>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p style="font-size:0.8rem; color:#FFF200; margin-bottom:15px;">
                        Emailmu belum diverifikasi.
                        <button form="send-verification" style="background:none;border:none;color:#FFF200;text-decoration:underline;cursor:pointer;padding:0;">
                            Klik untuk kirim ulang email verifikasi.
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p style="font-size:0.8rem; color:#55FF55; margin-bottom:15px;">Link verifikasi baru sudah dikirim ke emailmu.</p>
                    @endif
                @endif

                <button class="btn-submit">Simpan</button>
            </form>

            <form id="send-verification" method="POST" action="{{ route('verification.send') }}" style="display:none;">
                @csrf
            </form>
        </div>

        {{-- GANTI KATA SANDI --}}
        <div class="auth-box" style="margin:0 0 40px 0; max-width:600px;">
            <h1 style="font-size:1.2rem;">Ganti Kata Sandi</h1>
            <p class="subtitle">Pastikan pakai kata sandi yang kuat dan tidak dipakai di tempat lain.</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="form-group">
                    <label>Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" autocomplete="current-password">
                    @error('current_password', 'updatePassword') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="password" autocomplete="new-password">
                    @error('password', 'updatePassword') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password">
                    @error('password_confirmation', 'updatePassword') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <button class="btn-submit">Ganti Kata Sandi</button>
            </form>
        </div>

        {{-- HAPUS AKUN --}}
        <div class="auth-box" style="margin:0; max-width:600px; border-color:rgba(255,85,85,0.3);">
            <h1 style="font-size:1.2rem; color:#FF5555;">Hapus Akun</h1>
            <p class="subtitle">Setelah dihapus, semua data akunmu akan hilang permanen. Unduh data penting sebelum lanjut.</p>

            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin mau hapus akun ini? Tindakan ini permanen dan tidak bisa dibatalkan.')">
                @csrf
                @method('delete')

                <div class="form-group">
                    <label>Konfirmasi Kata Sandi</label>
                    <input type="password" name="password" placeholder="Masukkan kata sandimu untuk konfirmasi">
                    @error('password', 'userDeletion') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" style="width:100%; padding:16px; background:#FF5555; color:#000; border:none; font-weight:900; text-transform:uppercase; letter-spacing:1px; cursor:pointer;">
                    Hapus Akun Saya
                </button>
            </form>
        </div>
    </section>
</x-app-layout>
