<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <div class="ayushman-home-main">
        <img class="top-img" src="{{ asset('assets/images/splash-screen-top 1.png')}}">
        <div class="container" style="max-width: 700px;">
            <div class="logo">
                <img src="{{ asset('assets/images/logo-auys (1).png')}}">
            </div>
            <div class="home-buttons">
                <div class="row" style="justify-content: center;">
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{route('mst_login')}}"><button><img src="{{ asset('assets/images/icon-1.png')}}"><br><span>Admin</span></button></a>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{route('mst_login.pharmacy')}}"><button><img src="{{ asset('assets/images/icon-2.png')}}"><br><span>Pharmacy</span></button></a>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{route('mst_login.doctor')}}"><button><img src="{{ asset('assets/images/icon-3.png')}}"><br><span>Doctor</span></button></a>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{route('mst_login.receptionist')}}"><button><img src="{{ asset('assets/images/icon-4.png')}}"><br><span>Reception</span></button></a>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{route('mst_login.accountant')}}"><button><img src="{{ asset('assets/images/icon-5.png')}}"><br><span>Accountant</span></button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <img class="top-img2" src="{{ asset('assets/images/splash-screen-top 2.png')}}">
</body>

</html>