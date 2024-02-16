<div class="form-group">
    <label class="required">Name:</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required>
    @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Code:</label>
    <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
        value="{{ old('code', isset($data) ? $data->code : '') }}" required>

    @error('code')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label>Model:</label>
    <input type="text" class="form-control @error('model') is-invalid @enderror" name="model"
        value="{{ old('model', isset($data) ? $data->model : '') }}">

    @error('model')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Category:</label>
    <select class="form-control @error('category_id') is-invalid @enderror select2" name="category_id" required>
        <option value="" selected disabled>Select Category</option>
        @foreach ($categories as $item)
            @if (!$item->parent_id)
                <option value="{{ $item->id }}"
                    {{ old('category_id', isset($data) ? $data->category_id : '') == $item->id ? 'selected' : '' }}
                    disabled>
                    {{ $item->name }}
                </option>
            @endif
            @if ($item->relationLoaded('children') && count($item->children) > 0)
                @foreach ($item->children as $child)
                    <option value="{{ $child->id }}"
                        {{ old('category_id', isset($data) ? $data->category_id : '') == $child->id ? 'selected' : '' }}>
                        -- {{ $child->name }}
                    </option>
                @endforeach
            @endif
        @endforeach
    </select>
    @error('category_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Base Unit:</label>
    <select class="form-control @error('base_unit_id') is-invalid @enderror select2" name="base_unit_id" required>
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
    <label class="required">Purchase Price:</label>
    <input type="number" step="any" min="0"
        class="form-control @error('purchase_price') is-invalid @enderror" name="purchase_price"
        value="{{ old('purchase_price', isset($data) ? $data->purchase_price : '') }}" required>
    @error('purchase_price')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label>MRP:</label>
    <input type="number" step="any" min="0" class="form-control @error('mrp') is-invalid @enderror"
        name="mrp" value="{{ old('mrp', isset($data) ? $data->mrp : '') }}">
    @error('mrp')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label>Discount Percentage:</label>
    <input type="number" step="any" min="0" max="100"
        class="form-control @error('discount_percentage') is-invalid @enderror" name="discount_percentage"
        value="{{ old('discount_percentage', isset($data) ? $data->discount_percentage : '') }}">

    @error('discount_percentage')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>


<div class="form-group">
    <label class="required">Status:</label>
    <select name="status" class="form-control @error('status') is-invalid @enderror select2" required>
        @php($status = old('status', isset($data) ? $data->status : ''))
        @foreach (['Active', 'Inactive'] as $sts)
            <option value="{{ $sts }}" {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}
            </option>
        @endforeach
    </select>
    @error('status')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
