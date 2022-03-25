@extends('frontend.layouts.app-auth')
@section('content')
    <div class="w-full md:w-1/2">
    </div>
    <div class="flex justify-center items-center h-96">
        <div class="w-full bg-white md:w-6/12  m-auto text-sm rounded-lg shadow-sm py-5">
            <img class="h-10 w-auto  m-auto " src="https://d2jnbxtr5v4vqu.cloudfront.net/images/anggarancom-logo-2021_11_05_08_24_17.png" alt="Workflow">
            <hr>
            <div class="w-full px-5 py-5">
                <h1 class="text-lg uppercase font-bold text-gray-600 text-center">Input OTP</h1>
                <form action="{{ url('login/otp') }}" method="POST" class="w-full py-5 px-5">
                    {{ csrf_field() }}
                    @if (isset($timeEx) || Session::get('timeEx'))
                        <input type="hidden" name="timeEx" id="timeExpiredOtp">
                    @endif
                    @if (isset($messageCodeGoogle) || Session::get('messageCodeGoogle') )
                        <input type="hidden" name="googleAuth" id="googleAuth" value="{{isset($messageCodeGoogle) == true ? $messageCodeGoogle : Session::get('messageCodeGoogle')}}">
                    @endif
                    <div class="w-full flex mb-5">
                        <input type="text" name="is_otp" class="rounded-sm px-5 m-auto py-3 w-1/2 border border-gray-200" placeholder="OTP" id="OTP">
                    </div>
                    <div class="form-group mb-5 text-center">
                        <button type="submit" class="w-1/2 bg-blue-400 py-3 hover:bg-blue-500 rounded-pill shadow-sm uppercase text-white mt-5">Login</button>
                    </div>
                </form>
            </div>
            <div class="flex justify-center items-center">
                <div class="border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                 fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 ">
                            <p class="text-sm text-yellow-700 flex justify-center items-center">
                                <span>Remember, this code will only be active for 2 minutes after this message is received.</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-center items-center">
                        <span>Remaining time <span id="demo"></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @if (isset($timeEx) || Session::get('timeEx') )
        @if (isset($timeEx))
            {{$timeExpired = $timeEx}}
        @else
            {{$timeExpired =  Session::get('timeEx')}}
        @endif
    <script>
        // Data timeExpired
        var dataFirst = `{{$timeExpired}}`;
        var dataExpired    = new Date(`{{$timeExpired}}`);

        // Convert data time To local Timezone
        var data =  new Date(Date.UTC( dataExpired.getFullYear(),dataExpired.getMonth(), dataExpired.getDate(), dataExpired.getHours(), dataExpired.getMinutes(), dataExpired.getSeconds()));
        data.toLocaleString().replace(/([.?*+^$[\]/\\(){}-])/g,"-").replace(/([.?*+^$[\]/,\\(){}])/g,"");
        // Replace data to Ex: 2021-12-14 24:59:50
        var timeExOtp = document.getElementById('timeExpiredOtp').value = dataFirst;
        
        // Set the date we're counting down to
        var countDownDate = new Date(data).getTime();
        
        // Update the count down every 1 second
        var x = setInterval(function() {
        
          // Get today's date and time
          var now = new Date().getTime();
        
          // Find the distance between now and the count down date
          var distance = countDownDate - now;
        
          // Time calculations for days, hours, minutes and seconds
          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
          // Display the result in the element with id="demo"
          document.getElementById("demo").innerHTML = minutes + "m " + seconds + "s ";
        
          // If the count down is finished, write some text
          if (distance < 0) {
            clearInterval(x);
            document.getElementById("demo").innerHTML = "EXPIRED";
          }
        }, 1000);
    </script>
    @endif
    @if (isset($sessionMessage))
        <script>
            const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: false,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
            })

            Toast.fire({
            icon: 'success',
            title:'{{$sessionMessage}}'
            })
        </script>
    @endif
    @if (Session::get('sessionMessage'))
        <script>
            const sessionMessage = '{{Session::get('sessionMessage')}}';
            const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: false,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
            })

            Toast.fire({
            icon: 'error',
            title: sessionMessage
            })
        </script>
   @endif
@endsection