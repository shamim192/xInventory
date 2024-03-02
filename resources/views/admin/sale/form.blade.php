<div class="form-group">
    <label class="required">Customer :</label>
    <select class="form-control @error('customer_id') is-invalid @enderror select2" name="customer_id" id="customer_id"
        required>
        <option value="">Select Customer</option>
        @php($customer_id = old('customer_id', isset($data) ? $data->customer_id : ''))
        @foreach ($customers as $customer)
            <option value="{{ $customer->id }}" {{ $customer_id == $customer->id ? 'selected' : '' }}>
                {{ $customer->name }}</option>
        @endforeach
    </select>

    @error('customer_id')
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

<div class="box-body table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th></th>
                <th class="required">Category</th>
                <th class="required">Product</th>
                <th class="required">Unit</th>
                <th class="required" style="width: 110px">Quantity</th>
                <th class="required" style="width: 110px">Unit Price</th>
                <th style="width: 110px">Dis. Per.</th>
                <th style="width: 110px">Dis. Amt.</th>
                <th class="required" style="width: 110px">Total Price</th>
            </tr>
        </thead>
        <tbody id="responseHtml">
            @foreach ($items as $key => $item)
                <tr class="subRow" id="row{{ $key }}">
                    <input type="hidden" name="sale_item_id[]" value="{{ $item->id }}">
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
                        <select name="category_id[]" id="category_id{{ $key }}" class="form-control select2"
                            onchange="onChangeCategory(this, {{ $key }})" required>
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
                        <select name="product_id[]" id="product_id{{ $key }}" class="form-control select2"
                            onchange="onChangeProduct(this,{{ $key }})" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    data-product-units="{{ json_encode($product->baseUnit->units) }}"
                                    data-price="{{ $product->purchase_price }}" data-stock="{{ $product->stockQty }}"
                                    data-discount_percentage="{{ $product->discount_percentage }}"
                                    data-mrp="{{ $product->mrp }}"
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
                        <input type="hidden" id="stock{{ $key }}"
                            value="{{ isset($edit) ? $item->product->getStock($item->product_id) + $item->actual_quantity : 0 }}">
                        <input type="number" step="any" min="1" class="form-control" name="quantity[]"
                            id="quantity{{ $key }}" value="{{ $item->quantity }}"
                            onclick="chkItemPrice({{ $key }})" onkeyup="chkItemPrice({{ $key }})"
                            required>
                    </td>
                    <td>
                        <input type="number" step="any" min="0" class="form-control" name="unit_price[]"
                            id="unit_price{{ $key }}" value="{{ $item->unit_price }}"
                            onclick="chkItemPrice({{ $key }})" onkeyup="chkItemPrice({{ $key }})"
                            required>
                    </td>
                    <td>
                        <input type="number" step="any" min="0" class="form-control"
                            name="discount_percentage[]" id="discount_percentage{{ $key }}"
                            value="{{ $item->discount_percentage }}"
                            onkeyup="saleDiscountCal({{ $key }}, 'percent')"
                            onclick="saleDiscountCal({{ $key }}, 'percent')">
                    </td>
                    <td>
                        <input type="number" step="any" min="0" class="form-control"
                            name="discount_amount[]" id="discount_amount{{ $key }}"
                            value="{{ $item->discount_amount }}"
                            onkeyup="saleDiscountCal({{ $key }}, 'amount')"
                            onclick="saleDiscountCal({{ $key }}, 'amount')">
                    </td>
                    <td>
                        <input type="number" step="any" min="0" class="form-control" name="amount[]"
                            id="amount{{ $key }}" value="{{ $item->amount }}" readonly>
                    </td>

                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td class="text-right" colspan="4"><strong>Total Quantity :</strong>
                </td>
                <td class="text-right"><input type="text" class="form-control" readonly name="total_quantity"
                        id="total_quantity" value="{{ isset($edit) ? $items->sum('quantity') : '' }}"></td>

                <td class="text-right" colspan="3"><strong>Sub Total Amount :</strong>
                </td>
                <td class="text-right"><input type="text" class="form-control" readonly name="subtotal_amount"
                        id="subtotal_amount" value="{{ isset($edit) ? numberFormat($items->sum('amount')) : '' }}">
                </td>
            </tr>
            <tr>
                <td class="text-right" colspan="8"><strong>Flat Dis. Per :</strong>
                </td>
                <td class="text-right"><input type="number" step="any" min="0" class="form-control"
                        name="flat_discount_percentage" id="flat_discount_percentage"
                        value="{{ old('flat_discount_percentage', isset($data) ? $data->flat_discount_percent : '') }}"
                        onkeyup="saleTotalDiscountCal('percent')" onclick="saleTotalDiscountCal('percent')"></td>
            </tr>
            <tr>
                <td class="text-right" colspan="8"><strong>Flat Dis. Amt :</strong>
                </td>
                <td class="text-right"><input type="number" step="any" min="0" class="form-control"
                        name="flat_discount_amount" id="flat_discount_amount"
                        value="{{ old('flat_discount_amount', isset($data) ? $data->flat_discount_amount : '') }}"
                        onkeyup="saleTotalDiscountCal('amount')" onclick="saleTotalDiscountCal('amount')"></td>
            </tr>
            <tr>
                <td class="text-right" colspan="8"><strong>Total Amount :</strong></td>
                <td class="text-right"><input type="text" class="form-control" readonly name="total_amount"
                        id="total_amount" value="{{ old('total_amount', isset($data) ? $data->total_amount : '') }}">
                </td>
            </tr>
            <tr>
                <td class="text-right" colspan="8"><strong>Paid Amount :</strong></td>
                <td class="text-right"><input type="text" class="form-control" name="paid_amount"
                        id="paid_amount" value="{{ old('paid_amount', isset($data) ? $data->paid_amount : '') }}"
                        onkeyup="chkPaid()"></td>
            </tr>
            <tr>
                <td class="text-right" colspan="7"><strong>Bank :</strong></td>
                <td class="text-right" colspan="2">
                    <select name="bank_id" id="bank_id" class="form-control select2">
                        <option value="">Select Bank</option>
                        @php($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}" {{ $bank_id == $bank->id ? 'selected' : '' }}>
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

    @section('scripts')
    <script>
        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var categoryOptions = $('#category_id' + key).html();
            var productOptions = $('#product_id' + key).html();
            var unitOptions = $('#unit_id' + key).html();

            var html = `<tr class="subRow" id="row${newKey}">
                <input type="hidden" name="sale_item_id[]" value="0">
                <td><a class="btn btn-danger btn-flat" onclick="removeRow(${newKey})"><i class="fa fa-minus"></i></a></td>
                <td>
                    <select name="category_id[]" id="category_id${newKey}" class="form-control select2" onchange="onChangeCategory(this, ${newKey})" required>${categoryOptions}</select>
                </td>
                <td class="product">
                    <select name="product_id[]" id="product_id${newKey}" class="form-control select2" onchange="onChangeProduct(this, ${newKey})" required>${productOptions}</select>
                </td>
                <td class="unit">
                    <select name="unit_id[]" class="form-control select2" onchange="onChangeUnitPrice(${newKey})" required></select>
                </td>               
                <td>
                    <input type="hidden" id="stock${newKey}">
                    <input type="number" step="any" min="1" class="form-control" name="quantity[]" id="quantity${newKey}" onclick="chkItemPrice(${newKey})" onkeyup="chkItemPrice(${newKey})" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control unit_price" name="unit_price[]" id="unit_price${newKey}" onclick="chkItemPrice(${newKey})" onkeyup="chkItemPrice(${newKey})" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control discount_percentage" name="discount_percentage[]" id="discount_percentage${newKey}" onkeyup="saleDiscountCal(${newKey}, 'percent')" onclick="saleDiscountCal(${newKey}, 'percent')" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control discount_amount" name="discount_amount[]" id="discount_amount${newKey}" onkeyup="saleDiscountCal(${newKey}, 'amount')" onclick="saleDiscountCal(${newKey}, 'amount')" required>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control" name="amount[]" id="amount${newKey}" readonly>
                </td>
            </tr>`;
            $('#responseHtml').append(html);
            $('#category_id' + newKey).val('');
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

        function onChangeCategory(e, key) {
            var selectedOption = e.options[e.selectedIndex];

            $.ajax({
                url: "{{ url('sale/products-by-category') }}/" + e.value,
                type: "GET",
                success: function(response) {
                    console.log(response.products);
                    var productSelect = document.getElementById('product_id' + key);
                    productSelect.innerHTML = '<option value="">Select Product</option>';
                    response.products.forEach(function(product) {
                        var option = document.createElement('option');
                        option.value = product.id;
                        option.text = product.name + ' (' + product.code + ')';
                        option.setAttribute('data-product-units', JSON.stringify(product.base_unit
                            .units));
                        option.setAttribute('data-price', product.purchase_price);
                        option.setAttribute('data-stock', product.stockQty);
                        productSelect.appendChild(option);
                    });
                },
                error: function(response) {
                    alert(response.error);
                }
            });
        }

        function onChangeProduct(e, key) {
            
            var customer = $('#customer_id').val();
            var product = $('#product_id' + key).val();

            $.ajax({
                url: "{{ route('sale.customer.last.discount') }}",
                type: "GET",
                data: {
                    product_id: product,
                    customer_id: customer,
                },
                success: function(response) {
                    if (response.success) {
                        $('#discount_percentage' + key).val(response.data.discount_percentage);
                        $('#discount_amount' + key).val(response.data.discount_amount);
                    } else {
                        $('#discount_percentage' + key).val(e.options[e.selectedIndex].dataset
                            .discount_percentage);
                    }
                },
                error: function(response) {
                    alert(response.error);
                }
            });

            var productUnits = JSON.parse(e.options[e.selectedIndex].dataset.productUnits || '[]');
            var unitSelect = e.closest('tr').querySelector('td.unit select');
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            productUnits.forEach(function(unit) {
                var option = document.createElement('option');
                option.value = unit.id;
                option.text = unit.name;
                unitSelect.appendChild(option);
            });

            $('#stock' + key).val(e.options[e.selectedIndex].dataset.stock);

            var product_id = $('#product_id' + key).val();
            var rowId = $(".subRow").length;
            var productOptions = $('#product_id' + key).html();
            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#product_id' + x).val() == product_id) {
                        $('#product_id' + key).html(productOptions);
                        alerts('This Product Already Entered In This Purchase.');
                        $('#mrp' + key).closest('div').hide();
                        $('#unit_price' + key).closest('td').hide();
                        $('#discount_percentage' + key).closest('td').hide();
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
            var discount_percentage = Number($('#discount_percentage' + key).val());
            let discount_amount = ((((unit_price * quantity) * discount_percentage) / 100));
            $('#discount_amount' + key).val(discount_amount.toFixed(2));
            var amount = (quantity * unit_price) - discount_amount;
            $('#amount' + key).val(amount.toFixed(2));

            checkStock(key);
            totalCal();
        }

        function checkStock(key) {
            var quantity = Number($('#quantity' + key).val());

            var productSelect = document.getElementById('row' + key).closest('tr').querySelector('td.product select');
            var productUnits = JSON.parse(productSelect.options[productSelect.selectedIndex].dataset.productUnits || '[]');
            var unitSelect = document.getElementById('row' + key).closest('tr').querySelector('td.unit select');
            var unit = productUnits[unitSelect.selectedIndex - 1];

            var stock = Number($('#stock' + key).val()) / unit.quantity;



            if (stock < quantity) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Stock quantity not exist! Your stock quantity is ' + stock + '.');
            }
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

            var flat_discount_percentage = Number($('#flat_discount_percentage').val());
            var flat_discount_amount = Number($('#flat_discount_amount').val());
            flat_discount_amount = (((subTotal * flat_discount_percentage) / 100));
            $('#flat_discount_amount').val(flat_discount_amount.toFixed(2))
            var total = (subTotal - flat_discount_amount);
            $('#total_amount').val(total.toFixed(2));

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

        function saleDiscountCal(key, type) {
            var unit_price = Number($('#unit_price' + key).val());
            var quantity = Number($('#quantity' + key).val());
            var discount_percentage = Number($('#discount_percentage' + key).val());
            var discount_amount = Number($('#discount_amount' + key).val());
            let total_amount = $('#amount' + key).val();
            if (type == 'percent') {
                if (discount_percentage > 99) {
                    $('#discount_percentage' + key).val(0);
                    $('#discount_amount' + key).val(0);
                    alert('Discount percentage will be less than 8!');
                    return false;
                }
                let amount = ((((unit_price * quantity) * discount_percentage) / 100));
                $('#discount_amount' + key).val(amount.toFixed(2));
            } else {
                if (discount_amount > total_amount) {
                    $('#discount_amount' + key).val(0);
                    $('#discount_percentage' + key).val(0);
                    alert('Discount amount will be less than Price!');
                    return false;
                }
                let percentage = ((100 * discount_amount) / (unit_price * quantity));
                $('#discount_percentage' + key).val(percentage.toFixed(2));
            }
            var d_amount = Number($('#discount_amount' + key).val());
            total_amount = ((unit_price * quantity) - d_amount);
            $('#amount' + key).val(total_amount.toFixed(2));
            totalCal();
        }

        function saleTotalDiscountCal(type) {
            var flat_discount_percentage = Number($('#flat_discount_percentage').val());
            var flat_discount_amount = Number($('#flat_discount_amount').val());
            var subtotal_amount = Number($('#subtotal_amount').val());
            if (type == 'percent') {
                if (flat_discount_percentage > 99) {
                    $('#flat_discount_percentage').val(0);
                    $('#flat_discount_amount').val(0);
                    alert('Discount percentage will be less than 8!');
                    return false;
                }
                let amount = (((subtotal_amount * flat_discount_percentage) / 100));
                $('#flat_discount_amount').val(amount.toFixed(2));
            } else {
                if (flat_discount_amount > subtotal_amount) {
                    $('#flat_discount_amount').val(0);
                    $('#flat_discount_percentage').val(0);
                    alert('Discount amount will be less than Sub Total Amount!');
                    return false;
                }
                let percentage = ((100 * flat_discount_amount) / subtotal_amount);
                $('#flat_discount_percentage').val(percentage.toFixed(2));
            }
            let total_amount = (subtotal_amount - flat_discount_amount);
            $('#total_amount').val(total_amount.toFixed(2))
        }

    </script>
@endsection