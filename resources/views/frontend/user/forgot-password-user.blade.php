@extends('frontend.layouts.app-auth')
<style>
    .field-icon {
        float: right;
        margin-top: -30px;
        position: relative;
        z-index: 2;
        right: 10;
    }
</style>
@section('js')
    <script>
    $(".toggle-new-password").click(function() {

        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $(".toggle-confirm-password").click(function() {

        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
    </script>
@endsection

@section('content')
<div class="w-full bg-white md:w-6/12 mx-auto text-sm rounded-lg shadow-sm">

    <div class="w-full px-5 py-5">
        <h1 class="text-lg font-bold text-blue-700 text-center">Create New Password</h1>
        <h5 class="text-center font-bold">Your new password must be different from previous used passwords</h5>
        @include('frontend.layouts.alert-message')
        <form action="{{ url('request-password?token='.$token.'&email='.$email) }}" method="POST" class="w-full py-5 px-5">
            {{ csrf_field() }}
            <div class="row">
                
                <div class="col-md-12">
                    <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2" style="margin-left: 5px">Password</label>
                    <input type="password" id="password-field" name="new_password" class="rounded-sm px-2 py-3 w-full border border-gray-200 rounded" required>
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-new-password"></span>
                </div>
                <div class="col-md-12">
                    <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2" style="margin-left: 5px">Confirm Password</label>
                    <input id="password-confirm" type="password" class="form-control rounded-sm px-2 py-3 w-full border border-gray-200 rounded" name="confirm_password" required>
                    <span toggle="#password-confirm" class="fa fa-fw fa-eye field-icon toggle-confirm-password"></span>
                </div>
                    <strong>Both passwords must match.</strong>
                <div class="form-group" style="text-align: center; margin-top: 10px">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                        Send
                    </button>
                    <a href=""></a>
                </div>
        </form>
        <div class="loading-bg" style="display: none">
            <div id="loading"></div>    
        </div>
    </div>
</div>
@endsection