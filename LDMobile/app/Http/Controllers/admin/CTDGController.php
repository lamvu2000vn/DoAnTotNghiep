<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CTDGController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $detailReview = CDTG::where('id_dg', $id)->get();
        $html = '<tbody id="lst_detail_review">';
        foreach($detailReview as $detail){
            $html .= '<td class="vertical-center w-10">'.$detail->id.'</td>
            <td class="vertical-center w-10">$review->id_tk</td>
            <td class="vertical-center w-20">$review->id_sp</td>
            <td class="vertical-center w-30">$review->noidung</td>
            <td class="vertical-center w-25">$review->trangthai</td>';
        }
    }
}
