{{-- <div class="form-group">
    <label> Parent Category:</label>   
        <select name="parent_id" class="form-control select2 @error('parent_id') is-invalid @enderror">
            <option value="">Select Parent Category</option>
            @php ($parent_id = old('parent_id', isset($data) ? $data->parent_id : ''))
            @foreach($parents as $par)
                <option value="{{ $par->id }}" {{ ($parent_id == $par->id) ? 'selected' : '' }}>{{ $par->name }}</option>
                @if ($par->children->count() > 0)
                    @foreach($par->children as $chld)
                        <option value="{{ $chld->id }}" {{ ($parent_id == $chld->id) ? 'selected' : '' }}> -- {{ $chld->name }}</option>
                    @endforeach
                @endif
            @endforeach
        </select>
        @error('parent_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror 
</div> --}}
<div class="form-group">
    <label>Parent Category:</label>
    <select name="parent_id" class="form-control select2 @error('parent_id') is-invalid @enderror">
        <option value="">Select Parent Category</option>
        @php ($parent_id = old('parent_id', isset($data) ? $data->parent_id : ''))
        @foreach($parents as $par)
            <option value="{{ $par->id }}" {{ ($parent_id == $par->id) ? 'selected' : '' }}>{{ $par->name }}</option>
        @endforeach
    </select>
    @error('parent_id')
    <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Name:</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required>
    @error('name')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label class="required">Status:</label>
    <select name="status" class="form-control select2  @error('status') is-invalid @enderror" required>
        @php ($status = old('status', isset($data) ? $data->status : ''))
        @foreach(['Active', 'Inactive'] as $sts)
            <option value="{{ $sts }}" {{ ($status == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
        @endforeach
    </select>
    @error('status')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
