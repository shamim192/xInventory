<div class="form-group">
    <label class="required">Name:</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required>
    @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Branch:</label>
    <input type="text" class="form-control @error('branch') is-invalid @enderror" name="branch"
        value="{{ old('branch', isset($data) ? $data->branch : '') }}" required>
    @error('branch')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Account Number:</label>
    <input type="number" class="form-control @error('account_number') is-invalid @enderror" name="account_number"
        value="{{ old('account_number', isset($data) ? $data->account_number : '') }}" required>
    @error('account_number')
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
