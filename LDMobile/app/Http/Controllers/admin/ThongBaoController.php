<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\THONGBAO;
use App\Models\TAIKHOAN;
use Carbon\Carbon;
use DB;
class ThongBaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->admin='admin/content/';
    }
    public function index(Request $request)
    {
        //

        $listNotification = THONGBAO::paginate(10);
        if($request->ajax()){
            $html =  '';
            foreach($listNotification as $notification){
                $color = "red";
                $status ="Chưa đọc";
                if($notification->trangthaithongbao==1){
                    $status ="Đã đọc";
                    $color = "green";
                }
             
                $html .= 
                '<tr data-id="'.$notification->id.'">
                <td class="vertical-center w-10">'.$notification->id.'</td>
                <td class="vertical-center w-20">'.$notification->id_tk.'</td>
                <td class="vertical-center w-25">'.$notification->tieude.'</td>
                <td class="vertical-center w-20">'.$notification->noidung.'</td>
                <td class="vertical-center w-30">'.$notification->thoigian.'</td>
                <td class="vertical-center w-15" style="color:'. $color.'">'.$status.'</td>
                {{-- nút --}}
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="'.$notification->id.'" class="edit-notification-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                        <div data-id="'.$notification->id.'" data-object="notification" class="delete-notification-btn delete-btn">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>';
            }
            return $html;
        }
        $listAccount = TAIKHOAN::all();
        return view($this->admin."thong-bao", compact('listNotification','listAccount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $notification = new THONGBAO();
        $notification->tieude = $request->title;
        $notification->noidung = $request->content;
        $notification->thoigian = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i');
        $notification->id_tk = $request->account;
        $notification->trangthaithongbao = 0;
        if($notification->save()){
            $html = '<tr data-id="'.$notification->id.'">
            <td class="vertical-center w-10">'.$notification->id.'</td>
            <td class="vertical-center w-20">'.$notification->id_tk.'</td>
            <td class="vertical-center w-25">'.$notification->tieude.'</td>
            <td class="vertical-center w-20">'.$notification->noidung.'</td>
            <td class="vertical-center w-30">'.$notification->thoigian.'</td>
            <td class="vertical-center w-15" style="color:green">Chưa đọc</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$notification->id.'" class="edit-notification-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                    <div data-id="'.$notification->id.'" data-object="notification" class="delete-notification-btn delete-btn">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
            return $html;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $notification = THONGBAO::find($id);
        return $notification;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $notification = THONGBAO::find($id);
        $notification->tieude = $request->title;
        $notification->noidung = $request->content;
        $notification->id_tk = $request->account;
        $notification->trangthaithongbao = $request->status;
        if($notification->update()){
            $color = "red";
                $status ="Chưa đọc";
                if($notification->trangthaithongbao==1){
                    $status ="Đã đọc";
                    $color = "green";
                }
            
            $html = '<tr data-id="'.$notification->id.'">
            <td class="vertical-center w-10">'.$notification->id.'</td>
            <td class="vertical-center w-20">'.$notification->id_tk.'</td>
            <td class="vertical-center w-25">'.$notification->tieude.'</td>
            <td class="vertical-center w-20">'.$notification->noidung.'</td>
            <td class="vertical-center w-30">'.$notification->thoigian.'</td>
            <td class="vertical-center w-15" style="color:'. $color.'">'.$status.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$notification->id.'" class="edit-notification-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                    <div data-id="'.$notification->id.'" data-object="notification" class="delete-notification-btn delete-btn">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
            return $html;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $notification = THONGBAO::find($id);
        if($notification->delete()){
            return true;
        }
    }
    public function searchNotification(Request $request){
        $listNotification = THONGBAO::where('id', $request->search)->orWhere('id_tk',$request->search)->get();
        $html =  '<tbody id="lst_notification">';
        foreach($listNotification as $notification){
            $color = "red";
                $status ="Chưa đọc";
                if($notification->trangthaithongbao==1){
                    $status ="Đã đọc";
                    $color = "green";
                }
           
            $html .= 
            '<tr data-id="'.$notification->id.'">
            <td class="vertical-center w-10">'.$notification->id.'</td>
            <td class="vertical-center w-20">'.$notification->id_tk.'</td>
            <td class="vertical-center w-25">'.$notification->tieude.'</td>
            <td class="vertical-center w-20">'.$notification->noidung.'</td>
            <td class="vertical-center w-30">'.$notification->thoigian.'</td>
            <td class="vertical-center w-15" style="color:'. $color.'">'.$status.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$notification->id.'" class="edit-notification-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                    <div data-id="'.$notification->id.'" data-object="notification" class="delete-notification-btn delete-btn">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
        }
        $html .='</tbody>';
        return $html;
    }
    public function filterNotification(Request $request){
        if(!empty($request->dateStart)&&!empty($request->dateEnd)){
            $listNotification = THONGBAO::where(DB::raw("date_format(STR_TO_DATE(thoigian, '%d/%m/%Y'),'%Y-%m-%d')"),">=", $request->dateStart)->where(DB::raw("date_format(STR_TO_DATE(thoigian, '%d/%m/%Y'),'%Y-%m-%d')"),"<=", $request->dateEnd)->where('trangthaithongbao', $request->status)->get();
        }else if(!empty($request->dateStart)){
            $listNotification = THONGBAO::where(DB::raw("date_format(STR_TO_DATE(thoigian, '%d/%m/%Y'),'%Y-%m-%d')"),">=", $request->dateStart)->where('trangthaithongbao', $request->status)->get();
        }else if(!empty($request->dateEnd)){
            $listNotification = THONGBAO::where(DB::raw("date_format(STR_TO_DATE(thoigian, '%d/%m/%Y'),'%Y-%m-%d')"),">=", $request->dateEnd)->where('trangthaithongbao', $request->status)->get();
        }else  $listNotification = THONGBAO::where('trangthaithongbao', $request->status)->get();
        $html =  '<tbody id="lst_notification">';
        foreach($listNotification as $notification){
            $color = "red";
            $status ="Chưa đọc";
            if($request->status==1){
                $status ="Đã đọc";
                    $color = "green";
            }
            
            $html .= 
            '<tr data-id="'.$notification->id.'">
            <td class="vertical-center w-10">'.$notification->id.'</td>
            <td class="vertical-center w-20">'.$notification->id_tk.'</td>
            <td class="vertical-center w-25">'.$notification->tieude.'</td>
            <td class="vertical-center w-20">'.$notification->noidung.'</td>
            <td class="vertical-center w-30">'.$notification->thoigian.'</td>
            <td class="vertical-center w-15" style="color:'. $color.'">'.$status.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$notification->id.'" class="edit-notification-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                    <div data-id="'.$notification->id.'" data-object="notification" class="delete-notification-btn delete-btn">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
        }
        $html .='</tbody>';
        return $html;
    }
}
