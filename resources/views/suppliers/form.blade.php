<div class="form-grid">
    <div class="form-group full">
        <label for="name">Nama pemasok</label>
        <input type="text" id="name" name="name" value="{{ old('name', $supplier->name ?? '') }}" required>
        @error('name') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label for="phone">Telepon</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}">
        @error('phone') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $supplier->email ?? '') }}">
        @error('email') <div class="field-error">{{ $message }}</div> @enderror
    </div>
</div>