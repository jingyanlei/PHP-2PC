<?php
/**
 * 分布式事务测试 更新订单测试
 */
require('./vendor/autoload.php');

use Hprose\Http\Client;

$_grid = uniqid("");
$_o = false;
$_g = false;

$data = [
    'xa'=>true,
    'grid'=>uniqid("")
];
$client_order = Client::create('http://order.server.rpc.com/order.php', false);
try {
    var_dump($data);
    //1.更新order表
    $result_order = $client_order->add($data);
    var_dump($result_order);
    echo '<hr>'.'-----状态------';
    //开启XA事务
    if ($data['xa'] == true) {
        if ($result_order['code'] == 200) {
            var_dump($client_order->XAcommit($data));
        } else {
            var_dump($client_order->XArollback($data));
        }
    }
} catch (Exception $e) {
//    var_dump($e->getMessage());
    //2.失败回滚
    if ($data['xa'] == true) {
        var_dump($client_order->XArollback($data));
    }
    throw new Exception('执行失败');
}