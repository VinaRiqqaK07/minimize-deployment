<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Kelola Admin</h2>
            <p>Tambah atau cabut akses admin lain</p>
        </div>

        <div class="auth-box" style="margin:0 0 60px 0; max-width:600px;">
            <h1 style="font-size:1.2rem;">Tambah Admin Baru</h1>
            <form method="POST" action="{{ route('admin.users.store') }}" style="margin-top:20px;">
                @csrf
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" required>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label>Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" required>
                </div>

                <button class="btn-submit">Tambah Admin</button>
            </form>
        </div>

        <h3 style="margin-bottom:20px; color:#999; text-transform:uppercase; font-size:0.85rem; letter-spacing:1px;">Daftar Admin</h3>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse ($admins as $admin)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:18px 20px; background:rgba(13,16,23,0.85); border:1px solid rgba(255,255,255,0.05);">
                    <div>
                        <p style="font-weight:600;">{{ $admin->name }}</p>
                        <p style="font-size:0.85rem; color:#777;">{{ $admin->email }}</p>
                    </div>
                    @if ($admin->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" onsubmit="return confirm('Cabut akses admin dari {{ $admin->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#FF5555;cursor:pointer;font-size:0.85rem;">Cabut Akses</button>
                        </form>
                    @else
                        <span style="font-size:0.75rem; color:#444;">Ini akunmu</span>
                    @endif
                </div>
            @empty
                <p style="color:#666;">Belum ada admin lain.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
