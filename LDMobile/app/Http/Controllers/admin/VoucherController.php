<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\VOUCHER;
use App\Models\TAIKHOAN_VOUCHER;

class VoucherController extends Controller
{
    public function __construct()
    {
        $this->admin = 'admin/content/';
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->IndexController = new IndexController;
    }

    public function index()
    {
        $lst_voucher = VOUCHER::limit(10)->get();
        foreach($lst_voucher as $i => $key){
            // trạng thái
            $dateEnd = strtotime(str_replace('/', '-', $key->ngayketthuc));
            $currentDate = strtotime(date('d-m-Y'));
            $lst_voucher[$i]->trangthai = $dateEnd >= $currentDate ? 1 : 0;
        }

        $data = [
            'lst_voucher' => $lst_voucher,
        ];

        return view($this->admin.'voucher')->with($data);
    }

    public function store(Request $request)
    {
        if($request->ajax()){
            $data = [
                'code' => $request->code,
                'noidung' => $request->noidung,
                'chietkhau' => $request->chietkhau,
                'dieukien' => $request->dieukien ? $request->dieukien : 0,
                'ngaybatdau' => date('d/m/Y', strtotime($request->ngaybatdau)),
                'ngayketthuc' => date('d/m/Y', strtotime($request->ngayketthuc)),
                'sl' => $request->sl,
            ];

            if(VOUCHER::where('code', $data['code'])->first()){
                return 'exists';
            }

            $create = VOUCHER::create($data);

            // trạng thái
            $dateEnd = strtotime(str_replace('/', '-', $create->ngayketthuc));
            $currentDate = strtotime(date('d-m-Y'));
            $status = $dateEnd >= $currentDate ? 'Hoạt động' : 'Hết hạn';

            $create->status = $status;

            return [
                'id' => $create->id,
                'data' => [$create]
            ];
        }
    }

    public function update(Request $request, $id)
    {
        if($request->ajax()){
            $data = [
                'code' => $request->code,
                'noidung' => $request->noidung,
                'chietkhau' => $request->chietkhau,
                'dieukien' => $request->dieukien ? $request->dieukien : 0,
                'ngaybatdau' => date('d/m/Y', strtotime($request->ngaybatdau)),
                'ngayketthuc' => date('d/m/Y', strtotime($request->ngayketthuc)),
                'sl' => $request->sl,
            ];
    
            $oldData = VOUCHER::find($id);
    
            // voucher đã tồn tại
            if($oldData->code != $data['code']){
                if(VOUCHER::where('code', $data['code'])->first()){
                    return 'exists';
                }
            }
    
            VOUCHER::where('id', $id)->update($data);

            $newRow = VOUCHER::find($id);

            // trạng thái
            $dateEnd = strtotime(str_replace('/', '-', $newRow->ngayketthuc));
            $currentDate = strtotime(date('d-m-Y'));
            $status = $dateEnd >= $currentDate ? 'Hoạt động' : 'Hết hạn';

            $newRow->status = $status;
    
            return [$newRow];
        }
    }

    public function destroy($id)
    {
        // xóa voucher của người dùng
        TAIKHOAN_VOUCHER::where('id_vc', $id)->delete();
        // xóa voucher
        VOUCHER::destroy($id);
    }

    public function AjaxGetVoucher(Request $request)
    {
        if($request->ajax()){
            $voucher = VOUCHER::find($request->id);
            $voucher->ngaybatdau = date('Y-m-d', strtotime(str_replace('/', '-', $voucher->ngaybatdau)));
            $voucher->ngayketthuc = date('Y-m-d', strtotime(str_replace('/', '-', $voucher->ngayketthuc)));

            return $voucher;
        }
    }

    public function AjaxSearch(Request $request)
    {
        if($request->ajax()){
            $keyword = $this->IndexController->unaccent($request->keyword);
            $lst_result = [];

            if($keyword == ''){
                $lst_voucher = VOUCHER::limit(10)->get();
                foreach($lst_voucher as $key){
                    // trạng thái
                    $dateEnd = strtotime(str_replace('/', '-', $key->ngayketthuc));
                    $currentDate = strtotime(date('d-m-Y'));
                    $status = $dateEnd >= $currentDate ? 'Hoạt động' : 'Hết hạn';

                    $key->status = $status;
                }

                return $lst_voucher;
            }

            foreach(VOUCHER::all() as $key){
                $dateEnd = strtotime(str_replace('/', '-', $key->ngayketthuc));
                $currentDate = strtotime(date('d-m-Y'));
                $status = $dateEnd >= $currentDate ? 'Hoạt động' : 'Hết hạn';

                $string = strtolower($this->IndexController->unaccent($key->id.$key->code.$key->chietkhau*100 .'%'.$key->ngaybatdau.$key->ngayketthuc.$key->sl.$status));
                if(str_contains($string, $keyword)){
                    $key->status = $status;
                    array_push($lst_result, $key);
                }
            }
            return $lst_result;
        }
    }
}
