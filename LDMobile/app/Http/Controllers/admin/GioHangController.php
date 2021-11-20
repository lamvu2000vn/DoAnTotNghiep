<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GIOHANG;

class GioHangController extends Controller
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
        $listCart = GIOHANG::paginate(10);
        if($request->ajax()){
                $html =  '';
            foreach($listCart as $cart){
                $html .= 
                '<tr data-id="'.$cart->id.'">
                <td class="vertical-center w-10">'.$cart->id.'</td>
                <td class="vertical-center w-10">'.$cart->id_tk.'</td>
                <td class="vertical-center w-20">'.$cart->id_sp.'</td>
                <td class="vertical-center w-30">'.$cart->sl.'</td>
                {{-- nút --}}
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="'.$cart->id.'" data-object="cart" class="delete-cart-btn delete-btn">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>';
            }
            return $html;
        }
        return view($this->admin."gio-hang", compact('listCart'));
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
        $giohang = GIOHANG::find($id);
        $giohang->delete();
        return null;
    }
    public function searchCart(Request $request){
        $listCart= GioHang::where('id', $request->search)->orWhere('id_tk',$request->search)->orWhere('id_sp',$request->search)->get();
        $html =  ' <tbody id="lst_cart">';
        foreach($listCart as $cart){
            $html .= 
            '<tr data-id="'.$cart->id.'">
            <td class="vertical-center w-10">'.$cart->id.'</td>
            <td class="vertical-center w-10">'.$cart->id_tk.'</td>
            <td class="vertical-center w-20">'.$cart->id_sp.'</td>
            <td class="vertical-center w-30">'.$cart->sl.'</td>
            {{-- nút --}}
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$cart->id.'" data-object="cart" class="delete-cart-btn delete-btn">
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
