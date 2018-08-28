<?php
namespace App\Http\Common;

/**
 * 公共常量
 *
 * Class CommonConst
 * @package App\Http\Common
 */
class CommonConst
{
    /****订单常量******/
    const ORDER_NOT_PAY = 1;
    const ORDER_IS_PAY = 2;
    const ORDER_CANCEL = 4; //订单取消


    /*****优惠券常量*****/

    const USER_COUPON_ON = 1; //用户领取优惠券正常
    const USER_COUPON_OFF = 2; //用户领取优惠券过期
}