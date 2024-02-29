@extends('layouts.admin')
<style>
     .unit .select2-selection.select2-selection--single {
        min-width: 50px !important;
    }
</style>
@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('sale.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Sale {{ __('lang.List') }}
                    </a>
                </li>
                @can('create sale')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('sale.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> {{ __('lang.Add') }} Sale
                        </a>
                    </li>
                @endcan

                @can('edit sale')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> {{ __('lang.Edit') }} Sale
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show sale')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Sale {{ __('lang.Details') }}
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
                                    <th style="width:120px;">Invoice Number</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{  $data->invoice_no }}</td>
                                </tr>
                                <tr>
                                    <th style="width:120px;">Customer</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->customer != null ? $data->customer->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->date) }}</td>
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
                                        <th style="text-align: right;">Discount Percentage</th>
                                        <th style="text-align: right;">Discount Amount</th>
                                        <th style="text-align: right;">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->items as $key => $item)
                                        <tr>

                                            <td>{{ $item->category != null ? $item->category->name : '-' }}</td>
                                            <td>{{ $item->product != null ? $item->product->name . ' (' . $item->product->code . ')' . ' (' . $item->quantity . ')' : '-' }}
                                            <td>{{ optional($item->unit)->name }}</td>
                                            <td style="text-align: right;">{{ $item->quantity }}</td>
                                            <td style="text-align: right;">{{ $item->unit_price }}</td>
                                            <td style="text-align: right;">{{ $item->discount_percentage }}</td>
                                            <td style="text-align: right;">{{ $item->discount_amount }}</td>
                                            <td style="text-align: right;">{{ $item->amount }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;" colspan="3">Total Quantity :</th>
                                        <th style="text-align: right;">{{ $data->items->sum('quantity') }}</th>
                                        <th style="text-align: right;" colspan="3">Sub Total Amount:</th>
                                        <th style="text-align: right;">{{ numberFormat($data->items->sum('amount')) }}</th>
                                    </tr>

                                    <tr>
                                        <th style="text-align: right;" colspan="7"><strong>Flat Discount Amount
                                                :</strong>
                                        </th>
                                        <th style="text-align: right;">{{ $data->flat_discount_amount }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="7"><strong>Total Amount :</strong></th>
                                        <th style="text-align: right;">{{ $data->total_amount }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="7"><strong>Paid Amount :</strong></th>
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
                                action="{{ isset($edit) ? route('sale.update', $edit) : route('sale.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Customer :</label>
                                            <div class="col-sm-8 input-group">
                                                <select class="form-control select2" name="customer_id" id="customer_id"
                                                    required>
                                                    <option value="">Select Customer</option>
                                                    @php($customer_id = old('customer_id', isset($data) ? $data->customer_id : ''))
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}"
                                                            {{ $customer_id == $customer->id ? 'selected' : '' }}>
                                                            {{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="input-group-addon" data-toggle="modal"
                                                    data-target="#customerModal"><i class="fa fa-plus"></i></span>
                                                @if ($errors->has('customer_id'))
                                                    <span class="help-block">{{ $errors->first('customer_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Transport :</label>
                                            <div class="col-sm-8 input-group">
                                                <select class="form-control select2" name="transport_id" id="transport_id">
                                                    <option value="">Select Transport</option>
                                                    @php($transport_id = old('transport_id', isset($data) ? $data->transport_id : ''))
                                                    @foreach ($transports as $transport)
                                                        <option value="{{ $transport->id }}"
                                                            {{ $transport_id == $transport->id ? 'selected' : '' }}>
                                                            {{ $transport->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('transport_id'))
                                                    <span class="help-block">{{ $errors->first('transport_id') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-8 input-group">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}"
                                                    required>

                                                @if ($errors->has('date'))
                                                    <span class="help-block">{{ $errors->first('date') }}</span>
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
                                                    <input type="hidden" name="sale_item_id[]"
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
                                                                    data-stock="{{ $product->stockQty }}"
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
                                                        value="{{ isset($edit) ? ($item->product->getStock($item->product_id) + $item->actual_quantity) : 0 }}">
                                                        <input type="number" step="any" min="1"
                                                            class="form-control" name="quantity[]"
                                                            id="quantity{{ $key }}"
                                                            value="{{ $item->quantity }}"
                                                            onclick="chkItemPrice({{ $key }})"
                                                            onkeyup="chkItemPrice({{ $key }})" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" min="0"
                                                            class="form-control" name="unit_price[]"
                                                            id="unit_price{{ $key }}"
                                                            value="{{ $item->unit_price }}"
                                                            onclick="chkItemPrice({{ $key }})"
                                                            onkeyup="chkItemPrice({{ $key }})" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" min="0"
                                                            class="form-control" name="discount_percentage[]"
                                                            id="discount_percentage{{ $key }}"
                                                            value="{{ $item->discount_percentage }}"
                                                            onkeyup="saleDiscountCal({{ $key }}, 'percent')"
                                                            onclick="saleDiscountCal({{ $key }}, 'percent')">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" min="0"
                                                            class="form-control" name="discount_amount[]"
                                                            id="discount_amount{{ $key }}"
                                                            value="{{ $item->discount_amount }}"
                                                            onkeyup="saleDiscountCal({{ $key }}, 'amount')"
                                                            onclick="saleDiscountCal({{ $key }}, 'amount')">
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

                                                <td class="text-right" colspan="3"><strong>Sub Total Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="subtotal_amount" id="subtotal_amount"
                                                        value="{{ isset($edit) ? numberFormat($items->sum('amount')) : '' }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="8"><strong>Flat Dis. Per :</strong>
                                                </td>
                                                <td class="text-right"><input type="number" step="any"
                                                        min="0" class="form-control"
                                                        name="flat_discount_percentage" id="flat_discount_percentage"
                                                        value="{{ old('flat_discount_percentage', isset($data) ? $data->flat_discount_percent : '') }}"
                                                        onkeyup="saleTotalDiscountCal('percent')"
                                                        onclick="saleTotalDiscountCal('percent')"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="8"><strong>Flat Dis. Amt :</strong>
                                                </td>
                                                <td class="text-right"><input type="number" step="any"
                                                        min="0" class="form-control" name="flat_discount_amount"
                                                        id="flat_discount_amount"
                                                        value="{{ old('flat_discount_amount', isset($data) ? $data->flat_discount_amount : '') }}"
                                                        onkeyup="saleTotalDiscountCal('amount')"
                                                        onclick="saleTotalDiscountCal('amount')"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="8"><strong>Total Amount :</strong></td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_amount" id="total_amount"
                                                        value="{{ old('total_amount', isset($data) ? $data->total_amount : '') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="8"><strong>Paid Amount :</strong></td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        name="paid_amount" id="paid_amount"
                                                        value="{{ old('paid_amount', isset($data) ? $data->paid_amount : '') }}"
                                                        onkeyup="chkPaid()"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7"><strong>Bank :</strong></td>
                                                <td class="text-right" colspan="2">
                                                    <select name="bank_id" id="bank_id" class="form-control select2">
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
                                            <tr>
                                                <td class="text-right" colspan="8">
                                                    <strong>Send SMS :</strong>
                                                </td>
                                                <td class="text-left">
                                                    <input type="radio" value="1" name="send_sms">
                                                    Yes
                                                    <input type="radio" value="0" name="send_sms" checked> No
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
                        <form method="GET" action="{{ route('sale.index') }}" class="form-inline">
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
                                        <select class="form-control select2" name="customer">
                                            <option value="">Any Customer</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ Request::get('customer') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}</option>
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
                                        <a class="btn btn-warning btn-flat" href="{{ route('sale.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Invoice Number</th>
                                        <th>Customer</th>
                                        <th>Category</th>
                                        <th>Items</th>
                                        <th>Date</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Amount</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $val)
                                        <tr>
                                            <td>{{ $val->invoice_no }}</td>
                                            <td>{{ optional($val->customer)->name }}</td>
                                            <td>
                                                @foreach ($val->items as $item)
                                                    {{ optional($item->category)->name }}<br>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($val->items as $item)
                                                    {{ optional($item->product)->name . ' (' . optional($item->product)->code . ')' . ' (' . intval($item->quantity) . ')' }}<br>
                                                @endforeach
                                            </td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td class="text-right">{{ $val->total_quantity }} </td>
                                            <td class="text-right">{{ numberFormat($val->total_amount) }} </td>
                                            <td>
                                                @canany(['show sale', 'print sale', 'edit sale', 'delete sale'])
                                                    <div class="dropdown">
                                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                            type="button" data-toggle="dropdown">Action <span
                                                                class="caret"></span></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            @can('show sale')
                                                                <li><a href="{{ route('sale.show', $val->id) . qString() }}"><i
                                                                            class="fa fa-eye"></i> Show</a></li>
                                                            @endcan

                                                            @can('print sale')
                                                                <li><a href="{{ route('sale.print', $val->id) . qString() }}"><i
                                                                            class="fa fa-print"></i> Print</a></li>
                                                            @endcan

                                                            @can('edit sale')
                                                                <li><a href="{{ route('sale.edit', $val->id) . qString() }}"><i
                                                                            class="fa fa-pencil"></i> Edit</a></li>
                                                            @endcan

                                                            @can('delete sale')
                                                                <li><a
                                                                        onclick="deleted('{{ route('sale.destroy', $val->id) . qString() }}')"><i
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
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($sales) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $sales->appends(Request::except('page'))->links() }}
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

    <div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form method="POST" class="form-horizontal non-validate" id="add_customer_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="customerModalLabel">Add Customer</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3 required">Name:</label>
                            <div class="col-sm-8">
                                <input type="text" id="name" class="form-control" name="name"
                                    value="{{ old('name') }}" required>
                                <span class="help-block" id="error_name"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 required">Mobile:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    value="{{ old('mobile') }}" required>
                                <span class="help-block" id="error_mobile"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Address:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="address" name="address"
                                    value="{{ old('address') }}">
                                <span class="help-block" id="error_address"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Date of Birth:</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                    value="{{ old('date_of_birth') }}">
                                <span class="help-block" id="error_date_of_birth"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Shop Name:</label>
                            <div class="col-sm-8">
                                <input type="text" id="shop_name" class="form-control" name="shop_name"
                                    value="{{ old('shop_name') }}">
                                <span class="help-block" id="error_shop_name"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 required">Status:</label>
                            <div class="col-sm-8">
                                <select name="status" class="form-control" required id="status">
                                    @foreach (['Active', 'Inactive'] as $sts)
                                        <option value="{{ $sts }}">{{ $sts }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block" id="error_status"></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="customerSubmit" class="btn btn-success btn-flat">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

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
            if (!customer) {
                alert('Please select customer first!');
                return false;
            }
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
            // var unit = productUnits[unitSelect.selectedIndex];
            // console.log(unit);
            // var price = parseFloat(e.options[e.selectedIndex].dataset.price);
            // var unit_price = price * unit.quantity;
            // $('#unit_price' + key).val(unit_price);

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
            // console.log(productUnits, unit, unitSelect.selectedIndex);

            // var stock = Number($('#stock' + key).val()) / unit.quantity;
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

        $(document).on('submit', '#add_customer_form', function(event) {
            event.preventDefault();
            var formElement = $(this).serializeArray()
            var formData = new FormData();
            formElement.forEach(element => {
                formData.append(element.name, element.value);
            });
            formData.append('_token', "{{ csrf_token() }}");
            resetValidationErrors();
            $.ajax({
                url: "{{ route('sale.new-customer-ajex') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    alert(response.successMessage);
                    resetForm();
                    $('#customerModal').modal('hide');
                },
                error: function(response) {
                    showValidationErrors('#add_customer_form', response.responseJSON.errors);
                }
            });
        });

        function showValidationErrors(formType, errors) {
            $(formType + ' #error_name').text(errors.name);
            $(formType + ' #error_mobile').text(errors.mobile);
            $(formType + ' #error_address').text(errors.address);
            $(formType + ' #error_date_of_birth').text(errors.date_of_birth);
            $(formType + ' #error_shop_name').text(errors.shop_name);
            $(formType + ' #error_status').text(errors.status);
        }

        function resetValidationErrors() {
            $('#error_name').text('');
            $('#error_mobile').text('');
            $('#error_address').text('');
            $('#error_date_of_birth').text('');
            $('#error_shop_name').text('');
            $('#error_status').text('');
        }

        function resetForm() {
            $('#name').val('');
            $('#mobile').val('');
            $('#address').val('');
            $('#date_of_birth').val('');
            $('#shop_name').val('');
            $('#status').val('');
        }
    </script>
@endsection
