@extends('backend.layout')


@section('content')
    <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Inquiry</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('backend') }}">Home</a></li>
                <li class="breadcrumb-item">Inquiry</li>
            </ol>
        </div>


        <div class="row">
            <div class="col-lg-12 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Inquiry</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('backend/inquiry') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Name</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{ isset($filters['name']) ? $filters['name'] : '' }}">
                                </div>
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Account Number</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Account Number" name="account_number" value="{{ isset($filters['account_number']) ? $filters['account_number'] : '' }}">
                                </div>
                                <div class="col-lg-3 input-group">
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
                                            <th scope="col">Name by Input</th>
                                            <th scope="col">Name by Server</th>
                                            <th scope="col">Account Number</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Bank Code</th>
                                            <th scope="col">Bank City</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @if(count($inquiries) > 0)
                                            @foreach($inquiries as $inquiry)
                                                <tr>
                                                    <th scope="row">{{ $i }}</th>
                                                    <td>{{ $inquiry->name_by_input }}</td>
                                                    <td>{{ $inquiry->name_by_server }}</td>
                                                    <td>{{ $inquiry->account_number }}</td>
                                                    <td>{!! $inquiry->status_label !!}</td>
                                                    <td>{{ $inquiry->bank_code }}</td>
                                                    <td>{{ $inquiry->bank_city_text }}</td>
                                                </tr>
                                                @php
                                                    $i+=1;
                                                @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <th colspan="8">Not Found</th>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                    <div class="text-center">
                                        @if(isset($inquiries))
                                            {!! $inquiries->appends(Input::except('page'))->links() !!}
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
