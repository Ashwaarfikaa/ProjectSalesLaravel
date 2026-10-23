@php $editing = isset($user); @endphp
<div class="form-grid">
    <div class="form-group">
        <label for="name">Nama</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" required {{ $editing && $user->id === auth()->id() ? 'disabled' : '' }}>
            <option value="staff" {{ old('role', $user->role ?? 'staff') === 'staff' ? 'selected' : '' }}>Staff</option>
            <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin / Owner</option>
        </select>
        @if ($editing && $user->id === auth()->id())
            <input type="hidden" name="role" value="{{ $user->role }}">
            <p class="hint">Anda tidak dapat mengubah role akun sendiri.</p>
        @endif
        @error('role') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group"></div>
    <div class="form-group">
        <label for="password">Kata sandi {{ $editing ? '(kosongkan jika tidak diubah)' : '' }}</label>
        <input type="password" id="password" name="password" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
        @error('password') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label for="password_confirmation">Ulangi kata sandi</label>
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
    </div>
</div>