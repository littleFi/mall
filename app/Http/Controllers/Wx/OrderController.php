<?php
namespace App\Http\Controllers;

use App\Http\Common\CommonConst;
use App\Model\CartModel;
use App\Model\CouponLogModel;
use App\Model\CouponUserModel;
use App\Model\ItemModel;
use App\Model\OrderDetailModel;
use App\Model\OrderModel;
use App\Model\UserModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends RestfulController
{
    /**
     * 订单列表
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderList()
    {
        $status = $this->request->get("status",0);
        $userId = $this->request->get("user_id");

        $data = OrderModel::query()
            ->where([
                'user_id' => $userId
            ])
            ->where(function($query) use($status){
                if (!empty($status)){
                    $query->where("status",$status);
                }
            })
            ->get()
            ->toArray();

        return $this->success($data);

    }

    /**
     * 订单明细
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderDetail()
    {
        $id = $this->request->get("id");

        $data = OrderModel::query()
            ->where("id",$id)
            ->with("detail")
            ->first()
            ->toArray();

        return $this->success($data);

    }

    /**
     * 订单创建
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderCreate()
    {
        $cartIds = $this->request->post("cart_id",0);
        $userId = $this->request->post("user_id");
        $couponId = $this->request->post("coupon_id",0);
        $total_fee = $this->request->post("total_fee");

        if (!empty($cartIds)){
            $carts = CartModel::query()
                ->whereIn("id",$cartIds)
                ->where("user_id",$userId)
                ->get()
                ->toArray();
        }else{
            $carts = CartModel::query()
                ->where("user_id",$userId)
                ->get()
                ->toArray();
        }

        $user = UserModel::query()
            ->where("id",$userId)
            ->first()
            ->toArray();

        $itemIds = array_column($carts,"item_id");

        $items = ItemModel::query()
            ->whereIn("id",$itemIds)
            ->get()
            ->toArray();

        $items = collect($items)->keyBy("id")->toArray();

        $no = "P".date("YmdHis").rand(100000,999999);
        $code = $this->code();

        $data = [
            'no' => $no,
            'user_id' => $user['id'],
            'username' => $user['username'],
            'total_fee' => $total_fee,
            'code' => $code,
            'create_at' => Carbon::now()->toDateTimeString(),
            'coupon_id' => $couponId
        ];

        DB::beginTransaction();

        try {

            $id = OrderModel::query()->insertGetId($data);

            $detail = [];
            foreach ($carts as $cart){
                $detail[] = [
                    'order_id' => $id,
                    'no' => $no,
                    'user_id' => $userId,
                    'item_id' => $cart['item_id'],
                    'wx_nickname' => $user['wx_nickname'],
                    'wx_pic' => $user['wx_pic'],
                    'buy_num' => $cart['num'],
                    'create_at' => Carbon::now()->toDateTimeString(),
                    'title' => $items[$cart['item_id']]['title'],
                    'primary_image' => $items[$cart['item_id']]['primary_image'],
                    'price' => $cart['price']
                ];
            }
            OrderDetailModel::query()->insert($detail);

            CartModel::query()->where("user_id")->update(['status' => 2]);//购物车清空

            if (!empty($couponId)){
                CouponUserModel::query()->where("id",$couponId)->update(['status' => 2]); //使用优惠券

                $log = [
                    'no' => $no,
                    'coupon_id' => $couponId,
                    'create_at' => Carbon::now()->toDateTimeString(),
                    'arguments' => '创建订单使用优惠券'
                ];
                CouponLogModel::query()->insert($log);
            }

            DB::commit();

            return $this->success(['msg' => '订单创建成功','status' => 'success']);

        }catch (\Exception $e){
            DB::rollBack();

            Log::error("订单创建失败",['exception' => $e]);
        }

    }

    /**
     * 订单支付
     */
    public function orderPay()
    {

    }

    /**
     * 订单取消
     */
    public function orderCancel()
    {
        $userId = $this->request->post("user_id");
        $orderId = $this->request->post("order_id");

        $order = OrderModel::query()
            ->where("id",$orderId)
            ->first()
            ->toArray();

        DB::beginTransaction();

        try {
            OrderModel::query()
                ->where(['user_id' => $userId,'id' => $orderId])
                ->update(['status' => CommonConst::ORDER_CANCEL]);

            //订单未支付并且使用优惠券，取消订单后返回优惠券
            if (!empty($order['coupon_id']) && $order['status'] == CommonConst::ORDER_NOT_PAY){
                CouponUserModel::query()->where("id",$order['coupon_id'])->update(['status' => 1]);

                $log = [
                    'no' => $order['no'],
                    'coupon_id' => $order['coupon_id'],
                    'create_at' => Carbon::now()->toDateTimeString(),
                    'arguments' => '取消订单返还优惠券'
                ];
                CouponLogModel::query()->insert($log);
            }

            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();

            Log::error("订单取消失败");
        }


        return $this->success(['msg' => '订单取消成功','status' => 'success']);
    }

    /**
     * 生成取货码
     *
     * @return string
     */
    private function code()
    {
        $str = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
        $str = str_shuffle($str);
        $randStr = strtoupper(substr($str, 0, 6));

        return $randStr;
    }
}