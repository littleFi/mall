<?php
namespace App\Http\Controllers\Wx;

use App\Http\Controllers\RestfulController;
use App\Model\UserModel;

class UserController extends RestfulController
{
    /**
     * 获取用户信息
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userInfo()
    {
        $userId = $this->request->get("user_id");

        $user = UserModel::query()
            ->where("id",$userId)
            ->first();

        if (!empty($user)){
            $user = $user->toArray();
        }

        return $this->success($user);
    }

    /**
     * 用户数据新增
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add()
    {
        $username = $this->request->post("username");
        $phone = $this->request->post("phone");
        $nickname = $this->request->post('nickname');
        $pic = $this->request->post("pic");
        $openid = $this->request->post("openid");
        $sex = $this->request->post("sex",2);

        $data = [
            'username' => $username,
            'wx_phone' => $phone,
            'wx_nickname' => $nickname,
            'wx_pic' => $pic,
            'wx_openid' => $openid,
            'sex' => $sex
        ];
        $uid = UserModel::query()->insertGetId($data);

        return $this->success(['id' => $uid]);
    }

    /**
     * 用户信息更改
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update()
    {
        $username = $this->request->post("username");
        $phone = $this->request->post("phone");
        $sex = $this->request->post("sex");
        $id = $this->request->post("user_id");

        $data = [
            'username' => $username,
            'wx_phone' => $phone,
            'sex' => $sex
        ];

        $isUpdate = UserModel::query()
            ->where("id",$id)
            ->update($data);

        return $this->success(['status' => $isUpdate]);
    }
}