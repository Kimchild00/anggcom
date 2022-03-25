@extends('frontend.layouts.app')
<style>
    #change_password{
        float: right;
        margin-top: 8px;
        margin-right: 12px;
        border-radius: 5.2rem !important;
    }
</style>
@section('content')
    <div class="w-full mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="flex flex-col w-full bg-white shadow-sm mt-5">
        <div class='overflow-x-auto w-full'>
            <table class="min-w-full divide-y divide-gray-200 w-full">
                <tbody>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name</th>
                    <td>{{ $user->user_company->title }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Package</th>
                    <td>{{ $user->user_company->package_name }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Expired At</th>
                    <td>{{ $user->user_company->expired_at ? date("d-M-Y H:i:s", strtotime($user->user_company->expired_at)) : 'Not Paid' }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <td>{{ $user->phone }}</td>
                </tr>
                <tr>
                    <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Account Level</th>
                    <td><label for="" class="btn btn-warning btn-sm">{{ $user->level }}</label></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <a href="{{url('change-password')}}" id="change_password" class="btn btn-success disabled-href">Change Password</a>

@endsection
