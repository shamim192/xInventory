@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('stock.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Stock List
                    </a>
                </li>
                @can('create stock_in')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('stock.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Stock
                        </a>
                    </li>
                @endcan
                @can('edit stock_in')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Stock
                            </a>
                        </li>
                    @endif
                @endcan
                @can('show stock_in')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Stock {{ __('lang.Details') }}
                            </a>
                        </li>
                    @endif
                @endcan
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:120px;">Supplier</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->supplier != null ? $data->supplier->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->date) }}</td>
                                </tr>
                                <tr>
                                    <th>Challan Number</th>
                                    <th>:</th>
                                    <td>{{ $data->challan_number }}</td>
                                </tr>
                                <tr>
                                    <th>Challan Image</th>
                                    <th>:</th>
                                    <td>{!! viewImg('stocks', $data->challan_image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}</td>
                                </tr>
                                <tr>
                                    <th>Challan Date</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->challan_date) }}</td>
                                </tr>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Items</th>
                                        <th>Unit</th>
                                        <th style="text-align: right;">Quantity</th>
                                        <th style="text-align: right;">Unit Price</th>
                                        <th style="text-align: right;">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->items as $key => $item)
                                        <tr>
                                            <td>{{ $item->category != null ? $item->category->name : '-' }}</td>
                                            <td>{{ $item->product != null ? $item->product->name . ' (' . $item->product->code . ')' . ' (' . $item->quantity . ')' : '-' }}
                                            </td>
                                            <td>{{ $item->unit != null ? $item->unit->name : '-' }}</td>
                                            <td style="text-align: right;">{{ $item->quantity }}</td>
                                            <td style="text-align: right;">{{ $item->unit_price }}</td>
                                            <td style="text-align: right;">{{ $item->amount }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;" colspan="3">Total Quantity :</th>
                                        <th style="text-align: right;">{{ $data->items->sum('quantity') }}</th>
                                        <th style="text-align: right;">Sub Total Amount:</th>
                                        <th style="text-align: right;">{{ numberFormat($data->items->sum('amount')) }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="5"><strong>Discount Amount :</strong>
                                        </th>
                                        <th style="text-align: right;">{{ $data->discount_amount }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="5"><strong>Total Amount :</strong></th>
                                        <th style="text-align: right;">{{ $data->total_amount }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="5"><strong>Paid Amount :</strong></th>
                                        <th style="text-align: right;">{{ $data->paid_amount }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('stock.update', $edit) : route('stock.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Supplier :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="supplier_id" required>
                                                    <option value="">Select Supplier</option>
                                                    @php($supplier_id = old('supplier_id', isset($data) ? $data->supplier_id : ''))
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ $supplier_id == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('supplier_id'))
                                                    <span class="help-block">{{ $errors->first('supplier_id') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}"
                                                    required>

                                                @if ($errors->has('date'))
                                                    <span class="help-block">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('challan_number') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Challan Number :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="challan_number"
                                                    value="{{ old('challan_number', isset($data) ? $data->challan_number : '') }}">

                                                @if ($errors->has('challan_number'))
                                                    <span class="help-block">{{ $errors->first('challan_number') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('challan_image') ? ' has-error' : '' }}">
                                            <label
                                                class="control-label col-sm-3 {{ isset($edit) ? '' : 'nullable' }}">Challan
                                                Image:</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="challan_image"
                                                    {{ isset($edit) ? '' : 'nullable' }}>
                                                    @if (isset($edit))
                                                    @if ($data->challan_image)
                                                    {!! viewImg('stocks', $data->challan_image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}
                                                    @endif
                                                @endif
                                                @if ($errors->has('challan_image'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('challan_image') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('challan_date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Challan Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="challan_date"
                                                    value="{{ old('challan_date', isset($data) ? $data->challan_date : '') }}">

                                                @if ($errors->has('challan_date'))
                                                    <span class="help-block">{{ $errors->first('challan_date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
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

                                <div class="form-group">
                                    <div class="text-center">
                                        <button type="submit"
                                            class="btn btn-success btn-flat">{{ isset($edit) ? __('lang.Update') : __('lang.Create') }}</button>
                                        <button type="reset"
                                            class="btn btn-warning btn-flat">{{ __('lang.Clear') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('stock.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="from" id="datepickerFrom"
                                            value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="to" id="datepickerTo"
                                            value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                                    </div>

                                    <div class="form-group">
                                        <select class="form-control" name="supplier">
                                            <option value="">Any Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ Request::get('supplier') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" class="form-control" name="q"
                                            value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn-info btn-flat">{{ __('lang.Search') }}</button>
                                        <a class="btn btn-warning btn-flat" href="{{ route('stock.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Category</th>
                                        <th>Items</th>
                                        <th>Date</th>
                                        <th>Challan Number</th>
                                        <th>Challan Date</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Amount</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stocks as $val)
                                        <tr>
                                            <td>{{ isset($val->supplier) ? $val->supplier->name : '' }}</td>
                                            <td>
                                                @foreach ($val->items as $item)
                                                    {{ $item->category != null ? $item->category->name : '-' }}<br>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($val->items as $item)
                                                    {{ $item->product != null ? $item->product->name . ' (' . $item->product->code . ')' .' (' . $item->quantity . ')' : '-' }}<br>
                                                @endforeach
                                            </td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->challan_number }}</td>
                                            <td>{{ dateFormat($val->challan_date) }}</td>
                                            <td class="text-right">{{ $val->total_quantity }} </td>
                                            <td class="text-right">{{ numberFormat($val->total_amount) }} </td>
                                            <td>
                                                @canany([
                                                    'show stock_in',
                                                    'print stock_in',
                                                    'edit stock_in',
                                                    'delete
                                                    stock_in',
                                                    ])
                                                    <div class="dropdown">
                                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                            type="button" data-toggle="dropdown">Action <span
                                                                class="caret"></span></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            @can('show stock_in')
                                                                <li><a href="{{ route('stock.show', $val->id) . qString() }}"><i
                                                                            class="fa fa-eye"></i> Show</a></li>
                                                            @endcan
                                                            {{-- @can('print stock_in')
                                                                <li><a href="{{ route('stock.print', $val->id) . qString() }}"><i
                                                                            class="fa fa-print"></i> Print</a></li>
                                                            @endcan --}}
                                                            @can('edit stock_in')
                                                                <li><a href="{{ route('stock.edit', $val->id) . qString() }}"><i
                                                                            class="fa fa-pencil"></i> Edit</a></li>
                                                            @endcan
                                                            @can('delete stock_in')
                                                                <li><a
                                                                        onclick="deleted('{{ route('stock.destroy', $val->id) . qString() }}')"><i
                                                                            class="fa fa-close"></i> Delete</a></li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                                @endcanany
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($stocks) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $stocks->appends(Request::except('page'))->links() }}
                            </div>

                            <div class="col-sm-4">
                                <div class="pagi-limit-box">
                                    <div class="input-group pagi-limit-box-body">
                                        <span class="input-group-addon">Show:</span>

                                        <select class="form-control pagi-limit" name="limit">
                                            @foreach (paginations() as $pag)
                                                <option value="{{ qUrl(['limit' => $pag]) }}"
                                                    {{ $pag == Request::get('limit') ? 'selected' : '' }}>
                                                    {{ $pag }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

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
