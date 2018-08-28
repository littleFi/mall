<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\RestfulController;
use App\Model\AdminModel;


class LoginController extends RestfulController
{
    /**
     * 登录
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login()
    {
        $username = $this->request->post("username");
        $password = $this->request->post("password");

        $admin = AdminModel::query()
            ->where([
                'username' => $username,
                'password' => md5($password),
                'level' => 3
            ])
            ->first();

        if (!empty($admin)){
            return $this->success(['msg' => '登录成功','status' => 'success']);
        }

        return $this->success(['msg' => '登录失败','status' => 'fail']);

    }
}