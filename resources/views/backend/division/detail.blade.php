@extends('backend.layout')


@section('content')

    <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Division Detail</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('backend') }}">Home</a></li>
                <li class="breadcrumb-item">Division Detail</li>
            </ol>
        </div>


        <div class="row">
            <div class="col-lg-6 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Division Detail</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th>Title</th>
                                    <td>{{ $division->title }}</td>
                                </tr>
                                <tr>
                                    <th>Director Email</th>
                                    <td>{{ $division->director_email }}</td>
                                </tr>
                                <tr>
                                    <th>Director Phone</th>
                                    <td>{{ $division->director_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Flip Name</th>
                                    <td>{{ $division->division_flip->flip_name }}</td>
                                </tr>
                                <tr>
                                    <th>Flip Key</th>
                                    <td>*************************</td>
                                </tr>
                                <tr>
                                    <th>Flip Token</th>
                                    <td>*************************</td>
                                </tr>
                                @if($division->division_journal)
                                    <tr>
                                        <th>Journal Name</th>
                                        <td>{{ $division->division_journal->journal_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Journal Key</th>
                                        <td>*************************</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
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
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                </thead>
                                <tbody>
                                @foreach($divisionUsers as $divisionUser)
                                    <tr>
                                        <td>{{ $divisionUser->user->name }}</td>
                                        <td>{{ $divisionUser->user->email }}</td>
                                        <td>{{ $divisionUser->role }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Last Transaction</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Remark</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">#</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @if(count($transactions) > 0)
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <th scope="row">{{ $i }}</th>
                                            <td>{{ $transaction->title }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>{{ $transaction->remark }}</td>
                                            <td><span class="label label-warning">{!! $transaction->status_label !!}</span></td>
                                            <td><a href="{{ url('backend/transaction/detail/' . $transaction->id) }}" class="btn btn-primary">Detail</a></td>
                                        </tr>
                                        @php
                                            $i+=1;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="5">
                                            <a href="{{ url('backend/transaction?division_name=' . $division->id) }}" class="btn btn-primary">Show All</a>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <th colspan="5">Not Found</th>
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
@endsection
