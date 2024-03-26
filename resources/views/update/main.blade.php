<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WorkTick | Update Database</title>
    <link rel=icon href={{ asset('/assets/images/logo.png') }}>
    <link rel="stylesheet" href="{{asset('/assets_setup/css/bootstrap.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('/assets_setup/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('/assets_setup/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('/assets_setup/css/font-awesome-animation.css')}}">

</head>
<body class="background">
    <div class="container-progress container">
        <div class="row text-center section-setup">
            <div class="col-12">
                <h1>WorkTick Update Database</h1>
            </div>
        </div>
        @yield('content')
    </div>


<script src="{{asset('/assets_setup/js/jquery.min.js')}}"></script>
<script src="{{asset('/assets_setup/js/tippy.all.min.js')}}"></script>
<script src="{{asset('/assets_setup/js/scripts.js')}}"></script>

</body>
</html>
