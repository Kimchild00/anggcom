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
            <div class="col-lg-12 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Transaction</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('backend/transaction') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Title</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Title" name="title" value="{{ isset($filters['title']) ? $filters['title'] : '' }}">
                                </div>
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Remark</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Remark" name="remark" value="{{ isset($filters['remark']) ? $filters['remark'] : '' }}">
                                </div>
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Division Name</span>
                                    </div>
                                    <select class="form-control" name="division_name">
                                        <option value="" {{ (isset($filters['division_name']) AND $filters['division_name'] == ''  ) }}>All</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}" {{ (isset($filters['division_name']) AND ($filters['division_name'] == $division->id)) ? 'selected' : '' }}>
                                                {{ $division->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3" style="text-align: right">
                                    <button class="btn btn-primary" type="submit">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                        <br>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th>Division Title</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Remark</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
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
                                                    <td>
                                                        <a href="{{ url('backend/division/detail/' . $transaction->division->id) }}">{{ $transaction->division->title }}</a></td>
                                                    <td>{{ $transaction->title }}</td>
                                                    <td>{{ $transaction->description }}</td>
                                                    <td>{{ $transaction->remark }}</td>
                                                    <td><span class="label label-warning">{!! $transaction->status_label !!}</span></td>
                                                    <td>
                                                        <a href="{{ url('backend/transaction/detail/' . $transaction->id) }}" class="btn btn-primary">
                                                            Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                                @php
                                                    $i+=1;
                                                @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <th colspan="5">Not Found</th>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                    <div class="text-center">
                                        @if(isset($transactions))
                                            {!! $transactions->appends(Input::except('page'))->links() !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
