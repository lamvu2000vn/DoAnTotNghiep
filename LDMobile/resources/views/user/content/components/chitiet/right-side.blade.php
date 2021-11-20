{{-- nhà cung cấp --}}
<div class="d-flex mb-40">
    <img src="{{ $url_logo.$supplier['anhdaidien'] }}" alt="" class="detail-supplier-img">
    <div class="d-flex flex-column ml-20 mt-20">
        <div>Cung cấp bởi <b>{{ $supplier['tenncc'] }}</b></div>
        <img src="images/icon/genuine-icon.png" alt="" width="130px">
    </div>
</div>
{{-- bảo hành --}}
@if($phone['baohanh'])
    <div class='detail-warranty p-10 mb-30'>
        <div class="d-flex align-items-center">
            <i class="fas fa-shield-check mr-10 fz-18 main-color-text"></i>
            <span>Bảo hành chính hãng {{ $phone['baohanh'] }}</span>
        </div>
    </div>
@endif
<div class="row mb-20">
    <div class="col-4">
        <div class="detail-badge">
            <div class="detail-badge-icon"><img src="images/icon/free-ship.png" alt="badge"></div>
            <div class="detail-badge-text">Miễn phí giao hàng</div>
            <div class="detail-badge-checked-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-4">
        <div class="detail-badge">
            <div class="detail-badge-icon"><img src="images/icon/box-check.png" alt="badge"></div>
            <div class="detail-badge-text">Mở hộp kiểm tra nhận hàng</div>
            <div class="detail-badge-checked-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-4">
        <div class="detail-badge">
            <div class="detail-badge-icon"><img src="images/icon/return.png" alt="badge"></div>
            <div class="detail-badge-text">Đổi trả trong vòng 30 ngày</div>
            <div class="detail-badge-checked-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
</div>

{{-- kiểm tra còn hàng --}}
<div id="check-qty-in-stock-btn" type="button" class="see-store" data-id="{{$phone['id']}}"><i class="fas fa-store mr-5"></i> Xem các chi nhánh còn hàng</div>