@extends('frontend.layouts.app')
<style>
    .button{
        margin-top: 10px;
        border-radius: 5.2rem !important;
    }
        .field-icon {
    float: right;
    margin-top: -25px;
    position: relative;
    z-index: 2;
    right: 10;
    }

    #loading{
        width: 50px;
        height: 50px;
        border: solid 5px #ccc;
        border-top-color:  rgb(68, 84, 175);
        border-radius: 100%;

        position: absolute;
        left: 0;
        bottom: 0;
        right: 0;
        top: 0;
        margin: auto;

        animation: load 2s linear infinite;
    }

    .loading-bg{
        /* background-color: rgba(26, 26, 26, 0.3); */
        width: 100%;
        height: 100%;
        position: absolute;
        top: 13;
        left: 0;
        right: 0;
    }

    @keyframes load{
        form{transform: rotate(0deg)}
        to{transform: rotate(360deg)}
    }
</style>
@section('js')
<script>
    $(".toggle-old-password").click(function() {

        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $(".toggle-password").click(function() {

        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $(".toggle-password-confirmation").click(function() {

        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    function load(){
        if ($('#old-password-field').val() == '' || $('#password-field').val() =='' || $('#password-confirmation-field').val() =='') {
            $('.loading-bg').css('display', 'none')
        }else{
            const button = $('.button').hide();
            $('.loading-bg').css('display', 'block')
        }
    }

</script>
    
@endsection
@section('content')
<div class="w-full bg-white md:w-6/12 mx-auto text-sm rounded-lg shadow-sm">
    <div class="w-full px-5 py-5">
        @include('frontend.layouts.alert-message')
        <h1 class="text-lg font-bold text-gray-600 text-center">Change Password</h1>
        <form action="{{ url('change-password') }}" method="POST" class="w-full py-5 px-5">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                    <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Old Password 
                    </label>
                    <input type="password" name="old_password" class="form-control load" id="old-password-field" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md" required>
                    <span toggle="#old-password-field" class="fa fa-fw fa-eye field-icon toggle-old-password"></span>
                </div>
                <div class="col-md-12">
                    <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        New Password 
                    </label>
                    <input type="password" name="password" class="form-control load" id="password-field" required>
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                </div>
                <div class="col-md-12">
                    <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Confirm Password 
                    </label>
                    <input type="password" name="password_confirmation" class="form-control load" id="password-confirmation-field" required>
                    <span toggle="#password-confirmation-field" class="fa fa-fw fa-eye field-icon toggle-password-confirmation"></span>
                </div>
                <div class="form-group mb-5">
                   <button type="submit" class="w-full bg-blue-400 py-3 px-3 hover:bg-blue-500 rounded-sm shadow-sm text-white button" onclick="load(this)">
                        Save
                    </button>
                </div>
            </div>
        </form>
        <div class="loading-bg" style="display: none">
            <div id="loading"></div>    
        </div>
    </div>
</div>
@endsection
