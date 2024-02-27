<div class="form-group">
    <label class="required">Supplier :</label>
        <select class="form-control @error('supplier_id') is-invalid @enderror select2" name="supplier_id" required>
            <option value="">Select Supplier</option>
            @php($supplier_id = old('supplier_id', isset($data) ? $data->supplier_id : ''))
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    {{ $supplier_id == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}</option>
            @endforeach
        </select>

        @error('supplier_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label class="required">Date :</label>
        <input type="text" class="form-control @error('date') is-invalid @enderror datepicker" name="date"
            value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}"
            required>

            @error('date')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
</div>

<div class="form-group">
    <label>Challan Number :</label>
        <input type="text" class="form-control @error('challan_number') is-invalid @enderror" name="challan_number"
            value="{{ old('challan_number', isset($data) ? $data->challan_number : '') }}">

            @error('challan_number')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
</div>

<div class="form-group">
    <label>Challan Date :</label>
        <input type="text" class="form-control @error('challan_date') is-invalid @enderror datepicker" name="challan_date"
            value="{{ old('challan_date', isset($data) ? $data->challan_date : '') }}">

            @error('challan_date')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
</div>

<div class="box-body table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th></th>
                <th class="required">Category</th>
                <th class="required">Product</th>
                <th class="required">Unit</th>
                <th class="required">Quantity</th>
                <th class="required">Unit Price</th>
                <th class="required">Total Price</th>
            </tr>
        </thead>
        <tbody id="responseHtml">
            @foreach ($items as $key => $item)
                <tr id="row{{ $key }}" class="subRow">
                    <input type="hidden" name="stock_item_id[]"
                        value="{{ $item->id }}">
                    <td>
                        @if ($key == 0)
                            <a class="btn btn-success btn-flat"
                                onclick="addRow({{ $key }})"><i
                                    class="fa fa-plus"></i></a>
                        @else
                            <a class="btn btn-danger btn-flat"
                                onclick="removeRow({{ $key }})"><i
                                    class="fa fa-minus"></i></a>
                        @endif
                    </td>
                    <td>
                        <select name="category_id[]" id="category_id{{ $key }}"
                            class="form-control select2"
                            onchange="onChangeCategory(this, {{ $key }})"
                            required>
                            <option value="" selected disabled>Select Category
                            </option>
                            @foreach ($categories as $category)
                                @if (!$category->parent_id)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', isset($item) ? $item->category_id : '') == $category->id ? 'selected' : '' }}
                                        disabled>
                                        {{ $category->name }}
                                    </option>
                                @endif
                                @if ($category->relationLoaded('children') && count($category->children) > 0)
                                    @foreach ($category->children as $child)
                                        <option value="{{ $child->id }}"
                                            {{ old('category_id', isset($item) ? $item->category_id : '') == $child->id ? 'selected' : '' }}
                                            data-category-products="{{ json_encode($child->products) }}">
                                            -- {{ $child->name }}
                                        </option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td class="product">
                        <select name="product_id[]" id="product_id{{ $key }}"
                            class="form-control select2"
                            onchange="onChangeProduct(this,{{ $key }})" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    data-product-units="{{ json_encode($product->baseUnit->units) }}"
                                    data-price="{{ $product->purchase_price }}"
                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->code }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="unit">
                        <select name="unit_id[]" class="form-control select2"
                            onchange="onChangeUnitPrice({{ $key }})" required>
                            <option value="">Select Unit</option>
                            @if (isset($edit))
                                @foreach ($item->product->baseUnit->units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                    <td>
                        <input type="number" step="any" min="1"
                            class="form-control" name="quantity[]"
                            id="quantity{{ $key }}"
                            value="{{ $item->quantity }}"
                            onchange="chkItemPrice({{ $key }})"
                            onkeyup="chkItemPrice({{ $key }})" required>
                    </td>
                    <td>
                        <input type="number" step="any" min="0"
                            class="form-control unit_price" name="unit_price[]"
                            id="unit_price{{ $key }}"
                            value="{{ $item->unit_price }}"
                            onkeyup="chkItemPrice({{ $key }})" required>
                    </td>
                    <td>
                        <input type="number" step="any" min="0"
                            class="form-control" name="amount[]"
                            id="amount{{ $key }}" value="{{ $item->amount }}"
                            readonly>
                    </td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td class="text-right" colspan="4"><strong>Total Quantity :</strong>
                </td>
                <td class="text-right"><input type="text" class="form-control"
                        readonly name="total_quantity" id="total_quantity"
                        value="{{ isset($edit) ? $items->sum('quantity') : '' }}"></td>

                <td class="text-right"><strong>Sub Total Amount :</strong></td>
                <td class="text-right"><input type="text" class="form-control"
                        readonly name="subtotal_amount" id="subtotal_amount"
                        value="{{ isset($edit) ? numberFormat($items->sum('amount')) : '' }}">
                </td>
            </tr>

            <td class="text-right" colspan="6"><strong>Discount Amount :</strong>
            </td>
            <td class="text-right"><input type="text" class="form-control"
                    name="discount_amount" id="discount_amount"
                    value="{{ old('discount_amount', isset($data) ? $data->discount_amount : '') }}"
                    onkeyup="chkAmount('discount_amount')"></td>
            </tr>
            <tr>
                <td class="text-right" colspan="6"><strong>Total Amount :</strong></td>
                <td class="text-right"><input type="text" class="form-control"
                        readonly name="total_amount" id="total_amount"
                        value="{{ old('total_amount', isset($data) ? $data->total_amount : '') }}">
                </td>
            </tr>
            <tr>
                <td class="text-right" colspan="6"><strong>Paid Amount :</strong></td>
                <td class="text-right"><input type="text" class="form-control"
                        name="paid_amount" id="paid_amount"
                        value="{{ old('paid_amount', isset($data) ? $data->paid_amount : '') }}"
                        onkeyup="chkPaid()"></td>
            </tr>
            <tr>
                <td class="text-right" colspan="6"><strong>Bank :</strong></td>
                <td class="text-right">
                    <select name="bank_id" id="bank_id" class="form-control">
                        <option value="">Select Bank</option>
                        @php($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}"
                                {{ $bank_id == $bank->id ? 'selected' : '' }}>
                                {{ $bank->name }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('bank_id'))
                        <span class="help-block">{{ $errors->first('bank_id') }}</span>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
</div>

@section('scripts')
    <script>
        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var CategoryOptions = $('#category_id' + key).html();
            var productOptions = $('#product_id' + key).html();

            var html = `<tr id="row${newKey}" class="subRow">
                <input type="hidden" name="stock_item_id[]" value="0">
                <td><a class="btn btn-danger btn-flat" onclick="removeRow(${newKey})"><i class="fa fa-minus"></i></a></td>
                <td>
                    <select name="category_id[]" id="category_id${newKey}" class="form-control select2"  onchange="onChangeCategory(this, ${newKey})" required>${CategoryOptions}</select>
                </td>
                <td class="product">
                    <select name="product_id[]" id="product_id${newKey}" class="form-control select2" onchange="onChangeProduct(this, ${newKey})" required>${productOptions}</select>
                </td>
                <td class="unit">
                    <select name="unit_id[]" class="form-control select2" onchange="onChangeUnitPrice(${newKey})" required></select>
                </td>
                <td>
                    <input type="number" step="any" min="1" class="form-control" name="quantity[]" id="quantity${newKey}" onchange="chkItemPrice(${newKey})" onkeyup="chkItemPrice(${newKey})" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control unit_price" name="unit_price[]" id="unit_price${newKey}" onkeyup="chkItemPrice(${newKey})" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control" name="amount[]" id="amount${newKey}" readonly>
                </td>
            </tr>`;
            $('#responseHtml').append(html);
            $('#product_id' + newKey).val('');
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select',
                tag: true
            });
        }

        function removeRow(key) {
            $('#row' + key).remove();
        }

        function chkItemPrice(key) {
            var quantity = Number($('#quantity' + key).val());
            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Please Provide Valid Quantity!');
            }

            var unit_price = Number($('#unit_price' + key).val());
            if (isNaN(unit_price)) {
                $('#unit_price' + key).val('');
                $('#unit_price' + key).focus();
                alerts('Please Provide Valid Price!');
            }

            var amount = (quantity * unit_price);
            $('#amount' + key).val(amount.toFixed(2));
            totalCal();
        }

        function chkAmount(field) {
            var fieldAmount = Number($('#' + field).val());
            if (isNaN(fieldAmount)) {
                $('#' + field).val('');
                $('#' + field).focus();
                alerts('Please Provide Valid Amount!');
            }

            totalCal();
        }

        function totalCal() {
            var quantity = 0;
            $("input[id^='quantity']").each(function() {
                quantity += +$(this).val();
            });
            $('#total_quantity').val(quantity);

            var subTotal = 0;
            $("input[id^='amount']").each(function() {
                subTotal += +Number($(this).val());
            });
            $('#subtotal_amount').val(subTotal.toFixed(2));

            var discountAmount = Number($('#discount_amount').val());

            var total = ((subTotal) - discountAmount);
            $('#total_amount').val(total.toFixed(2));

        }

        function onChangeCategory(e, key) {
            var selectedOption = e.options[e.selectedIndex];

            $.ajax({
                url: "{{ url('stock/products-by-category') }}/" + e.value,
                type: "GET",
                success: function(response) {
                    // console.log(response.products);
                    var productSelect = document.getElementById('product_id' + key);
                    productSelect.innerHTML = '<option value="">Select Product</option>';
                    response.products.forEach(function(product) {
                        var option = document.createElement('option');
                        option.value = product.id;
                        option.text = product.name + ' (' + product.code + ')';
                        option.setAttribute('data-product-units', JSON.stringify(product.base_unit.units));
                        option.setAttribute('data-price', product.purchase_price);
                        productSelect.appendChild(option);
                    });
                },
                error: function(response) {
                    F
                    alert(response.error);
                }
            });
        }

        function onChangeProduct(e, key) {
            var productUnits = JSON.parse(e.options[e.selectedIndex].dataset.productUnits || '[]');
            var unitSelect = e.closest('tr').querySelector('td.unit select');
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            productUnits.forEach(function(unit) {
                var option = document.createElement('option');
                option.value = unit.id;
                option.text = unit.name;
                unitSelect.appendChild(option);
            });
            // $('#unit_price' + key).val(e.options[e.selectedIndex].dataset.price);
            var product_id = $('#product_id' + key).val();
            var rowId = $(".subRow").length;
            var productOptions = $('#product_id' + key).html();
            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#product_id' + x).val() == product_id) {
                        $('#product_id' + key).html(productOptions);
                        alerts('This Product Already Entered In This Purchase.');
                        return false;
                    }
                }
            }
        }

        function onChangeUnitPrice(key) {

            var productSelect = document.getElementById('row' + key).closest('tr').querySelector('td.product select');
            var productUnits = JSON.parse(productSelect.options[productSelect.selectedIndex].dataset.productUnits || '[]');
            var price = JSON.parse(productSelect.options[productSelect.selectedIndex].dataset.price || '[]');
            var unitSelect = document.getElementById('row' + key).closest('tr').querySelector('td.unit select');
            var unit = productUnits[unitSelect.selectedIndex - 1];

            var unit_price = Number(price) * Number(unit.quantity);
            unit_price = Number(unit_price.toFixed(2));
            $('#unit_price' + key).val(unit_price);

        }

        function chkPaid() {
            var paidAmount = Number($('#paid_amount').val());
            if (isNaN(paidAmount)) {
                $('#paid_amount').val('');
                $('#paid_amount').focus();
                alert('Please Provide Valid Amount!');
            }

            var totalAmount = Number($('#total_amount').val());
            if (paidAmount > totalAmount) {
                $('#paid_amount').val('');
                $('#paid_amount').focus();
                alert('You Can\'t Paid greater than Total Amount!');
            }

            if (paidAmount > 0) {
                $('#bank_id').prop('required', true);
            } else {
                $('#bank_id').prop('required', false);
            }
        }
    </script>
@endsection