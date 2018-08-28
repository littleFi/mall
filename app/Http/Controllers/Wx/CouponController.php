<?php
namespace App\Http\Controllers;
use App\Model\CouponModel;
use App\Model\CouponUserModel;


/**
 * 优惠券
 * Class CouponController
 * @package App\Http\Controllers
 */
class CouponController extends RestfulController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $userId = $this->request->post("user_id");
        $status = $this->request->post('status',1);

        $couponUsers = CouponUserModel::query()
            ->where("user_id",$userId)
            ->where('status',$status)
            ->with("coupon")
            ->get();

        $data = [];
        if (!empty($couponUsers)){
                $couponUsers = $couponUsers->toArray();

                foreach ($couponUsers as $coupon){
                    $data[] = [
                        'name' => $coupon['coupon']['name'],
                        'limit_money' => $coupon['coupon']['name'],
                        'deduct_money' => $coupon['coupon']['deduct_money'],
                        'create_at' => $coupon['create_at'],
                        'expired_at' => $coupon['expired_at']
                    ];
                }
        }

        return $this->success($data);

    }
}