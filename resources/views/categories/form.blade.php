<div class="form-group">
    <label for="name">Nama kategori</label>
    <input type="text" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required autofocus>
    @error('name') <div class="field-error">{{ $message }}</div> @enderror
</div>