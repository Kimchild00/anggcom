<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.url') }}</title>
    <style>
        * { box-sizing: border-box; }
        html { height: 100%; }
        body {
            position: relative;
            margin: 0;
            height: 100%;
            background: linear-gradient(steelblue, beige);
            font-family: 'Lato', sans-serif;
            font-weight: 300;
            text-align: center;
        }

        #loader {
            position: absolute;
            top:50%; left:50%;
            height:200px; width:100px;
            margin-top:-100px; margin-left:-50px;
        }

        #glass {
            position: relative;
            height: 100%;
            background: rgba(255,255,255,.1);
            border-radius: 0% 0% 15% 15%;
            border: 3px solid;
            border-top: 0;
            border-bottom: 20px solid;
            border-color: rgba(255,255,255,.7);
            overflow: hidden;
        }
        #drink {
            position: absolute;
            top:100%; right:0; bottom:0; left:0;
            background: linear-gradient(to bottom, orange, orangered);
            box-shadow: inset 0 2px 1px rgba(255,69,0,.2);
            opacity: .7;
        }
        #counter {
            position: relative;
            line-height: 200px;
            font-size: 22px;
            color: rgba(255,255,255,1);
        }

        #lemon {
            display: none;
            position: absolute;
            top:0; right:0;
            height:79px; width:79px;
            margin-top:-38px; margin-right:-38px;
            background: radial-gradient(#f7f3b6 10%, #d7d26c);
            border-radius: 50%;
            border: 4px solid #47582e;
            box-shadow: inset 0 0 0 2px #f7f3b6;
        }
        #straw {
            display: none;
            position: absolute;
            bottom:20px; right:30%;
            height:220px; width:6px;
            background: steelblue;
            border-radius: 0 6px 0 0;
            transform: rotate(-18.5deg);
            transform-origin: left bottom;
            -webkit-transform: rotate(-18.5deg);
            -webkit-transform-origin: left bottom;
        }
        #straw:after {
            content: '';
            position: absolute;
            top:0; right:0;
            height:6px; width:80px;
            background: inherit;
            border-radius: 0 6px 0 0;
        }

        #cubes {
            position: absolute;
            top:0; right:0; bottom:0; left:0;
        }
        #cubes div {
            /*display: none;*/
            position: absolute;
            width:50px; height:50px;
            background: rgba(255,255,255,.3);
            border-radius: 10px;
            box-shadow: inset 0 0 10px rgba(255,255,255,.6);
        }
        #cubes div:nth-child(1) {
            bottom:0;
        }
        #cubes div:nth-child(2) {
            bottom:45px; left:25px;
            transform: rotate(32deg);
            transform-origin: center bottom;
            -webkit-transform: rotate(32deg);
            -webkit-transform-origin: center bottom;
        }
        #cubes div:nth-child(3) {
            bottom:90px; left:20px;
            transform: rotate(-34deg);
            transform-origin: center bottom;
            -webkit-transform: rotate(-34deg);
            -webkit-transform-origin: center bottom;
        }

        #coaster {
            width: 130%; height: 4px;
            margin-left: -15%;
            background: steelblue;
            border-radius: 2px;
        }

        footer {
            position: absolute;
            left:0; top:50%; right:0;
            margin-top: 120px;
            color: steelblue;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <input type="hidden" value="{{ $token }}" name="__agl">
    <div id="loader">
        <div id="lemon"></div>
        <div id="straw"></div>
        <div id="glass">
            <div id="cubes">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div id="drink"></div>
            <span id="counter"></span>
        </div>
        <div id="coaster"></div>
    </div>

    <footer>Please wait!<br>you will redirect automatically...</footer>

    <script>
        var worker = null;
        var loaded = 0;

        function increment() {
            $('#counter').html(loaded+'%');
            $('#drink').css('top', (100-loaded*.9)+'%');
            if(loaded==25) $('#cubes div:nth-child(1)').fadeIn(100);
            if(loaded==50) $('#cubes div:nth-child(2)').fadeIn(100);
            if(loaded==75) $('#cubes div:nth-child(3)').fadeIn(100);
            if(loaded==100) {
                $('#lemon').fadeIn(100);
                $('#straw').fadeIn(300);
                loaded = 0;
                stopLoading();
                setTimeout(startLoading, 1000);
            }
            else loaded++;    
        }

        function startLoading() {
            $('#lemon').hide();
            $('#straw').hide();
            $('#cubes div').hide();
            worker = setInterval(increment, 30);
        }

        function stopLoading() {
            clearInterval(worker);
        }

        setTimeout(() => {
            $(document).ready(function() {
                $.post('{{ env('IMPORTIR_URL') }}'+'/api/auth/check-anggaran-user', {token: '{{ $token }}'}, function(data) {
                    if (data.status) {
                        window.location.replace('{{ env('APP_URL') }}'+'/backend/dashboard');
                    }else{
                        window.location.replace('{{ env('APP_URL') }}'+'/login');
                    }
                });
            });
        }, 3500);

        startLoading();
    </script>
</body>
</html>