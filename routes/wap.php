<?php

$router->post("/login", "Wap\\LoginController@login");
$router->post("/code","Wap\\OrderController@code");
$router->get("/order","Wap\\OrderController@order");
$router->post("/deliver","Wap\\OrderController@deliver");