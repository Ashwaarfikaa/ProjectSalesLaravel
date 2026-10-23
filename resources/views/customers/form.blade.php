<div class="form-grid">
    <div class="form-group full">
        <label for="name">Nama pelanggan</label>
        <input type="text" id="name" name="name" value="{{ old('name', $customer->name ?? '') }}" required autofocus>
        @error('name') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label for="phone">Telepon</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone ?? '') }}">
        @error('phone') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $customer->email ?? '') }}">
        @error('email') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group full">
        <label for="address">Alamat</label>
        <textarea id="address" name="address" rows="3">{{ old('address', $customer->address ?? '') }}</textarea>
        @error('address') <div class="field-error">{{ $message }}</div> @enderror
    </div>
</div>