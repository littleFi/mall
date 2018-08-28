<?php
namespace App\ConstHelper;

/**
 * 公共变量类
 *
 * @package App\ConstHelper
 */
class CommonConst
{
    /*****************订单相关变量****************************/
    const ORDER_NOT_PAY = 1; //未付款
    const ORDER_IS_PAY = 2; //已付款
    const ORDER_POST = 3; //已发货
    const ORDER_SUCCESS = 4; //已完成
    const ORDER_CANCEL = 5; //已取消
    const ORDER_DELETE = 6; //已删除

    /***************首页*********************************************/
    const INDEX_SCOPE = 'index';
    const INDEX_PHONE = 'phone'; //联系方式

}