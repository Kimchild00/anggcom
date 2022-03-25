<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anggaran COM @yield('title')</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.1.0/css/all.css" integrity="sha512-ajhUYg8JAATDFejqbeN7KbF2zyPbbqz04dgOLyGcYEk/MJD3V+HJhJLKvJ2VVlqrr4PwHeGTTWxbI+8teA7snw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" id="theme-styles">
    <style>
      .select2-selection__rendered {
        line-height: 20px !important;
      }
      .select2-container .select2-selection--single {
        padding-bottom: 2.75rem !important;
        border: 1px solid #E4E4E7;
      }
      .select2-selection__arrow {
        padding-bottom: .75rem;
        height: 44px !important;
        padding-top: .75rem;
      }
      .select2-selection__rendered{
        padding-bottom: .75rem;
        padding-top: .75rem;
        
      }

      .select2-container{
        width: 100% !important;
      }

      .select2-selection__rendered
      {
        /* padding-bottom: .75rem !important;
        padding-top: .75rem !important; */
      }
    </style>
</head>
<body>
<div class="h-screen flex overflow-hidden bg-gray-100">
    <div class="flex flex-col w-0 flex-1 overflow-hidden">
      <main class="flex-1 relative overflow-y-auto focus:outline-none">
        <div class="py-6">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            @yield('content')
          </div>
        </div>
      </main>
    </div>
</div>
  <script src="{{ asset('js/app.js') }}"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" ></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @yield('js')

</body>
</html>