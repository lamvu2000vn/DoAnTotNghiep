<tr>
    {{-- mã đơn hàng --}}
    <td class='vertical-center'>
        <div class='p-20'>
            <a href="{{route('user/tai-khoan-chi-tiet-don-hang', ['id' => $order->id])}}">{{$order->id}}</a>
        </div>
    </td>
    {{-- ngày mua --}}
    <td class='vertical-center'>
        <div>{{explode(' ', $order->thoigian)[0]}}</div>
    </td>
    {{-- sản phẩm mua --}}
    <td class='w-40 vertical-center'>
        <div class='pt-15 pb-15'>
            <div class='d-flex'>
                <img src="{{$url_phone.$detail[0]['sanpham']['hinhanh']}}" alt="" width="110px" class="mr-5">
                <div class="d-flex flex-column">
                    {{-- tên sp --}}
                    <div class="fw-600">{{$detail[0]['sanpham']['tensp']}}</div>
                    {{-- màu sắc --}}
                    <div class="fz-14">Màu sắc: {{$detail[0]['sanpham']['mausac']}}</div>
                    {{-- số lượng --}}
                    <div class="fz-14">Số lượng: {{$detail[0]['sl']}}{{count($detail) > 1 ? ' ... và '.(count($detail) - 1).' sản phẩm khác' : ''}}</div>
                    {{-- chi tiết --}}
                    <a href="{{route('user/tai-khoan-chi-tiet-don-hang', ['id' => $order->id])}}">Xem chi tiết</a>
                </div>
            </div>
        </div>
    </td>
    {{-- giá  --}}
    <td class='vertical-center'>
        <div>{{number_format($order->tongtien, 0, '', '.')}}<sup>đ</sup></div>
    </td>
    {{-- trạng thái đơn hàng --}}
    <td class='vertical-center'>
        <div>{{$order->trangthaidonhang}}</div>
    </td>
</tr>