<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SP_YEUTHICH;
class SPYeuThichController extends Controller
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
        $listWishList = SP_YEUTHICH::paginate(10);
        if($request->ajax()){
            $html =  '';
            foreach($listWishList as $wishList){
                $html .= 
                '<tr data-id="'.$wishList->id.'">
                <td class="vertical-center w-10">'.$wishList->id.'</td>
                <td class="vertical-center w-25">'.$wishList->id_tk.'</td>
                <td class="vertical-center w-25">'.$wishList->id_sp.'</td>
                <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$wishList->id.'" data-object="wishList" class="delete-wishList-btn delete-btn">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
            </tr>';
            }
            return $html;
        }
        return view($this->admin."sp-yeu-thich", compact("listWishList"));
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
        $product = SP_YEUTHICH::find($id);
        $product->delete();
       
        return null;
    }
    public function searchWishList(Request $request){
        $listWishList = SP_YEUTHICH::where('id', $request->search)->orWhere('id_tk',$request->search)->get();
        $html =  '<tbody id="lst_wishlist">';
        foreach($listWishList as $wishList){
            $html .= 
            '<tr data-id="'.$wishList->id.'">
            <td class="vertical-center w-10">'.$wishList->id.'</td>
            <td class="vertical-center w-25">'.$wishList->id_tk.'</td>
            <td class="vertical-center w-25">'.$wishList->id_sp.'</td>
            <td class="vertical-center w-15">
            <div class="d-flex justify-content-evenly">
                <div data-id="'.$wishList->id.'" data-object="wishList" class="delete-wishList-btn delete-btn">
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
