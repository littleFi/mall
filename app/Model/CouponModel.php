<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CouponModel extends Model
{
    protected $table = 'wx_coupon';

    protected $dateFormat = "Y-m-d H:i:s";

    /**
     * 用户领取优惠券表关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->hasMany(CouponUserModel::class,'coupon_id','id');
    }
}