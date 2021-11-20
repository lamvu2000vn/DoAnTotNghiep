<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TAIKHOAN_VOUCHER;
class TaiKhoanVoucherController extends Controller
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
        $listTaiKhoanVoucher = TAIKHOAN_VOUCHER::paginate(10);
        if($request->ajax()){
            $html =  '';
            foreach($listTaiKhoanVoucher as $voucher){
                $html .= 
                '<tr data-id="'.$voucher->id.'">
                <td class="vertical-center w-10">'.$voucher->id.'</td>
                <td class="vertical-center w-25">'.$voucher->id_tk.'</td>
                <td class="vertical-center w-25">'.$voucher->id_vc.'</td>

                {{-- nút --}}
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="'.$voucher->id.'" data-object="accountvoucher" class="delete-account-voucher-btn delete-btn">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>';
            }
            return $html;
        }
        return view($this->admin."tai-khoan-voucher", compact('listTaiKhoanVoucher'));
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
        $v = TAIKHOAN_VOUCHER::find($id);
        $v->delete();
        return null;
    }
    public function searchAccountVoucher(Request $request){
        $listAccountVoucher = TAIKHOAN_VOUCHER::where('id', $request->search)->orWhere('id_tk',$request->search)->get();
        $html =  ' <tbody id="lst_account_voucher">';
        foreach($listAccountVoucher as $voucher){
            $html .= 
            '<tr data-id="'.$voucher->id.'">
            <td class="vertical-center w-10">'.$voucher->id.'</td>
            <td class="vertical-center w-25">'.$voucher->id_tk.'</td>
            <td class="vertical-center w-25">'.$voucher->id_vc.'</td>

            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$voucher->id.'" data-object="accountvoucher" class="delete-account-voucher-btn delete-btn">
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
