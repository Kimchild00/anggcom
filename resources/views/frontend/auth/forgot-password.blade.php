@extends('frontend.layouts.app-auth')
@section('js')
<script>
    $(document).ready(function(){
        $('#div-email').hide();
        $('#div-password').hide();
        $('#div-company').hide();

        var select2 = $('#company_name').select2({
            placeholder: "Select Company",
            ajax: {
                url: '/login/get-company-name',
                data: function (params) {
                    return {
                        search: params.term,
                        _token: $('base').data('token')
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },

            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 2,
            templateResult: function (data) {
                return data.title;

            },
            templateSelection: function (data) {
                if (data.id === '') { // adjust for custom placeholder values
                    return 'Company Name';
                }
                return data.title;
            },
        });

        select2.on('select2:select', function (evt) {
            $('input[name=company_name]').val(evt.params.data.title);
        }); 

        $('#form').on('submit', function(){
            const companyId = $('#company_name').val();
            const email = $('#email').val();
            const password = $('#password').val();
            
            if (companyId == '' || email == '' || password == '') {
                $('#div-email').hide();
                $('#div-password').hide();
                $('#div-company').hide();
                console.log('empty');
            }else{
                console.log('asdw');
                $('#form').submit();
            }
        });

    });
</script>
@endsection

@section('content')
<div class="w-full bg-white md:w-6/12 mx-auto text-sm rounded-lg shadow-sm">
    <div class="w-full px-5 py-5">
        <h1 class="text-lg font-bold text-blue-700 text-center">Forgot Password</h1>
        <h5 class="text-center font-bold">Enter your registered email below to receive password reset instruction</h5>
        @include('frontend.layouts.alert-message')
        <form action="{{ url('forgot-password') }}" method="POST" class="w-full py-5 px-5">
            {{ csrf_field() }}
            <div class="row">
                <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2" style="margin-left: 5px">Choose Your Company</label>
                <div class="w-full flex mb-5" >
                    <select class="rounded-sm px-2 py-3 w-full border " style="padding-bottom: .75rem; padding-top: .75rem;" name="company_name" id="company_name">
                    </select>
                </div>
                <div class="col-md-12">
                    <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2" style="margin-left: 5px">Email</label>
                    <input type="email" name="email" class="rounded-sm px-2 py-3 w-full border border-gray-200 rounded" placeholder="E-Mail" required>
                </div>
                <small>Remember password? <a href="{{ url('login') }}" class="goto-log text-blue-700">Login</a></small>
                <div class="form-group" style="text-align: center; margin-top: 10px">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                        Send
                    </button>
                </div>
        </form>
        <div class="loading-bg" style="display: none">
            <div id="loading"></div>    
        </div>
    </div>
</div>
@endsection