@extends('backend.layout')


@section('content')
    <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">User</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('backend') }}">Home</a></li>
                <li class="breadcrumb-item">User</li>
            </ol>
        </div>


        <div class="row">
            <div class="col-lg-12 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">User</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('backend/user') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Name</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{ isset($filters['name']) ? $filters['name'] : '' }}">
                                </div>
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Email</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Email" name="email" value="{{ isset($filters['email']) ? $filters['email'] : '' }}">
                                </div>
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Company Name</span>
                                    </div>
                                    <select class="form-control" name="company_name">
                                        <option value="" {{ (isset($filters['company_name']) AND $filters['company_name'] == ''  ) }}>All</option>
                                        @foreach($userCompanies as $userCompany)
                                            <option value="{{ $userCompany->id }}" {{ (isset($filters['company_name']) AND ($filters['company_name'] == $userCompany->id)) ? 'selected' : '' }}>
                                                {{ $userCompany->title }}
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
                                            <th scope="col">Company Name</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Level</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @if(count($users) > 0)
                                            @foreach($users as $user)
                                                <tr>
                                                    <th scope="row">{{ $i }}</th>
                                                    <td>
                                                        <a href="{{ url('backend/user/detail/' . $user->user_company->id) }}">{{ $user->user_company->title }}</a>
                                                    </td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->phone }}</td>
                                                    <td>{{ $user->level }}</td>
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
                                    <div class="text-center" style="display: flex; justify-content: center;">
                                        @if(isset($users))
                                            {{ $users->links('pagination::bootstrap-4') }}
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
