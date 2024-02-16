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
    <label class="required">Email:</label>
    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email"
        value="{{ old('email', isset($data) ? $data->email : '') }}" required>
    @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
    <label class="{!! isset($create) ? 'required' : '' !!}">Password:</label>
    <input type="password" class="form-control" name="password" {{ isset($create) ? 'required' : '' }}>

    @if ($errors->has('password'))
        <span class="help-block">{{ $errors->first('password') }}</span>
    @endif
</div>

<div class="form-group">
    <label class="{!! isset($create) ? 'required' : '' !!}">Confirm password:</label>
    <input type="password" class="form-control" name="password_confirmation" {{ isset($create) ? 'required' : '' }}>
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
