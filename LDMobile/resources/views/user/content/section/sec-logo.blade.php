<section class="clients-logo-section pt-70 pb-70">
    <div class="container">
        <div id='logo-carousel' class="owl-carousel">
            @foreach ($lst_brand as $key)
            <a href="{{route('user/dien-thoai')}}?hang={{$key['brand']}}"><img src="{{ $url_logo.$key['image']}}"></a>    
            @endforeach
        </div>
    </div>
</section>