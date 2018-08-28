<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    protected $table = 'wx_cart';

    protected $dateFormat = "Y-m-d H:i:s";

    /**
     * 商品表关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function item()
    {
        return $this->hasOne(ItemModel::class,'id','item_id');
    }
}