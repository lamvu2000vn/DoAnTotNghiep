<div class='account-voucher'>
    {{-- số phần trăm giảm --}}
    <div class='voucher-left-small'>
        <div class='voucher-left-small-content'>-{{$voucher->chietkhau*100}}%</div>
    </div>
    {{-- nội dung --}}
    <div class='voucher-right-small'>
        {{-- icon xem chi tiết --}}
        <b class="fz-14">{{$voucher->code}}</b>
        <div class="d-flex align-items-center">
            <div data-id="{{$voucher->id}}" class="relative promotion-info-icon mr-10">
                <i class="fal fa-info-circle main-color-text fz-20"></i>
                <div data-id="{{$voucher->id}}" class='voucher-content'>
                    <table class='table'>
                        <tbody>
                            <tr>
                                <td class='w-40'>Mã</td>
                                <td><b>{{$voucher->code}}</b></td>
                            </tr>
                            <tr>
                                <td class="w-40">Nội dung</td>
                                <td>{{$voucher->noidung}}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class='w-40'>
                                    <div class='d-flex flex-column'>
                                        <span>Điều kiện</span>
                                        @if ($voucher->dieukien != 0)
                                        <ul class='mt-10'>
                                            <li>Áp dụng cho đơn hàng từ {{number_format($voucher->dieukien, 0, '', '.')}}<sup>đ</sup></li>
                                        </ul>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class='w-40'>Hạn sử dụng</td>
                                <td>{{$voucher->ngayketthuc}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div data-id="{{$voucher->id}}" class="apply-voucher-btn main-btn" style="padding: 5px">Bỏ chọn</div>
        </div>
    </div>
</div>