<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DANHGIASP;
use App\Models\CTDG;
use App\Models\TAIKHOAN;
use App\Models\LUOTTHICH;
use App\Models\SANPHAM;
use App\Models\PHANHOI;
use DB;
class DanhGiaController extends Controller
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
        $listReview = DANHGIASP::paginate(10);
        foreach($listReview as $review){
            $product = SANPHAM::find($review->id_sp);
            $user = TAIKHOAN::find($review->id_tk);
            $review->tensp = $product->tensp.' '.$product->dungluong.' '. $product->mausac;
            $review->tentaikhoan = $user->hoten;
        }
        if($request->ajax()){
            $html = "";
            foreach($listReview as $review){
                $product = SANPHAM::find($review->id_sp);
                $html .= 
                '<tr data-id="'.$review->id.'">
                <td class="vertical-center w-10">'.$review->id.'</td>
                <td class="vertical-center w-10">'.$review->id_tk.'</td>
                <td class="vertical-center w-20">'.$product->tensp.'</td>
                <td class="vertical-center w-20">'.$review->noidung.'</td>
                <td class="vertical-center w-10">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$review->id.'" class="info-reply-btn info-reply-btn"><i class="far fa-list-alt"></i></div>
                </div>
                </td>
                <td class="vertical-center w-15">
                    <div class="d-flex justify-content-evenly">
                        <div data-id="'.$review->id.'" class="info-review-btn info-btn"><i class="fas fa-info"></i></div>
                        <div data-id="'.$review->id.'" data-object="review" class="delete-review-btn delete-btn">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </td>
            </tr>';
            }
            return $html;
        }
        return view($this->admin."danh-gia", compact("listReview"));
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
        $review = DANHGIASP::find($id);
        $usr = TAIKHOAN::find($review->id_tk);
        $likes = LUOTTHICH::where('id_dg', $id)->get();
        $pro = SANPHAM::find($review->id_sp);
        $like = count($likes);
        $images = CTDG::where('id_dg', $id)->get();
        $review->image = $images;
        $review->soluotthich = $like;
        $review->name = $usr->hoten;
        $review->nameProduct = $pro->tensp;
        return $review;
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
        $listReply = PHANHOI::where('id_dg', $id)->get();
        
        $danhgia = DANHGIASP::find($id);
    
        $danhgia->ctdg()->delete();
        $danhgia->phanhoi()->delete();
        $danhgia->luotthich()->delete();
        $danhgia->delete();
        return null;
    }
    public function searchReview(Request $request){
        $listReview = DANHGIASP::where('id', $request->search)->orWhere('noidung','like','%'.$request->search.'%')->orWhere('id_tk','like','%'.$request->search.'%')->get();
        $html =  '<tbody id="lst_review">';
        foreach($listReview as $review){
            $product = SANPHAM::find($review->id_sp);
            $html .= 
            '<tr data-id="'.$review->id.'">
            <td class="vertical-center w-10">'.$review->id.'</td>
            <td class="vertical-center w-10">'.$review->id_tk.'</td>
            <td class="vertical-center w-20">'.$product->tensp.'</td>
            <td class="vertical-center w-20">'.$review->noidung.'</td>
            <td class="vertical-center w-10">
            <div class="d-flex justify-content-evenly">
                <div data-id="'.$review->id.'" class="info-reply-btn info-reply-btn"><i class="far fa-list-alt"></i></div>
            </div>
            </td>
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$review->id.'" class="info-review-btn info-btn"><i class="fas fa-info"></i></div>
                    <div data-id="'.$review->id.'" data-object="review" class="delete-review-btn delete-btn">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </td>
        </tr>';
        }
        $html .='</tbody>';
        return $html;
    }
    public function filterReview(Request $request){
        if(!empty($request->dateStart)&&!empty($request->dateEnd)){
            $listReview = DANHGIASP::where(DB::raw("date_format(STR_TO_DATE(thoigian, '%d/%m/%Y'),'%Y-%m-%d')"),">=", $request->dateStart)->where(DB::raw("date_format(STR_TO_DATE(thoigian, '%d/%m/%Y'),'%Y-%m-%d')"),"<=", $request->dateEnd)->get();
        }else if(!empty($request->dateStart)){
            $listReview = DANHGIASP::where(DB::raw("date_format(STR_TO_DATE(thoigian, '%d/%m/%Y'),'%Y-%m-%d')"),">=", $request->dateStart)->get();
        }else if(!empty($request->dateEnd)){
            $listReview = DANHGIASP::where(DB::raw("date_format(STR_TO_DATE(thoigian, '%d/%m/%Y'),'%Y-%m-%d')"),">=", $request->dateEnd)->get();
        }else  $listReview = [];
        $html =  '<tbody id="lst_review">';
        foreach($listReview as $review){
            $product = SANPHAM::find($review->id_sp);
            $html .= 
            '<tr data-id="'.$review->id.'">
            <td class="vertical-center w-10">'.$review->id.'</td>
            <td class="vertical-center w-10">'.$review->id_tk.'</td>
            <td class="vertical-center w-20">'.$product->tensp.'</td>
            <td class="vertical-center w-20">'.$review->noidung.'</td>
            <td class="vertical-center w-10">
            <div class="d-flex justify-content-evenly">
                <div data-id="'.$review->id.'" class="info-reply-btn info-reply-btn"><i class="far fa-list-alt"></i></div>
            </div>
            </td>
            <td class="vertical-center w-15">
                <div class="d-flex justify-content-evenly">
                    <div data-id="'.$review->id.'" class="info-review-btn info-btn"><i class="fas fa-info"></i></div>
                    <div data-id="'.$review->id.'" data-object="review" class="delete-review-btn delete-btn">
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
