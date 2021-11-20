<div class='detail-specifications'>Thông số kỹ thuật</div>
<table class='table border'>
    <tbody class='fz-14 '>
        <tr>
            <td class='w-40 center-td'>Màn hình</td>
            <td>
                {{  
                    ($specifications['man_hinh']['cong_nghe_mh'] ? $specifications['man_hinh']['cong_nghe_mh'] : $updating)
                    . ', ' .
                    ($specifications['man_hinh']['ty_le_mh'] ? $specifications['man_hinh']['ty_le_mh'] . '"' : $updating)
                }}
            </td>
        </tr>
        <tr>
            <td class='w-40 center-td'>Camera sau</td>
            <td>
                {{
                    $specifications['camera_sau']['do_phan_giai'] ? $specifications['camera_sau']['do_phan_giai'] : $updating
                }}
            </td>
        </tr>
        <tr>
            <td class='w-40 center-td'>Camera trước</td>
            <td>
                {{
                    $specifications['camera_truoc']['do_phan_giai'] ? $specifications['camera_truoc']['do_phan_giai'] : $updating
                }}
            </td>
        </tr>
        <tr>
            <td class='w-40 center-td'>Hệ điều hành</td>
            <td>
                {{
                    $specifications['HDH_CPU']['HDH'] ? $specifications['HDH_CPU']['HDH'] : $updating
                }}
            </td>
        </tr>
        <tr>
            <td class='w-40 center-td'>CPU</td>
            <td>
                {{
                    $specifications['HDH_CPU']['CPU'] ? $specifications['HDH_CPU']['CPU'] : $updating
                }}
            </td>
        </tr>
        <tr>
            <td class='w-40 center-td'>RAM</td>
            <td>
                {{
                    $specifications['luu_tru']['RAM'] ? $specifications['luu_tru']['RAM'] : $updating
                }}
            </td>
        </tr>
        <tr>
            <td class='w-40 center-td'>Bộ nhớ trong</td>
            <td>
                {{
                    $specifications['luu_tru']['bo_nho_trong'] ? $specifications['luu_tru']['bo_nho_trong'] : $updating
                }}
            </td>
        </tr>
        <tr>
            <td class='w-40 center-td'>SIM</td>
            <td>
                {{
                    ($specifications['ket_noi']['SIM'] ? $specifications['ket_noi']['SIM'] : $updating)
                    . ', ' .
                    ($specifications['ket_noi']['mang_mobile'] ? $specifications['ket_noi']['mang_mobile'] :$updating)
                }}
            </td>
        </tr>
        <tr>
            <td class='w-40 center-td'>Pin</td>
            <td>
                {{
                    ($specifications['pin']['loai'] ? $specifications['pin']['loai'] : $updating)
                    . ', ' .
                    ($specifications['pin']['dung_luong'] ? $specifications['pin']['dung_luong'] : $updating)
                }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class='main-btn w-100 p-10 fz-16' data-bs-toggle="modal" data-bs-target="#specifications-modal">Xem thêm</div>
            </td>
        </tr>
    </tbody>
</table>