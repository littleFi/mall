<?php
namespace App\Http\Controllers;

use App\Helper\RestfulResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 *
 * Class RestfulController
 * @package app\Http\Controller
 */
class RestfulController extends BaseController
{

    protected $app;

    protected $request;

    /**
     * 创建接口控制器
     * @param Application $app
     * @param Request $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * 发送接口成功响应
     *
     * @param null $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function success($data = null)
    {
        return RestfulResponse::success($data);
    }
}