@extends('frontend.layouts.app')
@section('title', isset($user) ? '| Edit User' : '| Create User')
@section('js')
    <script type="text/javascript">
        $('#resetPassword').on('click', function(){
            MODAL.show('.reset-password');
        })

        $('.submitResetPassword').on('click', function(){
            var resetPassword = $('#reset_password').val();
            var userID = $('#user_id').val();
            var confPassowrd = $('#reset_password_confirmation').val();
            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                url: "{{ url('user/reset-password' ) }}" + '/' + userID,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    resetPassword: resetPassword,
                    confPassword: confPassowrd
                },
                success: function(response){
                    if(response.status){
                        MODAL.hide('.reset-password');
                        $('.submitResetPassword').prop('disabled', false).html('Submit');
                        Swal.fire({
                            icon: 'success',
                            title: 'Password has been reset',
                            showConfirmButton: false,
                            timer: 1000
                        })
                    }else{
                        $('.submitResetPassword').prop('disabled', false).html('Submit');
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                        })
                    }
                }
            })
        })
        // function passwordConfirmation(e){
        $("#reset_password_confirmation").on("input", function(){

            var password = $('#reset_password').val();
            var passwordConfirmation = $(this).val();
            if(password == passwordConfirmation){
                $('#confirmation-password-span').hide();
            }else{
                $('#confirmation-password-span').show();
            }
        });
        // }
        function showPass(e,eye){
            console.log(e);
            var e = document.getElementById(e);
            var eye = document.getElementById(eye);
            if(e.type == 'password'){
                eye.src ="https://image.flaticon.com/icons/png/512/65/65000.png"
                e.type = 'text'
            }else{
                eye.src ="https://static.thenounproject.com/png/718767-200.png"
                e.type = 'password'
            }
        }
    </script>
@endsection
@section('content')
    <div class="w-full mt-5">
        <div class="w-full md:w-3/5">
            @include('frontend.layouts.alert-message')
        </div>
        <div class="w-full md:w-3/5 bg-white rounded-sm">
            <form action="{{ isset($user) ? url('user/' . $user->id) : url('user') }}" method="post"
                class="w-full py-5 px-3">
                @if (isset($user))
                    <p class="pb-5 text-sm text-gray-600 font-bold"> Edit User {{ $user->email }}</p>
                    {{ method_field('PUT') }}
                @endif
                {{ csrf_field() }}
                <div class="sm:col-span-3 mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Name <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" name="name" autocomplete="name" value="{{ isset($user) ? $user->name : '' }}"
                            class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            required>
                    </div>
                </div>

                @if (isset($user))
                    <input type="hidden" name="email" autocomplete="name" value="{{ $user->email }}">
                @else
                    <div class="sm:col-span-3 mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <input type="email" name="email" autocomplete="name" required
                                class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                @endif
                <div class="sm:col-span-3 mb-5">
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Phone <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" name="phone" value="{{ isset($user) ? $user->phone : '' }}"
                            autocomplete="phone" required
                            class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                @if (isset($user))
                    <div class="sm:col-span-3 mb-5">
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Level <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <select name="level" autocomplete="bank" required
                                class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white capitalize">
                                <option value="master" {{ $user->level == 'master' ? 'selected' : '' }}>Master</option>
                                <option value="child" {{ $user->level == 'child' ? 'selected' : '' }}>Child</option>
                                <option value="finance" {{ $user->level == 'finance' ? 'selected' : '' }}>Finance</option>
                            </select>
                        </div>
                    </div>
                @else
                    <div class="sm:col-span-3 mb-5">
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Level <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <select name="level" autocomplete="bank" required
                                class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white capitalize">
                                <option value="">-- Choose your level --</option>
                                <option value="child">Child</option>
                                <option value="finance">Finance</option>
                            </select>
                        </div>
                    </div>
                @endif
                @if(!isset($user))
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-5">
                        Enable OTP Login
                    </label>
                    <div class="sm:col-span-3 mb-5">
                        <div class="mt-1">
                            <select name="is_otp" autocomplete="bank" required
                            class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white capitalize">
                                <option value="Off" {{isset($user) && $user->is_otp == 'Off' ? 'selected' : ''}}>Off</option>
                                <option value="Email" {{isset($user) && $user->is_otp == 'Email' ? 'selected' : ''}}>Email</option>
                                <option value="GoogleAuthenticator" {{isset($user) && $user->is_otp == 'GoogleAuthenticator' ? 'selected' : ''}}>Google Authenticator</option>
                            </select>
                        </div>
                    </div>
                @endif
                @if (!isset($user))
                <div class="sm:col-span-3 mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password {!! isset($user) ? '' : '<span class="text-red-600 font-bold italic">*) required</span>' !!}
                    </label>
                    <div class="mt-1">
                        <input type="password" name="password" autocomplete="phone" {{ isset($user) ? '' : 'required' }}
                            class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                @endif
                <div class="w-full">
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save
                    </button>
                    <a href="{{ url('user') }}" class="btn btn-warning">
                        Back
                    </a>
                    @if(isset($user) && $user->level == 'master')
                        <button type='button' class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        id="resetPassword">
                            Reset password
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="modal reset-password z-3 h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded shadow-lg w-10/12 md:w-2/5">
            <div class="border-b px-4 py-2 flex justify-between items-center">
                <h3 class="font-semibold text-lg">Reset Password</h3>
                <button class="text-black close-modal">&cross;</button>
            </div>
            <div class="w-full p-3">
                <div class="sm:col-span-3 mb-5">
                    <form action="">
                        <input type="hidden" id="user_id" value="{{ isset($user) ? $user->id : '' }}">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Reset Password  
                        </label>
                        <div class="w-full flex mb-5">
                            <input type="password" id="reset_password" name="reset_password"
                                value=""
                                required
                                placeholder="Password"
                                class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300"/>
                                <button class="w-6/10 bg-blue-400  hover:bg-blue-500 px-3 py-3 rounded-pill shadow-sm uppercase text-white" type="button" onclick="showPass('reset_password','eyes-reset-password')">
                                    <img src="https://static.thenounproject.com/png/718767-200.png" class="h-5 w-5" alt="" id="eyes-reset-password">
                                </button>
                        </div>
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Reset Password confirmation  
                        </label>
                        <div class="w-full flex mb-5">
                            <input type="password" id="reset_password_confirmation" name="reset_password"
                                value=""
                                required
                                placeholder="Confirmation Password"
                                class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300" />
                                <button class="w-6/10 bg-blue-400  hover:bg-blue-500 px-3 py-3 rounded-pill shadow-sm uppercase text-white" type="button" onclick="showPass('reset_password_confirmation','eyes-reset-password-conf')">
                                    <img src="https://static.thenounproject.com/png/718767-200.png" class="h-5 w-5" alt="" id="eyes-reset-password-conf">
                                </button>
                        </div>
                        <span class="text-red-600 font-bold italic" id="confirmation-password-span" hidden>*) Password Not Same</span>

                    </div>
                    <div class="flex justify-end items-center w-100 border-t p-3">
                        <button type="button" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white mr-1 close-modal">Cancel</button>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-white submitResetPassword">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
