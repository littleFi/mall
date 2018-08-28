<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CouponUserModel extends Model
{
    protected $table = 'wx_coupon_user';

    protected $dateFormat = "Y-m-d H:i:s";

    /**
     * 优惠券关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function coupon()
    {
        return $this->hasOne(CouponModel::class,'id','coupon_id');
    }
}