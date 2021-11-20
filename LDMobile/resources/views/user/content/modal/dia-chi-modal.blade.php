{{-- modal thêm|sửa địa chỉ --}}
<div class="modal fade" id="address-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-8 mx-auto pt-50 pb-50">
                    <h3 id="address-modal-title" class="text-end"></h3>
                    <hr class="mt-5 mb-40">
                    {{-- create/edit --}}
                    <input type="hidden" name="address_type">
                    {{-- id --}}
                    <input type="hidden" name="tk_dc_id">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="fw-600 mb-5">Họ và Tên</label>
                                <input type="text" name="adr_fullname_inp">
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="fw-600 mb-5">Số điện thoại nhận hàng</label>
                                <input type="text" name="adr_tel_inp" maxlength="10">
                            </div>
                        </div>
                    </div>
                    {{-- khu vực --}}
                    <div class="mb-3">
                        <label class="fw-600 mb-5">Địa chỉ</label>
                        <div class="row">
                            {{-- chọn tỉnh thành --}}
                            <div class='col-md-6 mb-3'>
                                <div class="select">
                                    <div id='TinhThanh-selected' class="select-selected">
                                        <div id='TinhThanh-name'>Chọn Tỉnh / Thành phố</div>
                                        <div class="spinner-border select-spinner" role="status"></div>
                                    </div>
                                    <div id='TinhThanh-box' class="select-box">
                                        {{-- tìm kiếm --}}
                                        <div class="select-search">
                                            <input id='search-tinh-thanh' type="text" class="select-search-inp" placeholder="Nhập tên Tỉnh / Thành">
                                            <i class="select-search-icon far fa-search"></i>
                                        </div>

                                        {{-- option --}}
                                        <div id='list-tinh-thanh' class="select-option"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- chọn quận huyện --}}
                            <div class='col-md-6 mb-3'>
                                <div class="select">
                                    <div id='QuanHuyen-selected' class="select-selected">
                                        <div id='QuanHuyen-name'>Chọn Quận / Huyện</div>
                                        <div class="spinner-border select-spinner" role="status"></div>
                                    </div>
                                    <div id='QuanHuyen-box' class="select-box">
                                        {{-- tìm kiếm --}}
                                        <div class="select-search">
                                            <input id='search-quan-huyen' type="text" class="select-search-inp" placeholder="Nhập tên Quận / Huyện">
                                            <i class="select-search-icon far fa-search"></i>
                                        </div>

                                        {{-- option --}}
                                        <div id='list-quan-huyen' class="select-option"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- chọn phường xã --}}
                            <div class='col-md-6 mb-3'>
                                <div class="select">
                                    <div id='PhuongXa-selected' class="select-disable">
                                        <div id="PhuongXa-name">Chọn Phường / Xã</div>
                                        <i class="far fa-chevron-down fz-14"></i>
                                    </div>
                                    <div id='PhuongXa-box' class="select-box">
                                        {{-- tìm kiếm --}}
                                        <div class="select-search">
                                            <input id='search-phuong-xa' type="text" class="select-search-inp" placeholder="Nhập tên Phường / Xã">
                                            <i class="select-search-icon far fa-search"></i>
                                        </div>

                                        {{-- option --}}
                                        <div id='list-phuong-xa' class="select-option"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- số nhà, tên đường --}}
                            <div class='col-md-6 mb-3'>
                                <input name="address_inp" type="text" placeholder="Số nhà, tên đường" required>
                            </div>
                        </div>
                    </div>

                    {{-- đặt mặc định --}}
                    <div class="mb-3">
                        <input type="checkbox" id="set_default_address" name="set_default_address">
                        <label for="set_default_address">Đặt làm địa chỉ mặc định</label>
                    </div>

                    {{-- nút --}}
                    <div class="row mb-3">
                        <div class="d-flex justify-content-end">
                            <div class="cancel-btn mr-10" data-bs-dismiss="modal">Hủy</div>
                            <div class="address-action-btn main-btn p-10"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>