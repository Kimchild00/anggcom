@extends('backend.layout')


@section('content')
    <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">User Company</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('backend') }}">Home</a></li>
                <li class="breadcrumb-item">User Company</li>
            </ol>
        </div>


        <div class="row">
            <div class="col-lg-12 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">User Company</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('backend/user-company') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Title</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Title" name="title" value="{{ isset($filters['title']) ? $filters['title'] : '' }}">
                                </div>
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Package Name</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Package Name" name="package_name" value="{{ isset($filters['package_name']) ? $filters['package_name'] : '' }}">
                                </div>
                                <div class="col-lg-6" style="text-align: right">
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
                                            <th scope="col">Title</th>
                                            <th scope="col">Package name</th>
                                            <th scope="col">Expired At</th>
                                            <th scope="col">Expired By</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @if(count($userCompanies) > 0)
                                            @foreach($userCompanies as $userCompany)
                                                <tr>
                                                    <th scope="row">{{ $i }}</th>
                                                    <td>
                                                        <a href="{{ url('backend/user/detail/' . $userCompany->id) }}">{{ $userCompany->title }}</a>
                                                    </td>
                                                    <td>{{ $userCompany->package_name }}</td>
                                                    <td>
                                                        @if($userCompany->expired_at)
                                                            {{ date("d-M-Y H:i:s", strtotime($userCompany->expired_at)) }}
                                                        @else
                                                            Not Paid
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($userCompany->user_member_order_active)
                                                            {{ $userCompany->user_member_order_active->paid_with }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php
                                                    $i+=1;
                                                @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <th colspan="4">Not Found</th>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                    <div class="text-center">
                                        @if(isset($userCompanies))
                                            {!! $userCompanies->appends(Input::except('page'))->links() !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
