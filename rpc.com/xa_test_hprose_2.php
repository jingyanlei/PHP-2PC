<?php
/**
 * 分布式事务测试 更新goods测试
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
$client_goods = Client::create('http://goods.server.rpc.com/goods.php', false);
try {
    var_dump($data);
    echo '<hr>';
    //更新goods表
    $result_goods = $client_goods->update($data);
    var_dump($result_goods);
    if ($result_goods['code'] == 200) {
        var_dump($client_goods->XAcommit($data));
    } else {
        var_dump($client_goods->XArollback($data));
    }
} catch (Exception $e) {
    var_dump($e->getMessage());
    //失败回滚
    if ($data['xa'] == true) {
        var_dump($client_goods->XArollback($data));
    }
    throw new Exception('执行失败');
}