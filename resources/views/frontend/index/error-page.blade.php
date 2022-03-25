@extends('frontend.layout')


@section('content')
    @include('notif-alert')

    <div class="flex-center position-ref full-height">

        <div class="content">
            <div class="title m-b-md">
                Error page
            </div>


            <div class="links">
                {{ $message }}
            </div>

        </div>
    </div>
@endsection
