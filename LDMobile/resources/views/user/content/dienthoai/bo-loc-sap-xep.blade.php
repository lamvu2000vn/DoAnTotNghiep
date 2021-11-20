<div class="shop-bar box-shadow">
    <b id="qty-product" class="fz-18"></b>
    @if(!parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY))
    <div id="filter-sort-btn">
        <div class="d-flex">
            {{-- bộ lọc --}}
            <div class='relative mr-20'>
                <span id='btn-show-filter' data-bs-toggle="modal" data-bs-target="#filter-modal"><i class="fal fa-filter mr-5"></i>Bộ lọc</span>
                <div class="filter-badge"></div>
            </div>

            {{-- sắp xếp --}}
            <div class="relative">
                <span id='btn-show-sort'><i class="fal fa-sort mr-5"></i>Sắp xếp</span>
                <div class="sort-badge" style="display: block"></div>
                <div class="shop-sort-box">
                    <div class="d-flex justify-content-center">
                        <div class='d-flex flex-column'>
                            <div class="mb-3">
                                <input type="radio" name='sort' id='default' value="default" checked>
                                <label for="default">Mặc định</label>
                            </div>
                            <div class="mb-3">
                                <input type="radio" name='sort' id='high-to-low' value="high-to-low">
                                <label for="high-to-low">Giá cao đến thấp</label>
                            </div>
                            <div class="mb-3">
                                <input type="radio" name='sort' id='low-to-high' value="low-to-high">
                                <label for="low-to-high">Giá thấp đến cao</label>
                            </div>
                            <div>
                                <input type="radio" name='sort' id='sale-off-percent' value="sale-off-percent">
                                <label for="sale-off-percent">% giảm</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- modal bộ lọc --}}
<div class="modal fade" id="filter-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body p-20">
                {{-- nút đóng --}}
                <div type="button" class="btn-close" data-bs-dismiss="modal"></div>
                {{-- hãng --}}
                <div class="filter-title">Hãng</div>
                <div class="d-flex align-items-center flex-wrap">
                    @foreach ($lst_brand as $key)
                        <div type="button" name="filter-item" data-type="brand" data-keyword="{{ $key['brand'] }}" class="filter-item brand">{{ $key['brand'] }}</div>
                    @endforeach
                </div><hr>

                {{-- giá, hệ điều hành --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="filter-title">Giá</div>
                        <div class="d-flex align-items-center flex-wrap">
                            <div type="button" name="filter-item" data-type="price" data-keyword="2" class="filter-item">Dưới 2 triệu</div>
                            <div type="button" name="filter-item" data-type="price" data-keyword="3-4" class="filter-item">Từ 3 - 4 triệu</div>
                            <div type="button" name="filter-item" data-type="price" data-keyword="4-7" class="filter-item">Từ 4 - 7 triệu</div>
                            <div type="button" name="filter-item" data-type="price" data-keyword="7-13" class="filter-item">Từ 7 - 13 triệu</div>
                            <div type="button" name="filter-item" data-type="price" data-keyword="13-20" class="filter-item">Từ 13 - 20 triệu</div>
                            <div type="button" name="filter-item" data-type="price" data-keyword="20" class="filter-item">Trên 20 triệu</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="filter-title">Hệ điều hành</div>
                        <div class="d-flex align-items-center flex-wrap">
                            <div type="button" name="filter-item" data-type="os" data-keyword="Android" class="filter-item">Android</div>
                            <div type="button" name="filter-item" data-type="os" data-keyword="iOS" class="filter-item">iOS</div>
                        </div>
                    </div>
                </div><hr>

                {{-- ram, dung lượng --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="filter-title">Ram</div>
                        <div class="d-flex align-items-center flex-wrap">
                            @foreach ($lst_ram as $key)
                                <div type="button" name="filter-item" data-type="ram" data-keyword="{{$key}}" class="filter-item">{{$key}}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="filter-title">Dung lượng</div>
                        <div class="d-flex align-items-center flex-wrap">
                            @foreach ($lst_capacity as $key)
                            <div type="button" name="filter-item" data-type="capacity" data-keyword="{{$key}}" class="filter-item">{{$key}}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class='see-result-filter pt-20'>
                    <div id='btn-see-filter' class="main-btn"></div>
                    <div class='shop-btn-remove-filter pt-10'>Bỏ chọn tất cả</div>
                </div>
            </div>
        </div>
    </div>
</div>