<div class='account-voucher'>
    {{-- số phần trăm giảm --}}
    <div class='voucher-left w-20 p-70'>
        @if ($sl > 1)
            <div class="voucher-qty">{{$sl}}x</div>    
        @endif
        <div class='voucher-left-content fz-40'>-{{$voucher->chietkhau*100}}%</div>
    </div>
    {{-- nội dung --}}
    <div class='voucher-right w-80'>
        <div class="voucher-right-content">
            {{-- icon xem chi tiết --}}
            <div class="d-flex justify-content-end">
                <div data-id="{{$voucher->id}}" class="relative promotion-info-icon">
                    <i class="fal fa-info-circle fz-20"></i>
                    <div data-id="{{$voucher->id}}" class='voucher-content'>
                        <table class='table'>
                            <tbody>
                                <tr>
                                    <td class='w-40'>Mã</td>
                                    <td><b>{{$voucher->code}}</b></td>
                                </tr>
                                <tr>
                                    <td class='w-40'>Nội dung</td>
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
            </div>
            {{-- nội dung --}}
            <div class="flex-fill">{{$voucher->noidung}}</div>
            {{-- hạn sử dụng --}}
            <div class="d-flex justify-content-between">
                <span class="d-flex align-items-end">HSD: {{$voucher->ngayketthuc}}</span>

                {{-- áp dụng --}}
                <div data-id="{{$voucher->id}}" class="apply-voucher-btn main-btn p-10">Áp dụng</div>
            </div>
        </div>
    </div>
</div>