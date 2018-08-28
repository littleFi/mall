<?php
namespace App\Http\Controllers\Wx;

use App\ConstHelper\CommonConst;
use App\Http\Controllers\RestfulController;
use App\Model\ConfigModel;
use App\Model\ItemModel;
use App\Helper\QueryHelper;

/**
 * 首页
 *
 * @package App\Http\Controllers
 */
class IndexController extends RestfulController
{

    /**
     * 首页商品列表
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function items()
    {
        $page = $this->request->post("page");
        $pageSize = $this->request->post("pageSize",10);

        $model = ItemModel::query();

        $model = QueryHelper::filter($model, [
            'id', 'title', 'primary_image', 'sale_price', 'group_price', 'total_store', 'sale_store', 'start_time',
            'end_time', 'dispatch_time', 'tags'
        ]);
        $model = QueryHelper::orderBy($model, [
            'id' => 'desc'
        ]);

        $data = QueryHelper::forPage($model, $page, $pageSize)->get()->toArray();

        return $this->success($data);
    }

    /**
     * 首页联系方式
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPhoneConfig()
    {

        $data = ConfigModel::query()
            ->where(['scope' => CommonConst::INDEX_SCOPE, 'key' => CommonConst::INDEX_PHONE])
            ->first()
            ->toArray();

        $data['value'] = json_decode($data['value'],true);

        return $this->success($data['value']);

    }
}