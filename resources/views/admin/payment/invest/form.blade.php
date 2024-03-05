<div class="form-group">
    <label class="required">Investor:</label>
    <select name="investor_id" class="form-control @error('investor_id') is-invalid @enderror select2" required>
        <option value="">Select Investor</option>
        @php($investor_id = old('investor_id', isset($data) ? $data->investor_id : ''))
        @foreach ($investors as $investor)
            <option value="{{ $investor->id }}" {{ $investor_id == $investor->id ? 'selected' : '' }}>
                {{ $investor->name }}</option>
        @endforeach
    </select>

    @error('investor_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Bank:</label>
    <select name="bank_id" class="form-control @error('bank_id') is-invalid @enderror select2" required>
        <option value="">Select Bank</option>
        @php($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
        @foreach ($banks as $bank)
            <option value="{{ $bank->id }}" {{ $bank_id == $bank->id ? 'selected' : '' }}>{{ $bank->name }}
            </option>
        @endforeach
    </select>

    @error('bank_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Date :</label>
    <input type="text" class="form-control @error('date') is-invalid @enderror datepicker" name="date"
        value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}" required>

    @error('date')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror

</div>

<div class="form-group">
    <label>Note:</label>
    <textarea type="text" class="form-control" name="note" rows="4">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
    @error('note')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Amount:</label>
    <input type="number" step="any" min="0" class="form-control @error('amount') is-invalid @enderror"
        name="amount" value="{{ old('amount', isset($data) ? $data->amount : '') }}" required>

    @error('amount')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
