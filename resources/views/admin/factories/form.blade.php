<div class="form-group">
    <label class="required">Name:</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required>
    @error('name')
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
    <label>Google Map:</label>
    <textarea type="text" class="form-control @error('google_map') is-invalid @enderror" name="google_map" rows="6"> {{ old('google_map', isset($data) ? $data->google_map : '') }} </textarea>
    @error('google_map')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label>Email:</label>
    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email"
        value="{{ old('email', isset($data) ? $data->email : '') }}">
    @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror

</div>
<div class="form-group">
    <label>Mobile No:</label>
    <input type="text" class="form-control @error('mobile_no') is-invalid @enderror" name="mobile_no"
        value="{{ old('mobile_no', isset($data) ? $data->mobile_no : '') }}">
    @error('mobile_no')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Status:</label>
    <select name="status" class="form-control @error('status') is-invalid @enderror select2" required>
        @php ($status = old('status', isset($data) ? $data->status : ''))
        @foreach(['Active', 'Inactive'] as $sts)
            <option value="{{ $sts }}" {{ ($status == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
        @endforeach
    </select>
    @error('status')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
