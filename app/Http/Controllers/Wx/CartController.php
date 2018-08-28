<?php
namespace App\Http\Controllers\Wx;

use App\Http\Controllers\RestfulController;
use App\Model\CartModel;
use App\Model\ItemModel;
use Carbon\Carbon;

class CartController extends RestfulController
{
    /**
     * 购物车
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cart()
    {
        $userId = $this->request->get("user_id");

        $data = CartModel::query()
            ->where("user_id",$userId)
            ->with("item")
            ->get();

        if (!empty($data)){
            $data = $data->toArray();

            $itemIds = array_column($data,"item_id");

            $items = ItemModel::query()
                ->whereIn("id",$itemIds)
                ->get()
                ->toArray();
            $items = collect($items)->keyBy("id")->toArray();

            foreach ($data as &$value){
                $value['title'] = $items[$value['item_id']]['title'];
                $value['price'] = $items[$value['item_id']]['group_price'] ?? $items[$value['item_id']]['sale_price'];
                $value['image'] = $items[$value['item_id']]['primary_image'];
            }
        }
        return $this->success($data);
    }

    /**
     * 购物车添加
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add()
    {
        $itemId = $this->request->post("item_id");
        $useId = $this->request->post("user_id");
        $num = $this->request->post("num",1);

        $item = CartModel::query()
            ->where("item_id",$itemId)
            ->where("user_id",$useId)
            ->first();

        if (!empty($item)){
            CartModel::query()
                ->where([
                    'item_id' => $itemId,
                    'user_id' => $useId
                ])
                ->increment("num",$num);

            return $this->success(['status' => true]);
        }

        $data = [
            'item_id' => $itemId,
            'user_id' => $useId,
            'num' => $num,
            'create_at' => Carbon::now()->toDateTimeString(),
        ];

        $id = CartModel::query()->insertGetId($data);

        return $this->success(['id' => $id,"msg" => '添加成功']);

    }

    /**
     * 购物车减
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cut()
    {
        $itemId = $this->request->post("item_id");
        $useId = $this->request->post("user_id");

        CartModel::query()
            ->where([
                'item_id' => $itemId,
                'user_id' => $useId
            ])
            ->decrement("num");

        return $this->success(['status' => true]);
    }

    /**
     * 购物车删除某条记录
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete()
    {
        $id = $this->request->post("cart_id",0);
        $useId = $this->request->post("user_id",0);

        //清空购物车
        if (empty($id)){
            CartModel::query()
                ->where("useId",$useId)
                ->delete();
        }else{
            CartModel::query()
                ->where("id",$id)
                ->delete();
        }


        return $this->success(['id' => $id]);
    }
}