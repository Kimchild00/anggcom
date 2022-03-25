@extends('frontend.layouts.app')
@section('title', '| User')
@section('css')
<style>
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

        animation: load 1s linear infinite;
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
@endsection
@section('js')
    <script>
        $(document).ready(function () {

        });

        function editOtp(e,id){
            $('#loading').css('display', 'flex')

            $.ajax({
                url: "{{ url('/user/update-otp') }}" + "/" +id,
                type: "Put",
                data: {
                    _token: "{{ csrf_token() }}",
                    otp: e
                },
                success: function (data) {
                    // $('#loading').css('display', 'none')
                    if(data.status){
                        // setTimeout(myGreeting, 2000);
                        setTimeout(function() {
                            myGreeting(data.status);
                        }, 250)
                    }else{

                    }
                    console.log(data);
                }
            });
        }

        function myGreeting(status) {
            $('#loading').css('display', 'none')
            if(status){
                Swal.fire({
                    icon: 'success',
                    title: 'Update otp Success',
                    showConfirmButton: false,
                    timer: 1000
                })
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Update otp Error',
                    showConfirmButton: false,
                    timer: 1000
                })
            }
        }
    </script>
@endsection
@section('content')

    <div class="w-full mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="w-full mt-5">

        <div class="rounded-md bg-blue-500-50 py-2 mb-5">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Heroicon name: solid/x-circle -->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1zm-.5 5a1 1 0 1 0 0 2h.5a1 1 0 1 0 0-2h-.5zM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2z" fill="currentColor"/></g></svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-700">
                        Notes:
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul role="list" class="list-disc pl-5 space-y-1">
                            <li>Level consist of 3 types: master level, child level, and finance level</li>
                            <li>The master level is the main account that can create, modify and even delete other user account</li>
                            <li>The finance level is the account level where you can see all transactions in all divisions and can be selected as approval finance</li>
                            <li>The child level is the staff level where you can only make a budget.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="w-full mt-5 2xl:flex xl:flex lg:flex items-center 2xl:justify-between xl:justify-between lg:justify-between">
        <div class="w-full">
            @if(\Illuminate\Support\Facades\Auth::user()->level == 'master')
                <a href="{{url('user/create') }}"
                    class="cursor-pointer mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Create
                </a>
            @endif
        </div>
        <form class="sm:flex sm:items-center justify-end mt-2" action="{{ url('user') }}" method="GET">
            <div class="w-full sm:max-w-xs m-1">
                <label for="email" class="sr-only">Name</label>
                <input type="text" name="name" value="{{isset($filters['name']) ? $filters['name'] : ''}}"
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                       placeholder="Name">
            </div>
            <div class="w-full sm:max-w-xs m-1">
                <label for="email" class="sr-only">Email</label>
                <input type="text" name="email" value="{{isset($filters['email']) ? $filters['email'] : ''}}"
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                       placeholder="Email">
            </div>
            <button type="submit"
                    class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Search
            </button>
        </form>
    </div>
    <div class="flex flex-col w-full bg-white shadow-sm pb-5 mt-5">
        <div class='overflow-x-auto w-full'>
            <table class="min-w-full divide-y divide-gray-200 w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        #
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Name
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Phone
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Level
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Otp
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Action</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(count($users) > 0)
                    @foreach ($users as $key => $item)
                        <tr class="{{($key % 2) == 0 ? 'bg-white' : 'bg-gray-50'}}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$key + 1}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$item->name}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{$item->email}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{$item->phone}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{$item->level}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                <select id="Otp" onchange="editOtp(this.value, {{ $item->id }})" {{ \Illuminate\Support\Facades\Auth::user()->level == 'master' ? '' : 'disabled' }} class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2.5 px-2 bg-white">
                                    <option value="Off" {{ $item->is_otp == 'Off' ?'selected' : ''}}>Off</option>
                                    <option value="Email" {{ $item->is_otp == 'Email' ?'selected' : ''}}>Email</option>
                                    <option value="GoogleAuthenticator" {{ $item->is_otp == 'GoogleAuthenticator' ?'selected' : ''}}>GoogleAuthenticator</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(\Illuminate\Support\Facades\Auth::user()->level == 'master')
                                    <a href="{{ url('user/edit/' . $item->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </a>
                                    @if($item->level != 'master')
                                    |<a href="{{ url('user/delete/' . $item->id) }}"
                                       class="text-red-500 hover:text-red-900"
                                       onclick="return confirm('Are you sure?')">
                                        Delete
                                    </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="bg-white">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" colspan="6">
                            Not Found
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            
        </div>
    </div>
    <div class="w-full flex justify-center mt-5">
       {!! $users->appends(Request::only(['keyword']))->links('frontend.components.paginate') !!}
    </div>
@endsection
