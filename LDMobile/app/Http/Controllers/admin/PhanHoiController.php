<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\user\IndexController;
use Illuminate\Http\Request;

use App\Models\PHANHOI;
use App\Models\TAIKHOAN;
class PhanHoiController extends Controller
{
    public function index()
    {
        
    }

    public function show($id)
    {
        $listReply = PHANHOI::where('id_dg', $id)->get();
        $html = '<tbody id="lst_reply">';
        foreach($listReply as $reply){
            $user = TAIKHOAN::find($reply->id_tk);
            $html .= '<tr data-id="'.$reply->id.'"><td class="vertical-center w-10">'.$user->hoten.'</td>
            <td class="vertical-center w-10">'.$reply->noidung.'</td>
            <td class="vertical-center w-10">'.$reply->thoigian.'</td>
            <td class="vertical-center w-15">
            <div class="d-flex justify-content-evenly">
                <div data-id="'.$reply->id.'" data-object="review" id="delete-reply-btn" class="delete-reply-btn delete-btn">
                    <i class="fas fa-trash"></i>
                </div>
            </div>
        </td>
            </tr>';
            
        }
        $html .='</tbody>';
        return $html;
    }

    public function destroy($id)
    {
        $reply = PHANHOI::find($id);
        $reply->delete();
        $listReply = PHANHOI::where('id_dg', $id)->get();
        return null;       
    }
}
