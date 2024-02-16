<div class="form-group">
    <label class="required">Name:</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required>
    @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Mobile No:</label>
    <input type="number" class="form-control @error('mobile') is-invalid @enderror" name="mobile"
        value="{{ old('mobile', isset($data) ? $data->mobile : '') }}" required>
    @error('mobile')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label>Address:</label>
    <textarea type="text" class="form-control @error('address') is-invalid @enderror" name="address"> {{ old('address', isset($data) ? $data->address : '') }} </textarea>
    @error('address')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label>Date of Birth:</label>
    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror datepicker" name="date_of_birth"
        value="{{ old('date_of_birth', isset($data) ? $data->date_of_birth : '') }}">
    @error('date_of_birth')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label>Shop Name:</label>
    <input type="text" class="form-control @error('shop_name') is-invalid @enderror" name="shop_name"
        value="{{ old('shop_name', isset($data) ? $data->shop_name : '') }}">

    @error('shop_name')
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
