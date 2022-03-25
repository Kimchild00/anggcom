@if(Session::has('alert-class'))
    <div class="text-center alert alert-{{ (Session::get('alert-class') == "success") ? "success" : "danger" }}" role="alert">
        {{ Session::get('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif