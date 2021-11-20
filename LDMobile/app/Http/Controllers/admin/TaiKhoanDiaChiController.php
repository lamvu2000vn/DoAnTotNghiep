<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TAIKHOAN_DIACHI;
class TaiKhoanDiaChiController extends Controller
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
        $listAccountAddress = TAIKHOAN_DIACHI::paginate(10);
        if($request->ajax()){
            $html = ""; 
            foreach($listAccountAddress as $address){
                $html .= 
                '<tr data-id="'.$address->id.'">
                <td class="vertical-center w-10">'.$address->id.'</td>
                <td class="vertical-center w-25">'.$address->id_tk.'</td>
                <td class="vertical-center w-25">'.$address->hoten.'</td>
                <td class="vertical-center w-25">'.$address->sdt.'</td>
                <td class="vertical-center w-25">'.$address->macdinh.'</td>
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="'.$address->id.'" class="info-account-address-btn info-btn"><i class="fas fa-info"></i></div>
                        <div data-id="'.$address->id.'" data-object="accountaddress" class="delete-account-address-btn delete-btn">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>';
            }
            return $html;
        }
        return view($this->admin."tai-khoan-dia-chi", compact('listAccountAddress'));
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
        $accountAddress = TAIKHOAN_DIACHI::find($id);
        return  $accountAddress;
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
        $accountAddress = TAIKHOAN_DIACHI::find($id);
        $accountAddress->delete();
        return null;
    }
    public function searchAccountAddress(Request $request){
        $listAccountAddress = TAIKHOAN_DIACHI::where('id', $request->search)->orWhere('sdt','like','%'.$request->search.'%')->orWhere('hoten','like','%'.$request->search.'%')->orWhere('id_tk','like','%'.$request->search.'%')->get();
        $html =  '<tbody id="lst_review">';
        foreach($listAccountAddress as $address){
            $html .= 
            '<tr data-id="'.$address->id.'">
            <td class="vertical-center w-10">'.$address->id.'</td>
            <td class="vertical-center w-25">'.$address->id_tk.'</td>
            <td class="vertical-center w-25">'.$address->hoten.'</td>
            <td class="vertical-center w-25">'.$address->sdt.'</td>
            <td class="vertical-center w-25">'.$address->macdinh.'</td>
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$address->id.'" class="info-account-address-btn info-btn"><i class="fas fa-info"></i></div>
                    <div data-id="'.$address->id.'" data-object="accountaddress" class="delete-account-address-btn delete-btn">
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
