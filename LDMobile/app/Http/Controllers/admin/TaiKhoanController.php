<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;
use App\Models\TAIKHOAN;
use Hash;
use App\Classes\Helper;
class TaiKhoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->admin='admin/content/';
        $this->IndexController = new IndexController;
    }
    public function index(Request $request)
    {
        $listAccount = TAIKHOAN::paginate(10);
            if($request->ajax()){
                $html = "";
            foreach($listAccount as $user){
                $color = "";
                $status = "";
                $cate = "";
                $colorDelete ="";
                if($user->trangthai==1){
                    $status = "Hoạt động";
                    $color = "green";
                    $colorDelete = "red";
                }else {
                    $color = "red";
                    $status = "Khóa";
                    $colorDelete = "gray";
                }
                if($user->loaitk==1){
                    $cate ="admin";
                }else $cate ="user";

                $html .= '<tr data-id="'.$user->id.'">
                <td class="vertical-center w-10">'.$user->id.'</td>
                <td class="vertical-center w-20">'.$user->sdt.'</td>
                <td class="vertical-center w-20">'.$user->email.'</td>
                <td class="vertical-center w-20">'.$cate.'</td>
                <td class="vertical-center w-10">'.$user->htdn.'</td>
                <td class="vertical-center w-55" style="color: '.$color.'">'.$status.'</td>
                {{-- nút --}}
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="'.$user->id.'" class="info-taikhoan-modal-show info-btn"><i class="fas fa-info"></i></div>
                        <div data-id="'.$user->id.'" class="edit-taikhoan-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                        <div data-id="'.$user->id.'" data-object="taikhoan" data-status="'.$user->trangthai.'" class="delete-taikhoan-btn delete-btn" style="background-color:'.$colorDelete.'">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
                    </tr>';
            }
            return $html;
           
        }
        return view($this->admin."tai-khoan", compact('listAccount'));
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
        if($request->hasFile('image')){
            $user = new TAIKHOAN([
                'hoten' => $request->hoten,
                'sdt' => $request->sdt,
                'password' => Hash::make($request->password),
                'anhdaidien' => Helper::imageUpload($request),
                'loaitk'=>$request->loaitk,
                'htdn'=>'normal',
                'trangthai'=>$request->trangthai,
            ]);
        }else {
            $user = new TAIKHOAN([
                'hoten' => $request->hoten,
                'sdt' => $request->sdt,
                'password' => Hash::make($request->password),
                'anhdaidien' => 'avatar-default.png',
                'loaitk'=>$request->loaitk,
                'htdn'=>'normal',
                'trangthai'=>$request->trangthai,
            ]);
        }
        $color = "";
        $status = "";
        $cate = "";
        $colorDelete ="";
        if($user->save()){
            if($user->trangthai==1){
                $status = "Hoạt động";
                $color = "green";
                $colorDelete = "red";
            }else {
                $color = "red";
                $status = "Khóa";
                 $colorDelete = "gray";
            }
            if($user->loaitk==1){
                $cate ="admin";
            }else $cate ="user";

            $html = '<tr data-id="'.$user->id.'">
            <td class="vertical-center w-10">'.$user->id.'</td>
            <td class="vertical-center w-20">'.$user->sdt.'</td>
            <td class="vertical-center w-20">'.$user->email.'</td>
            <td class="vertical-center w-20">'.$cate.'</td>
            <td class="vertical-center w-10">'.$user->htdn.'</td>
            <td class="vertical-center w-55" style="color: '.$color.'">'.$status.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$user->id.'" class="info-taikhoan-modal-show info-btn"><i class="fas fa-info"></i></div>
                    <div data-id="'.$user->id.'" class="edit-taikhoan-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                    <div data-id="'.$user->id.'" data-object="taikhoan" data-status="'.$user->trangthai.'" class="delete-taikhoan-btn delete-btn" style="background-color:'.$colorDelete.'">
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
        $account = TAIKHOAN::find($id);
        return $account;
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
        $user = TAIKHOAN::find($id);
        $user->hoten = $request->hoten;
        $user->email = $request->email;
        $user->loaitk = $request->loaitk;
        $user->trangthai = $request->trangthai;
        if($request->hasFile('image')){
            $user->anhdaidien = Helper::imageUpload($request);
        }
        $color = "";
        $status = "";
        $cate = "";
        $colorDelete ="";
        if($user->update()){
            // cập nhật session user
            $this->IndexController->userSessionUpdate();

            if($user->trangthai==1){
                $status = "Hoạt động";
                $color = "green";
                $colorDelete = "red";
            }else {
                $color = "red";
                $status = "Khóa";
                 $colorDelete = "gray";
            }
            if($user->loaitk==1){
                $cate ="admin";
            }else $cate ="user";

            $html = '<tr data-id="'.$user->id.'">
            <td class="vertical-center w-10">'.$user->id.'</td>
            <td class="vertical-center w-20">'.$user->sdt.'</td>
            <td class="vertical-center w-20">'.$user->email.'</td>
            <td class="vertical-center w-20">'.$cate.'</td>
            <td class="vertical-center w-10">'.$user->htdn.'</td>
            <td class="vertical-center w-55" style="color: '.$color.'">'.$status.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$user->id.'"class="info-taikhoan-modal-show info-btn"><i class="fas fa-info"></i></div>
                    <div data-id="'.$user->id.'" class="edit-taikhoan-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                    <div data-id="'.$user->id.'" data-object="taikhoan" data-status="'.$user->trangthai.'" class="delete-taikhoan-btn delete-btn" style="background-color:'.$colorDelete.'">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
        $result = [$html, $user];
        return $result;
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
        $user = TAIKHOAN::find($id);
        $user->trangthai = 0;
        $color = "";
        $status = "";
        $cate = "";
        $colorDelete ="";
        if($user->update()){
            if($user->trangthai==1){
                $status = "Hoạt động";
                $color = "green";
                $colorDelete = "red";
            }else {
                $color = "red";
                $status = "Khóa";
                 $colorDelete = "gray";
            }
            if($user->loaitk==1){
                $cate ="admin";
            }else $cate ="user";
            $html = '<tr data-id="'.$user->id.'">
            <td class="vertical-center w-10">'.$user->id.'</td>
            <td class="vertical-center w-20">'.$user->sdt.'</td>
            <td class="vertical-center w-20">'.$user->email.'</td>
            <td class="vertical-center w-20">'.$cate.'</td>
            <td class="vertical-center w-10">'.$user->htdn.'</td>
            <td class="vertical-center w-55" style="color: '.$color.'">'.$status.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$user->id.'" class="info-taikhoan-modal-show info-btn"><i class="fas fa-info"></i></div>
                    <div data-id="'.$user->id.'" class="edit-taikhoan-modal-show edit-btn"><i class="fas fa-pen"></i></div>
                    <div data-id="'.$user->id.'" data-object="taikhoan" data-status="'.$user->trangthai.'" class="delete-taikhoan-btn delete-btn" style="background-color:'.$colorDelete.'">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
        return $html;
        }
    }
    public function checkPhone(Request $request){
        $valid = TAIKHOAN::where('sdt', $request->sdt)->get();
        $size = count($valid);
        return $size;
    }
    public function searchName(Request $request){
        $listAccount = TAIKHOAN::where('id',$request->search)->orWhere('sdt','like', "%".$request->search."%")->orWhere('email','like', "%".$request->search."%")->orWhere('hoten','like', "%".$request->search."%")->get();

        $html =  '<tbody id="lst_taikhoan">';
        foreach($listAccount as $account){
            $color = "";
            $status = "";
            $cate = "";
            $colorDelete ="";
            if($account->trangthai==1){
                    $status = "Hoạt động";
                    $color = "green";
                    $colorDelete = "red";
            }else{
                    $color = "red";
                    $status = "Khóa";
                    $colorDelete = "gray";
            }
            if($account->loaitk==1){
                    $cate ="admin";
            }else $cate ="user";
            $html .= 
            '<tr data-id="'.$account->id.'">
            <td class="vertical-center w-10">'.$account->id.'</td>
            <td class="vertical-center w-20">'.$account->sdt.'</td>
            <td class="vertical-center w-20">'.$account->email.'</td>
            <td class="vertical-center w-20">'.$cate.'</td>
            <td class="vertical-center w-10">'.$account->htdn.'</td>
            <td class="vertical-center w-55" style="color: '.$color.'">'.$status.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$account->id.'" class="info-taikhoan-modal-show info-btn"><i class="fas fa-info"></i></div>
                    <div data-id="'.$account->id.'" class="edit-taikhoan-modal-show edit-btn"><i class="fas fa-pen"></i></div>';
                    if($request->idUser!=$account->id){
                        $html .=   '<div data-id="'.$account->id.'" data-object="taikhoan" data-status="'.$account->trangthai.'" class="delete-taikhoan-btn delete-btn" style="background-color:'.$colorDelete.'">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
                    }else{
                        $html .=   '<div data-id="'.$account->id.'" data-object="taikhoan" data-status="'.$account->trangthai.'" data-user="'.$request->idUser.'" class="delete-taikhoan-btn delete-btn" style="background-color: gray">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
                    }
                    
        }
        $html .='</tbody>';
        return $html;
    }
    public function filterAccount(Request $request){
        $listAccount = TAIKHOAN::where('htdn', $request->formality)->where('loaitk', $request->cate)->where('trangthai', $request->status)->get();
        $html =  '<tbody id="lst_taikhoan">';
        foreach($listAccount as $account){
            $color = "";
            $status = "";
            $cate = "";
            $colorDelete ="";
            if($account->trangthai==1){
                    $status = "Hoạt động";
                    $color = "green";
                    $colorDelete = "red";
            }else{
                    $color = "red";
                    $status = "Khóa";
                    $colorDelete = "gray";
            }
            if($account->loaitk==1){
                    $cate ="admin";
            }else $cate ="user";
            $html .= 
            '<tr data-id="'.$account->id.'">
            <td class="vertical-center w-10">'.$account->id.'</td>
            <td class="vertical-center w-20">'.$account->sdt.'</td>
            <td class="vertical-center w-20">'.$account->email.'</td>
            <td class="vertical-center w-20">'.$cate.'</td>
            <td class="vertical-center w-10">'.$account->htdn.'</td>
            <td class="vertical-center w-55" style="color: '.$color.'">'.$status.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$account->id.'" class="info-taikhoan-modal-show info-btn"><i class="fas fa-info"></i></div>
                    <div data-id="'.$account->id.'" class="edit-taikhoan-modal-show edit-btn"><i class="fas fa-pen"></i></div>';
                    if($request->idUser!=$account->id){
                        $html .=   '<div data-id="'.$account->id.'" data-object="taikhoan" data-status="'.$account->trangthai.'" class="delete-taikhoan-btn delete-btn" style="background-color:'.$colorDelete.'">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
                    }else{
                        $html .=   '<div data-id="'.$account->id.'" data-object="taikhoan" data-status="'.$account->trangthai.'" data-user="'.$request->idUser.'" class="delete-taikhoan-btn delete-btn" style="background-color: gray">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
                    }
        }
        $html .='</tbody>';
        return $html;
    }
    
}
