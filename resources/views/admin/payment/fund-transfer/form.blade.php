<div class="form-group">
    <label class="required">Bank (From):</label>
    <select name="from_bank_id" class="form-control @error('from_bank_id') is-invalid @enderror select2" required>
        <option value="">Select Bank</option>
        @php($from_bank_id = old('from_bank_id', isset($data) ? $data->from_bank_id : ''))
        @foreach ($banks as $bank)
            <option value="{{ $bank->id }}" {{ $from_bank_id == $bank->id ? 'selected' : '' }}>
                {{ $bank->name }}</option>
        @endforeach
    </select>
    @error('from_bank_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Bank (To):</label>
    <select name="to_bank_id" class="form-control @error('to_bank_id') is-invalid @enderror select2" required>
        <option value="">Select Bank</option>
        @php($to_bank_id = old('to_bank_id', isset($data) ? $data->to_bank_id : ''))
        @foreach ($banks as $bank)
            <option value="{{ $bank->id }}" {{ $to_bank_id == $bank->id ? 'selected' : '' }}>
                {{ $bank->name }}</option>
        @endforeach
    </select>
    @error('to_bank_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Date:</label>
    <input type="text" class="form-control @error('date') is-invalid @enderror datepicker" name="date"
        value="{{ old('date', isset($data) ? $data->date : '') }}" required>
    @error('date')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="">Note:</label>
    <textarea type="text" class="form-control  @error('note') is-invalid @enderror" name="note" rows="4">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
    @error('note')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Amount:</label>
    <input type="number" step="any" min="0" class="form-control  @error('amount') is-invalid @enderror"
        name="amount" value="{{ old('amount', isset($data) ? $data->amount : '') }}" required>
    @error('amount')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
