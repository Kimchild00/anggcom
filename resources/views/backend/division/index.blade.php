@extends('backend.layout')


@section('content')
    <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Division</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('backend') }}">Home</a></li>
                <li class="breadcrumb-item">Division</li>
            </ol>
        </div>


        <div class="row">
            <div class="col-lg-12 mb-4">
                <!-- Simple Tables -->
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Division</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('backend/division') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Title</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Title" name="title" value="{{ isset($filters['title']) ? $filters['title'] : '' }}">
                                </div>
                                <div class="col-lg-3 input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Director Email</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Director Email" name="director_email" value="{{ isset($filters['director_email']) ? $filters['director_email'] : '' }}">
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
                                            <th scope="col">Director Email</th>
                                            <th scope="col">Director Phone</th>
                                            <th scope="col">Flip Name</th>
                                            <th scope="col">Journal Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @php
                                            $i = 1;
                                        @endphp
                                        @if(count($divisions) > 0)
                                            @foreach($divisions as $division)
                                                <tr>
                                                    <th scope="row">{{ $i }}</th>
                                                    <td>{{ $division->title }}</td>
                                                    <td>{{ $division->director_email }}</td>
                                                    <td>{{ $division->director_phone }}</td>
                                                    <td>{{ $division->flip_name ? $division->flip_name : 'Default Flip' }}</td>
                                                    <td>{{ $division->journal_name ? $division->journal_name : 'Default Journal' }}</td>
                                                    <td>
                                                        <a href="{{ url('backend/division/detail/' . $division->id) }}" class="btn btn-primary">
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
                                        @if(isset($divisions))
                                            {!! $divisions->appends(Input::except('page'))->links() !!}
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
