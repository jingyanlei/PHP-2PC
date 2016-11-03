<?php
/**
 * 分布式事务测试 order goods
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
$client_goods = Client::create('http://goods.server.rpc.com/goods.php', false);
try {
    var_dump($data);
    //1.更新order表
    $result_order = $client_order->add($data);
    var_dump($result_order);
    if($result_order['code'] == 200) {
        echo '<hr>';
        //2.更新goods表
        $result_goods = $client_goods->update($data);
        var_dump($result_goods);
    }
    echo '<hr>'.'-----状态------';
    if ($data['xa'] == true) {
        if ($result_order['code'] == 200 && $result_goods['code'] == 200) {
            //3.提交SQL
            var_dump($client_order->XAcommit($data));
            var_dump($client_goods->XAcommit($data));
            echo '<hr>'.'成功!!!!!!';
        } else {
            //3.失败回滚
            var_dump($client_order->XArollback($data));
            var_dump($client_goods->XArollback($data));
            echo '<hr>'.'失败回滚';
        }
    }
    echo '-----状态------';

} catch (Exception $e) {
    var_dump($e->getMessage());
    //3.失败回滚
    if ($data['xa'] == true) {
        var_dump($client_order->XArollback($data));
        var_dump($client_goods->XArollback($data));
    }
    throw new Exception('执行失败');
}