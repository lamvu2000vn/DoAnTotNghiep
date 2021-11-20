<section class='user-bg-color pt-20 pb-20'>
    <div class='container'>
        <div class='row'>
            <div class='col-lg-8 col-12'>
                <div id="carouselExampleIndicators" class="relative carousel carousel-dark slide" data-bs-ride="carousel">
                    <div class="carousel-inner box-shadow">
                        <div class="carousel-item active">
                            <img src="<?php echo $url_slide.$lst_slide[0]['hinhanh'] ?>" class='carousel-img' alt="...">
                        </div>    
                        @for($i = 1; $i < count($lst_slide); $i++)
                            <div class="carousel-item">
                                <img src="<?php echo $url_slide.$lst_slide[$i]['hinhanh'] ?>" class='carousel-img' alt="...">
                            </div>    
                        @endfor
                    </div>
                    <div class="slideshow-btn-prev" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                        <i class="far fa-chevron-left"></i>
                    </div>
                    <div class='slideshow-btn-next' data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                        <i class="far fa-chevron-right"></i>
                    </div>
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                            aria-current="true"></button>
                        @for ($i = 1; $i < $qty_slide; $i++)
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $i ?>"></button>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- banner --}}
            <div class='col-lg-4 col-12'>
                <div class="row">
                    @foreach ($lst_banner as $key)
                        <div class="col-lg-12 col-md-4 col-12"><img src="{{$url_banner.$key->hinhanh}}" class='single-banner' alt="banner"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div> 
</section>