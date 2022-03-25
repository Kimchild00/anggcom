@extends('backend.layout')


@section('content')
    <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Transaction</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('backend') }}">Home</a></li>
                <li class="breadcrumb-item">Transaction</li>
            </ol>
        </div>


        <div class="row">
            <div class="col-lg-6 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Transaction Detail</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th>Last Status</th>
                                            <td><span class="label label-warning">{!! $transaction->status_label !!}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Title</th>
                                            <td>{{ $transaction->title }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>{{ $transaction->description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Amount</th>
                                            <td>Rp. {{ number_format($transaction->amount, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Remark</th>
                                            <td>{{ $transaction->remark }}</td>
                                        </tr>
                                        <tr>
                                            <th>Chart of Account (Journal)</th>
                                            <td>{{ $transaction->ott_name }} | {{ $transaction->ott_code }}</td>
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
                        <h6 class="m-0 font-weight-bold text-primary">Status History</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <thead>
                                        <th>Title</th>
                                        <th>User Name</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        </thead>
                                        <tbody>
                                        @foreach($transaction->transaction_statuses as $status)
                                            <tr>
                                                <td>{!! $status->status_label !!}</td>
                                                <td>{{ $status->user->name }}</td>
                                                <td>{{ $status->message }}</td>
                                                <td>{{ date("d-M-Y H:i:s", strtotime($status->created_at)) }}</td>
                                            </tr>
                                        @endforeach
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
                        <h6 class="m-0 font-weight-bold text-primary">Division Detail</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <tbody>
                                        @if($transaction->division)
                                            <tr>
                                                <th>Division Title</th>
                                                <td>
                                                    <a href="{{ url('backend/division/detail/' . $transaction->division->id ) }}" class="btn btn-primary">
                                                        {{ $transaction->division->title }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Director Email</th>
                                                <td>{{ $transaction->division->director_email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Director Phone</th>
                                                <td>{{ $transaction->division->director_phone }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="2">Not found division</td>
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
