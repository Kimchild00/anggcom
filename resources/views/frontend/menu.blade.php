<div class="top-right links">
    @if (Auth::check())
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <a href="{{ url('/user') }}">User</a>
        <a href="{{ url('/transaction') }}">Transaction</a>
        <a href="{{ url('/division') }}">Division</a>
        <a href="{{ url('/inquiry') }}">Inquiry</a>
        <a href="{{ url('/logout') }}">Logout</a>
    @else
        <a href="{{ url('/login') }}">Login</a>
        <a href="{{ url('/register') }}">Register</a>
    @endif
</div>