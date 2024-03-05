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

<div class="box-body table-responsive bankdiv">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th></th>
                <th class="required">Category</th>
                <th class="required">Bank</th>
                <th class="required">Amount</th>
            </tr>
        </thead>

        <tbody id="responseHtml">

            @foreach ($items as $key => $item)
                <tr class="subRow" id="row{{ $key }}">
                    <input type="hidden" name="income_item_id[]"value="{{ $item->id }}">
                    <td>
                        @if ($key == 0)
                            <a class="btn btn-success btn-flat" onclick="addRow({{ $key }})"><i
                                    class="fa fa-plus"></i></a>
                        @else
                            <a class="btn btn-danger btn-flat" onclick="removeRow({{ $key }})"><i
                                    class="fa fa-minus"></i></a>
                        @endif
                    </td>
                    <td>
                        <select name="category_id[]" id="category_id{{ $key }}"
                            onchange="checkCategory({{ $key }})" class="form-control select2" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $item->income_category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="bank_id[]" id="bank_id{{ $key }}" class="form-control select2" required>
                            <option value="">Select Bank</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}"
                                    {{ $item->bank_id == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="any" min="0" class="form-control amount" name="amount[]"
                            id="amount{{ $key }}" value="{{ $item->amount }}"
                            onclick="checkAmountCost({{ $key }})"
                            onkeyup="checkAmountCost({{ $key }})" required>
                    </td>
                </tr>
            @endforeach

        </tbody>

        <tfoot>
            <tr>
                <td class="text-right" colspan="3"><strong>Total
                        :</strong></td>

                <td class="text-right"><input type="text" class="form-control" readonly name="total_amount"
                        id="total_amount" value="{{ isset($data) ? $items->sum('amount') : '' }}">
                </td>
            </tr>
        </tfoot>
    </table>
</div>


<script>
    function addRow(key) {
        var newKey = $("tr[id^='row']").length;
        var categoryoptions = $('#category_id' + key).html();
        var bankoptions = $('#bank_id' + key).html();

        var html = `<tr class="subRow" id="row` + newKey + `">
            <td><a class="btn btn-danger btn-flat" onclick="removeRow(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
            <input type="hidden" name="income_item_id[]" value="0">
            <td>
                <select name="category_id[]" id="category_id` + newKey +
            `" class="form-control select2" onchange="checkCategory(` + newKey + `)" required>` + categoryoptions + `</select>
            </td>
            <td>
                <select name="bank_id[]" id="bank_id` + newKey + `" class="form-control select2" required>` +
            bankoptions + `</select>
            </td>
            <td>
                <input type="number" step="any" min="1" class="form-control amount" name="amount[]" id="amount` +
            newKey +
            `" onkeyup="checkAmountCost(` + newKey + `)" onclick="checkAmountCost(` + newKey + `)" required>
            </td>
        </tr>`;
        $('#responseHtml').append(html);
        $('#category_id' + newKey).val('');
        $('#bank_id' + newKey).val('');
        $('.select2').select2({
            width: '100%',
            placeholder: 'Select',
            tag: true
        });
    }

    function removeRow(key) {
        $('#row' + key).remove();
    }

    function checkAmountCost(key) {

        let amount = Number($('#amount' + key).val());

        if (isNaN(amount)) {
            $('#amount' + key).val('');
            $('#amount' + key).focus();
            alerts('Please Provide Valid Amount!');
        }
        var total_amount = 0;
        $(".amount").each(function() {
            total_amount += Number($(this).val());
        });
        $('#total_amount').val(Number(total_amount));
    }

    function checkCategory(key) {
        var categoryId = $('#category_id' + key).val();
        var rowId = $(".subRow").length;
        for (var x = 0; x < rowId; x++) {
            if (x != key) {
                if ($('#category_id' + x).val() == categoryId) {
                    $('#category_id' + key).val('');
                    alerts('This category already exists in this Expense!!');
                    return false;
                }
            }
        }
    }
</script>
