@extends('backend.layout')


@section('content')

    <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">User Detail</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('backend') }}">Home</a></li>
                <li class="breadcrumb-item">User Company</li>
            </ol>
        </div>


        <div class="row">
            <div class="col-lg-6 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">User Company Detail</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <tbody>
                                        <tr>
                                            <th>Title</th>
                                            <td>{{ $userCompany->title }}</td>
                                        </tr>
                                        <tr>
                                            <th>Package Name</th>
                                            <td>{{ $userCompany->package_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Expired At</th>
                                            <td>
                                                @if($userCompany->expired_at)
                                                    {{ date("d-M-Y H:i:s", strtotime($userCompany->expired_at)) }}
                                                @else
                                                    Not Paid
                                                @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Users</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <thead>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        </thead>
                                        <tbody>
                                        @if(count($userCompany->users) > 0)
                                            @foreach($userCompany->users as $user)
                                                <tr>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->level }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3">
                                                    Not Found | <a href="{{ url('') }}" class="btn btn-xs btn-primary">Paid Manual</a>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">History User Member Orders</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <thead>
                                        <th>Invoice Number</th>
                                        <th>Email</th>
                                        <th>Package Name</th>
                                        <th>Package Price</th>
                                        <th>Long Expired</th>
                                        <th>Status</th>
                                        </thead>
                                        <tbody>
                                        @if(count($userCompany->user_member_orders) > 0)
                                            @foreach($userCompany->user_member_orders as $userMemberOrder)
                                                <tr>
                                                    <td>{{ $userMemberOrder->invoice_number }}</td>
                                                    <td>{{ $userMemberOrder->email }}</td>
                                                    <td>{{ $userMemberOrder->package_name }}</td>
                                                    <td>Rp. {{ number_format($userMemberOrder->package_price, 0) }}</td>
                                                    <td>{{ $userMemberOrder->long_expired }}</td>
                                                    <td>
                                                        @if($userMemberOrder->paid_at)
                                                            {{ date('d-M-Y H:i:s', strtotime($userMemberOrder->paid_at)) }}, by {{ $userMemberOrder->paid_with }}
                                                        @else
                                                            Not Paid
                                                            <a href="{{ url('backend/user/paid-manually/' . $userMemberOrder->id) }}" class="btn btn-xs btn-warning"
                                                               onclick="return confirm('Are you sure to paid manually this member order? ')">
                                                                Manually Paid
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6">Not Found</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
