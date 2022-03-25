<div class="top-right links">
        <label for="">Backend Site</label>
    {{--@if (Auth::check())--}}
        <a href="{{ url('backend/dashboard') }}">Dashboard</a>
        <a href="{{ url('backend/user-company') }}">User Company</a>
        <a href="{{ url('backend/user') }}">User</a>
        <a href="{{ url('backend/transaction') }}">Transaction</a>
        <a href="{{ url('backend/division') }}">Division</a>
        <a href="{{ url('backend/inquiry') }}">Inquiry</a>
        {{--<a href="{{ url('backend/logout') }}">Logout</a>--}}
    {{--@else--}}
        {{--<a href="{{ url('backend/login') }}">Login</a>--}}
        {{--<a href="{{ url('backend/register') }}">Register</a>--}}
    {{--@endif--}}
</div>