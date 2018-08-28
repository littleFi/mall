<?php
namespace App\Http\Controllers\Wx;

use App\Http\Controllers\RestfulController;
use App\Model\AddressModel;

/**
 * 自提点
 *
 * Class AddressController
 * @package App\Http\Controllers
 */
class AddressController extends RestfulController
{
    /**
     * 自提点列表
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $data = [];

        $address = AddressModel::query()
            ->get();

        if (!empty($address)){
            $data = $address->toArray();
        }

        return $this->success($data);
    }
}