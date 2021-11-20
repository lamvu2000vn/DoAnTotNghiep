<div class='row'>
    <div class='col-md-3'>
        @section("acc-address-active") account-sidebar-active @stop
        @include("user.content.taikhoan.sec-thanh-chuc-nang")
    </div>
    <div class="col-md-9">
        <div id="new-address-show" class="btn-new-address mb-30">
            <i class="far fa-plus mr-10"></i>Thêm địa chỉ mới
        </div>

        @if ($addressList['status'])
            {{-- địa chỉ mặc định --}}
            <?php $default = $addressList['default'] ?>

            <div id="address-{{$default['id']}}" data-default="true" class="address-wrapper-default">
                <div class="d-flex justify-content-between pb-10">
                    <div class="d-flex">
                        <b id="adr-fullname-{{$default['id']}}" class="text-uppercase">{{ $default['hoten'] }}</b>
                        <div class="d-flex align-items-center success-color fw-600 ml-15"><i class="far fa-check-circle mr-5"></i>Đang sử dụng</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div type="button" data-id="{{$default['id']}}" class="btn-edit-address main-color-text"><i class="fas fa-pen mr-5"></i>Chỉnh sửa</div>
                    </div>
                </div>
    
                <div class="mb-5">
                    <div class="adr-content">
                        {{$default['diachi'].', '.$default['phuongxa'].', '.$default['quanhuyen'].', '.$default['tinhthanh']}}
                    </div>
                </div>
    
                <div id="adr-tel-{{$default['id']}}" class="adr-tel">{{$default['sdt']}}</div>
            </div>

            {{-- địa chỉ khác --}}
            <?php $another = $addressList['another'] ?>
            @foreach ($another as $key)
                @if($key['macdinh'] == 0)
                    <div id="address-{{$key['id']}}" data-default="false" class="address-wrapper">
                        <div class="d-flex justify-content-between pb-10">
                            <div class="d-flex">
                                <b id="adr-fullname-{{$key['id']}}" class="text-uppercase">{{ $key['hoten'] }}</b>
                            </div>
                            {{-- nút chức năng --}}
                            <div class="d-flex align-items-center">
                                <div type="button" data-id="{{$key['id']}}" class="btn-edit-address main-color-text mr-10"><i class="fas fa-pen mr-5"></i>Chỉnh sửa</div>
                                <div type="button" data-id="{{$key['id']}}" class="btn-delete-address red"><i class="fas fa-trash mr-5"></i>Xóa</div>
                            </div>
                        </div>
            
                        <div class="mb-5">
                            <div class="adr-content">
                                {{$key['diachi'].', '.$key['phuongxa'].', '.$key['quanhuyen'].', '.$key['tinhthanh']}}
                            </div>
                        </div>
            
                        <div class="d-flex justify-content-between">
                            <div id="adr-tel-{{$key['id']}}" class="adr-tel">{{$key['sdt']}}</div>
                            <div type="button" data-id="{{$key['id']}}" class="btn-set-default-btn">Đặt làm mặc định</div>
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="p-70 box-shadow text-center">Bạn chưa có địa chỉ giao hàng.</div>
        @endif
    </div>
</div>

{{-- modal thêm|sửa địa chỉ --}}
@include("user.content.modal.dia-chi-modal")

{{-- modal xóa --}}
@include("user.content.modal.xoa-modal")

<div id="toast"></div>