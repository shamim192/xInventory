<div class="form-group">
    <label class="required">Name:</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required>
    @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Base Unit:</label>
    <select class="form-control  @error('base_unit_id') is-invalid @enderror select2" name="base_unit_id" required>
        <option value="" selected disabled>Select Base Unit</option>
        @foreach ($baseUnits as $baseUnit)
            <option value="{{ $baseUnit->id }}"
                {{ old('base_unit_id', isset($data) ? $data->base_unit_id : '') == $baseUnit->id ? 'selected' : '' }}>
                {{ $baseUnit->name }}
            </option>
        @endforeach
    </select>
    @error('base_unit_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Quantity:</label>
    <input type="number" step="any" min="0" class="form-control @error('quantity') is-invalid @enderror"
        name="quantity" value="{{ old('quantity', isset($data) ? $data->quantity : '') }}" required>
    @error('quantity')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
