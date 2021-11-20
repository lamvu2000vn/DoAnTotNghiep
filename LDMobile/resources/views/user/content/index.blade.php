@extends("user.layout")
@section("title")LDMobile @stop
@section("content")

@include("user.content.index.slideshow")

@include("user.content.index.sec-banner")

@include("user.content.index.sec-khuyen-mai-hot")
@include("user.content.index.sec-san-pham-noi-bat")

@include('user.content.section.sec-logo')
@stop