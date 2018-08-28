<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\RestfulController;
use App\Model\OrderModel;
use Carbon\Carbon;

class OrderController extends RestfulController
{
    /**
     * 取货码验证
     */
    public function code()
    {
        $code = $this->request->post("code");

        $order = OrderModel::query()
            ->where("code",$code)
            ->first();


        if (!empty($order)){
            $order = $order->toArray();
            return $this->success(['status' => 'success','id' => $order['id']]);
        }

        return $this->success(['status' => 'fail']);
    }

    /**
     * 订单详细
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function order()
    {
        $orderId = $this->request->get("order_id");

        $order = OrderModel::query()
            ->where("id",$orderId)
            ->with("detail")
            ->get();

        if (!empty($orderId)){
            $order = $order->toArray();

            return $this->success($order);
        }

        return $this->success([]);

    }

    /**
     * 交付
     */
    public function deliver()
    {
        $orderId = $this->request->post("order_id");

        $order = OrderModel::query()
            ->where("id",$orderId)
            ->first();

        if (!empty($order)){
            $order = $order->toArray();

            if ($order['status'] != 4){
                $data = [
                    'status' => 4, //完成
                    'dispatch_time' => Carbon::now()->toDateTimeString()
                ];

                OrderModel::query()
                    ->where("id",$orderId)
                    ->update($data);

                return $this->success(['status' => 'true','msg' => '领取成功']);
            }
        }

        return $this->success(['status'=> 'fail','msg' => '无此订单']);
    }
}