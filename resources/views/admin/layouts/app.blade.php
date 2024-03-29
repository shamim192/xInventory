<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'shamim.me') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/datetimepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    @yield('styles')
</head>

<body class="hold-transition skin-black sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <a href="{{ route('dashboard') }}" class="logo">
                <span class="logo-mini">
                    {{ config('app.name', 'Laravel') }}
                </span>
                <span class="logo-lg">
                    {{ config('app.name', 'Laravel') }}
                </span>
            </a>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    {{-- <span class="sr-only">Toggle navigation</span> --}}

                </a>



                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ asset('assets/images/avatar.png') }}" alt="avatar" class="user-image">
                                <span class="hidden-xs">
                                    {{ Auth::user()->name }}
                                </span>
                            </a>

                            <ul class="dropdown-menu">

                                <li class="user-header">
                                    <img src="{{ asset('assets/images/avatar.png') }}" alt="avatar"
                                        class="img-circle">
                                    <p>
                                        {{ Auth::user()->name }}
                                        <small>
                                            {{ Auth::user()->mobile }}<br>
                                            {{ Auth::user()->email }}
                                        </small>
                                    </p>
                                </li>

                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a class="btn btn-info btn-flat"
                                            href="{{ route('profile') }}">My Account</a>
                                    </div>
                                    <div class="pull-right">
                                        <a class="btn btn-danger btn-flat" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" class="non-validate" action="{{ route('logout') }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu">
                    <li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li
                        class="treeview {{ Request::routeIs('stock.*') || Request::routeIs('stock-return.*') ? 'active menu-open' : '' }}">
                        <a href="#">
                            <i class="fa fa-download"></i>
                            <span>Stocks</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">

                            <li class="{{ Request::routeIs('stock.*') ? 'active' : '' }}">
                                <a href="{{ route('stock.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Stock In </a>
                            </li>

                            <li class="{{ Request::routeIs('stock-return.*') ? 'active' : '' }}">
                                <a href="{{ route('stock-return.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Stock Return </a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="treeview {{ Request::routeIs('sale.*') || Request::routeIs('sale-return.*') ? 'active menu-open' : '' }}">
                        <a href="#">
                            <i class="fa fa-upload"></i>
                            <span>Sales</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">

                            <li class="{{ Request::routeIs('sale.*') ? 'active' : '' }}">
                                <a href="{{ route('sale.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Sale</a>
                            </li>

                            <li class="{{ Request::routeIs('sale-return.*') ? 'active' : '' }}">
                                <a href="{{ route('sale-return.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Sale Return</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="treeview {{ Request::routeIs('expense.*') || Request::routeIs('income.*') || Request::routeIs('supplier-payments.*') || Request::routeIs('customer-payments.*') || Request::routeIs('fund-transfer.*') || Request::routeIs('invest.*') || Request::routeIs('loan-holder-payments.*') ? 'active menu-open' : '' }}">
                        <a href="#">
                            <i class="fa fa-money"></i>
                            <span>Payment</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('invest.*') ? 'active' : '' }}">
                                <a href="{{ route('invest.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Invest
                                </a>
                            </li>
                            <li class="{{ Request::routeIs('customer-payments.*') ? 'active' : '' }}">
                                <a href="{{ route('customer-payments.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>
                                    Customer Payment</a>
                            </li>
                            <li class="{{ Request::routeIs('supplier-payments.*') ? 'active' : '' }}">
                                <a href="{{ route('supplier-payments.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>
                                    Supplier Payment</a>
                            </li>
                            <li class="{{ Request::routeIs('loan-holder-payments.*') ? 'active' : '' }}">
                                <a href="{{ route('loan-holder-payments.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i> Loan Holder Payment</a>
                            </li>
                            <li class="{{ Request::routeIs('fund-transfer.*') ? 'active' : '' }}">
                                <a href="{{ route('fund-transfer.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i> Fund Transfer
                                </a>
                            </li>
                            <li class="{{ Request::routeIs('income.*') ? 'active' : '' }}">
                                <a href="{{ route('income.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i> Income </a>
                            </li>
                            <li class="{{ Request::routeIs('expense.*') ? 'active' : '' }}">
                                <a href="{{ route('expense.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>
                                    Expense </a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="treeview {{ Request::routeIs('user.*') || Request::routeIs('customer.*') || Request::routeIs('supplier.*') || Request::routeIs('investor.*') || Request::routeIs('loan-holder.*') ? 'active menu-open' : '' }}">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span>User</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::routeIs('customer.*') ? 'active' : '' }}">
                                <a href="{{ route('customer.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Customers</a>
                            </li>
                            <li class="{{ Request::routeIs('supplier.*') ? 'active' : '' }}">
                                <a href="{{ route('supplier.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Suppliers</a>
                            </li>
                            <li class="{{ Request::routeIs('investor.*') ? 'active' : '' }}">
                                <a href="{{ route('investor.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Investors</a>
                            </li>
                            <li class="{{ Request::routeIs('loan-holder.*') ? 'active' : '' }}">
                                <a href="{{ route('loan-holder.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Loan Holders</a>
                            </li>
                            <li class="{{ Request::routeIs('user.*') ? 'active' : '' }}">
                                <a href="{{ route('user.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Users</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="treeview {{ Request::routeIs('base-units.*') || Request::routeIs('units.*') || Request::routeIs('categories.*') || Request::routeIs('bank.*') || Request::routeIs('products.*') || Request::routeIs('income-category.*') || Request::routeIs('expense-category.*') ? 'active menu-open' : '' }}">
                        <a href="#">
                            <i class="fa fa-cog"></i>
                            <span>Setting</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">

                            <li class="{{ Request::routeIs('bank.*') ? 'active' : '' }}">
                                <a href="{{ route('bank.index') }}"> <i class="fa fa-circle-o"></i>
                                    Banks </a>
                            </li>
                            <li class="{{ Request::routeIs('base-units.*') ? 'active' : '' }}">
                                <a href="{{ route('base-units.index') }}"> <i class="fa fa-circle-o"></i>
                                    Base Units </a>
                            </li>
                            <li class="{{ Request::routeIs('units.*') ? 'active' : '' }}">
                                <a href="{{ route('units.index') }}"> <i class="fa fa-circle-o"></i>
                                    Units </a>
                            </li>

                            <li class="{{ Request::routeIs('categories.*') ? 'active' : '' }}">
                                <a href="{{ route('categories.index') }}"><i class="fa fa-circle-o"></i>
                                    Categories</a>
                            </li>
                            <li class="{{ Request::routeIs('products.*') ? 'active' : '' }}">
                                <a href="{{ route('products.index') }}"><i class="fa fa-circle-o"></i> Products</a>
                            </li>
                            <li class="{{ Request::routeIs('income-category.*') ? 'active' : '' }}">
                                <a href="{{ route('income-category.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Income Heads</a>
                            </li>
                            <li class="{{ Request::routeIs('expense-category.*') ? 'active' : '' }}">
                                <a href="{{ route('expense-category.index') }}"><i class="fa fa-circle-thin"
                                        aria-hidden="true"></i>Expense Heads</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </section>
        </aside>
        <div class="content-wrapper">
            @if (session('successMessage'))
                <section class="content-header">
                    <div class="alert alert-success text-center" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        {{ session('successMessage') }} <br> <button type="button" class="btn btn-warning"
                            data-dismiss="alert" aria-label="Close"><span aria-hidden="true">Ok</span></button>
                    </div>
                </section>
            @endif

            @if (session('errorMessage'))
                <section class="content-header">
                    <div class="alert alert-danger text-center" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        {{ session('errorMessage') }} <br> <button type="button" class="btn btn-warning"
                            data-dismiss="alert" aria-label="Close"><span aria-hidden="true">Ok</span></button>
                    </div>
                </section>
            @endif

            @yield('content')
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                Developed by <a href="#" target="_blank">Md. Shamim Hossain</a>
            </div>
            <strong>
                Copyright &copy; {{ date('Y') }} {{ config('app.name', 'shamim.me') }}.
            </strong> All rights reserved
        </footer>
    </div>

    <script>
        var base_url = '{{ url('') }}';
    </script>
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datetimepicker/datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    @yield('scripts')
</body>

</html>
