<head>
    <base href="{{asset('user/')}}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!--====== Title ======-->
    <title>@yield('title')</title>

    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="icon" href="images/logo/LDMobile-logo.png" type="image/png" sizes="16x16">

    {{-- bootstrap --}}
    <link rel="stylesheet" href="css/bootstrap.css">

    {{-- owl-carousel --}}
    <link rel="stylesheet" href="css/owl.carousel.min.css">

    {{-- cropper --}}
    <link rel="stylesheet" href="css/cropper.css">
    
    {{-- jquery-ui --}}
    <link rel="stylesheet" href="css/jquery-ui.min.css">

    {{-- css --}}
    <link rel="stylesheet" href="css/style.css">

    {{-- font-awesome --}}
    <link rel="stylesheet" href="fonts/font-awesome/css/all.css">
</head>
    
</body>
</html>