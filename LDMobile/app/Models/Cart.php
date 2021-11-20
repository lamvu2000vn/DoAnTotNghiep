<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public $products = null;
	public $totalQuantity = 0;
	public $totalPrice = 0;
    public function __construct($cart){
        if($cart){
            $this->products=$cart->products;
            $this->totalQuantity = $cart->totalQuantity;
            $this->totalPrice = $cart->totalPrice;
        }
    }
    public function AddCart($product, $id){
        $newProduct=['quantity'=>0,'price'=>$product->GiaMoi,'productInfo'=>$product];
    
        if($this->products){
            if(array_key_exists($id,$this->products)){
                $newProduct=$this->products[$id];//neusp đã tồn tại trong mảng
            }
        }
        $newProduct['quantity']++;
        $newProduct['price']=$newProduct['quantity']*$product->GiaMoi;
        $this->products[$id] = $newProduct;
        $this->totalPrice+=$product->GiaMoi;
        $this->totalQuantity++;

    }
    public function DeleteItemCart($id){
        $this->totalQuantity -= $this->products[$id]['quantity']; //- số luong của sp có id trong dsach products
        $this->totalPrice -= $this->products[$id]['price'];//- giá của sp có id trong dsach products
        unset($this->products[$id]); 
    }
}
