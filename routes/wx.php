<?php

/******商品**********************/
$router->get("/items", "Wx\\IndexController@items");
$router->get("/item/detail","Wx\\ItemController@detail");
$router->get("/item/buy","Wx\\ItemController@buy");

/****自提点**********************/
$router->get("/address","Wx\\AddressController@index");


/******购物车**********************/
$router->get("/cart","Wx\\CartController@index");
$router->post("/cart/add","Wx\\CartController@add");
$router->post("/cart/cut","Wx\\CartController@cut");
$router->post("/cart/delete","Wx\\CartController@delete");

/******用户**********************/
$router->get("/user","Wx\\UserController@userInfo");
$router->post("/user/add","Wx\\UserController@add");
$router->post("/user/update","Wx\\UserController@update");

/******配置相关**********************/
$router->get("/index/config",'Wx\\IndexController@getPhoneConfig');

/*********优惠券********************/
$router->get("/coupon","Wx\\CouponController@index");

/*********订单********************/
$router->get("/order","Wx\\OrderController@orderList");
$router->post("/order/create","Wx\\OrderController@orderCreate");
