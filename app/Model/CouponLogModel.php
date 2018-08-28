<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CouponLogModel extends Model
{
    protected $table = 'wx_coupon_log';

    protected $dateFormat = "Y-m-d H:i:s";

    /**
     * 优惠券表关联
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function coupon()
    {
        return $this->hasone(CouponUserModel::class,'id','coupon_id');
    }
}