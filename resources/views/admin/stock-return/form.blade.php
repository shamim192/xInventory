<div class="form-group">
    <label class="required">Supplier :</label>
        <select class="form-control @error('supplier_id') is-invalid @enderror select2" name="supplier_id" id="supplier_id" required
            onchange="getStockIn()">
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
    <label class="required">Stock In :</label>
        <select class="form-control @error('stock_id') is-invalid @enderror select2" name="stock_id" id="stock_id"
            onchange="stockItems()" required>
            <option value="">Select Stock In</option>
            @if (isset($stocks))
                @php($stock_id = old('stock_id', isset($data) ? $data->stock_id : ''))
                @foreach ($stocks as $stock)
                    <option value="{{ $stock->id }}"
                        {{ $stock_id == $stock->id ? 'selected' : '' }}>
                        {{ $stock->challan_number ?? 'N/A' }} /
                        {{ $stock->date }}</option>
                @endforeach
            @endif
        </select>

        @error('stock_id')
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

<div class="box-body table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Category</th>
                <th>Product</th>
                <th>Unit</th>
                <th>U.P.</th>
                <th>Stockin Qty</th>
                <th>Returned Qty</th>
                <th>Rem. Qty</th>
                <th class="required">Returning Qty</th>
            </tr>
        </thead>
        <tbody id="responseHtml">
            @foreach ($returnItems as $i => $item)
                <tr>
                    <td style="width: 20%;">
                        <input type="hidden" name="category_id[]" value="{{ $item->category_id }}">
                        <input type="text" class="form-control" value="{{ $item->category->name }}" readonly>
                    </td>
                    <td>
                        <input type="hidden" name="stock_return_item_id[]" value="{{ $item->id }}">
                        <input type="hidden" name="stock_item_id[]" value="{{ $item->stock_item_id }}">
                        <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                        <input type="hidden" name="unit_id[]" value="{{ $item->unit_id }}">
                        <input type="text" class="form-control"
                            value="{{ $item->product->name }} @if ($item->product->code) ({{ $item->product->code }}) @endif"
                            readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control" value="{{ $item->unit->name }}" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="unit_price[]" value="{{ $item->unit_price }}"
                            readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control" value="{{ $item->stock_quantity }}" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control" value="{{ $item->returned_quantity }}" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="remain_quantity[]"
                            id="remain_quantity{{ $i }}" value="{{ $item->remain_quantity }}" readonly>
                    </td>
                    <td>
                        <input type="number" step="any" min="1" class="form-control" name="quantity[]"
                            id="quantity{{ $i }}" onkeyup="chkQty({{ $i }})"
                            value="{{ $item->quantity }}">
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right"><strong>Total Quantity :</strong>
                </td>
                <td class="text-right">
                    <input type="text" class="form-control" readonly id="total_quantity"
                        value="{{ isset($edit) ? $returnItems->sum('quantity') : '' }}">
                </td>
            </tr>
        </tfoot>
    </table>
</div>

@section('scripts')
    <script>
        function getStockIn() {
            var supplierId = $('#supplier_id').val();
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: "{{ route('supplier-wise-stock-ajax') }}",
                data: {
                    'supplier_id': supplierId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    var options = '<option value="">Select Stock In</option>';
                    if (res.status) {
                        res.stocks.forEach((item, i) => {
                            options += '<option value="' + item.id + '">' + item.challan_number +
                                ' / ' + item.date + '</option>';
                        });
                    }
                    $('#stock_id').html(options);
                }
            });
        }

        function stockItems() {
            var stockId = $('#stock_id').val();
            $.ajax({
                type: "POST",
                url: "{{ route('stock-item-ajax') }}",
                data: {
                    'stock_id': stockId,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    var html = '';
                    if (res.status) {
                        res.items.forEach((item, i) => {
                            html += `<tr>
                                <td style="width: 20%;">                                   
                                    <input type="hidden" name="category_id[]" value="${item.category_id}">                                    
                                    <input type="text" class="form-control" value="${item.category.name}" readonly>
                                </td>
                                <td>
                                    <input type="hidden" name="stock_return_item_id[]" value="${item.id}">
                                    <input type="hidden" name="stock_item_id[]" value="${item.stock_item_id}">
                                    <input type="hidden" name="product_id[]" value="${item.product_id}">
                                    <input type="hidden" name="unit_id[]" value="${item.unit_id}">
                                    <input type="text" class="form-control" value="${item.product.name} (${item.product.code})" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" value="${item.unit.name}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="unit_price[]" value="${item.unit_price}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" value="${item.stock_quantity}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" value="${item.returned_quantity}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="remain_quantity[]" id="remain_quantity${i}" value="${item.remain_quantity}" readonly>
                                </td>
                                <td>
                                    <input type="number" step="any" min="1" class="form-control" name="quantity[]" id="quantity${i}" onkeyUp="chkQty(${i})">
                                </td>
                            </tr>`;
                        });
                    }
                    $('#responseHtml').html(html);
                }
            });
        }

        function chkQty(key) {
            var remQty = Number($('#remain_quantity' + key).val());

            var quantity = Number($('#quantity' + key).val());
            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Please Provide Valid Price!');
            }

            if (quantity > remQty) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('You are not allowed to input greater than remaining quantity!');
            }          
            var totalQuantity = 0;
            $("input[id^='quantity']").each(function() {
                totalQuantity += +$(this).val();
            });
            $('#total_quantity').val(totalQuantity);
        }
    </script>
@endsection
