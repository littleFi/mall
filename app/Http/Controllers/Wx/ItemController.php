<?php
namespace App\Http\Controllers\Wx;

use App\Http\Controllers\RestfulController;
use App\Model\CartModel;
use App\Model\ItemModel;
use App\Model\OrderDetailModel;

class ItemController extends RestfulController
{
    /**
     * 商品详细页
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detail()
    {
        $id = $this->request->get("item_id");
        $userId = $this->request->get("user_id");

        $data = ItemModel::query()
            ->where("id",$id)
            ->first()
            ->toArray();

        $data['types'] = ItemModel::query()
            ->select(["item_type"])
            ->groupBy("item_type")
            ->get()
            ->toArray();

        $data['tags'] = json_decode($data['tags'],true);
        $data['images'] = json_decode($data['images'],true);

        $data['cart_num'] = CartModel::query()
            ->where("user_id",$userId)
            ->count();

        return $this->success($data);
    }

    /**
     * 商品购买记录
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buy()
    {
        $itemId = $this->request->get("item_id");

        $data = OrderDetailModel::query()
            ->where([
                'item_id' => $itemId
            ])
            ->get()
            ->toArray();

        return $this->success($data);

    }
}