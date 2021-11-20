@extends("admin.layout")
@section("sidebar-imei") sidebar-link-selected @stop
@section("content-title") IMEI @stop
@section("content")

<div class="white-bg p-20">
    {{-- function button --}}
    <div class="d-flex justify-content-end align-items-center mb-20">

        {{-- filter & sort --}}
        <div class="d-flex">
            {{-- search --}}
            <div class='relative'>
                <div class="head-input-grp">
                    <input type="text" id="search" placeholder="Tìm kiếm">
                    <span class='input-icon-right'><i class="fal fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>IMEI</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody id="lst_data">
            @foreach ($lst_imei as $key)
                <tr data-id="{{$key->id}}">
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->id}}</div>
                    </td>
                    <td class="vertical-center">
                        <div class="d-flex pt-10 pb-10">
                            <img src="{{$url_phone.$key->product->hinhanh}}" alt="" width="70px">
                            <div class="ml-10">
                                <div class="d-flex align-items-center fw-600">
                                    {{$key->product->tensp}}
                                    <i class="fas fa-circle fz-5 ml-5 mr-5"></i>
                                    {{$key->product->mausac}}
                                </div>
                                <div>Ram: {{$key->product->ram}}</div>
                                <div>Dung lượng: {{$key->product->dungluong}}</div>
                            </div>
                        </div>
                    </td>
                    <td class="vertical-center">
                        <div class="pt-10 pb-10">{{$key->imei}}</div>
                    </td>
                    <td class="vertical-center">
                        <div data-id="{{$key->id}}" class="trangthai pt-10 pb-10">{{$key->trangthai == 1 ? 'Đã kích hoạt' : 'Chưa kích hoạt'}}</div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div id="loadmore" class="text-center"><div class="spinner-border loadmore" role="status"></div></div>
</div>

<div id="toast"></div>

@stop