@extends('frontend.layouts.app-auth')
@section('title', '| Login')
@section('js')
<script type="text/javascript">
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

        document.getElementById('btnpass').addEventListener('click', function() {
            let passType = document.getElementById('password')
            let btnPass = document.getElementById('btnpass')
            var eyes = document.getElementById('eyes')
            if (passType.type == 'password') {
                eyes.src ="https://image.flaticon.com/icons/png/512/65/65000.png"
                passType.type = 'text'
            } else {
                eyes.src ="https://static.thenounproject.com/png/718767-200.png"
                passType.type = 'password'
            }
        })

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
    <div class="w-full md:w-3/5">
    </div>
    <div class="w-full bg-white md:w-6/12 mx-auto text-sm rounded-lg shadow-sm">
        <div class="w-full px-5 py-5">
            @include('frontend.layouts.alert-message')
            <h1 class="text-lg uppercase font-bold text-gray-600 text-center">Login</h1>
            <form action="{{ url('login') }}" method="POST" class="w-full py-5 px-5">
                {{ csrf_field() }}
                <div class="w-full flex mb-5" >
                    <select class="rounded-sm px-2 py-3 w-full border " style="padding-bottom: .75rem; padding-top: .75rem;" name="company_name" id="company_name">
                    </select>
                </div>
                <div class="w-full flex mb-5" id="div-company">
                    <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
                        Please fill out this field.
                    </span>
                </div>
                <div class="w-full flex mb-5">
                    <input type="email" name="email" class="rounded-sm px-2 py-3 w-full border border-gray-200" placeholder="E-Mail" id="email">
                </div>
                <div class="w-full flex mb-5" id="div-email">
                    <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
                        Please fill out this field.
                    </span>
                </div>
                <div class="w-full flex mb-5">
                    <input type="password"name="password" class="w-full rounded-sm px-2 py-3 border border-gray-200" placeholder="Password" id="password">
                    <button class="w-6/10 bg-blue-400  hover:bg-blue-500 px-3 py-3 rounded-pill shadow-sm uppercase text-white" type="button" id="btnpass">
                        <img src="https://static.thenounproject.com/png/718767-200.png" class="h-5 w-5" alt="" id="eyes">
                    </button>
                </div>
                <div class="w-full flex mb-5" id="div-password">
                    <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
                        Please fill out this field.
                    </span>
                </div>
                <div class="form-group mb-5">
                    <button class="w-full bg-blue-400 py-3 px-3 hover:bg-blue-500 rounded-pill shadow-sm uppercase text-white mt-5">Login</button>
                </div>
                <div class="form-group text-center ">
                    <a href="{{ url('forgot-password')}}" class="text-blue-500 hover:text-blue-600">Forgot Password?</a>
                    <p>New user ? Register <a href="/register" class="text-blue-500 hover:text-blue-600">Here</a></p>
                </div>
            </form>
        </div>
    </div>
@endsection