<?php
/**
 * 分布式事务测试
 */
//order
$dns_order = new PDO('mysql:host=127.0.0.1;dbname=vtown_order', 'orders', '123456aA!');
//goods
$dns_goods = new PDO('mysql:host=10.211.55.101;dbname=vtown_goods', 'goods', '123456aA!');

var_dump($dns_order);
echo '<hr>';
var_dump($dns_goods);
echo '<hr>';

$_grid = uniqid("");
$_o = false;
$_g = false;

//1.准备事务
$dns_order->query('XA START \''.$_grid.'\'');
$dns_goods->query('XA START \''.$_grid.'\'');
try {
    //2.更新order表
    $sql = 'INSERT INTO `order` (order_no, goods_id, goods_name, goods_num, create_time) VALUES (\''.time().'\', 1, \'test\', 1, \''.time().'\')';
    $resultOrder = $dns_order->query($sql);
    if ($resultOrder === false) {
        echo 'order更新失败';
    } else {
        if ($resultOrder->rowCount() > 0) {
            //4.成功通知准备提交
            var_dump($dns_order->query('XA END \''.$_grid.'\''));
            var_dump($dns_order->query('XA PREPARE \''.$_grid.'\''));
            var_dump($resultOrder->rowCount());
            $_o = true;
        }
    }
    if($_o == true) {
        echo '<hr>';
        //3.更新goods表
        $sql = "UPDATE `goods` SET `num` = `num` - 1 WHERE `id` = 2";
        $resultGoods = $dns_goods->query($sql);
        if ($resultGoods === false) {
            echo 'goods更新失败';
        } else {
            if ($resultGoods->rowCount() > 0) {
                //4.成功通知准备提交
                var_dump($dns_goods->query('XA END \''.$_grid.'\''));
                var_dump($dns_goods->query('XA PREPARE \''.$_grid.'\''));
                var_dump($resultGoods->rowCount());
                $_g = true;
            } else {
                echo 'goods未更新记录';
            }
        }
    }
    echo '<hr>'.'-----状态------';
    var_dump($_grid);
    var_dump($_o);
    var_dump($_g);
    echo '-----状态------';
    if ($_o == true && $_g == true) {
        //5.提交SQL
        var_dump($dns_order->query('XA COMMIT \''.$_grid.'\''));
        var_dump($dns_goods->query('XA COMMIT \''.$_grid.'\''));
        echo '<hr>'.'成功!!!!!!';
    } else {
        //4.失败回滚
        echo '<hr>'.'失败回滚';
        $dns_order->query('XA ROLLBACK \''.$_grid.'\'');
        $dns_goods->query('XA ROLLBACK \''.$_grid.'\'');
    }
} catch (Exception $e) {
    //4.失败回滚
    $dns_order->query('XA ROLLBACK \''.$_grid.'\'');
    $dns_goods->query('XA ROLLBACK \''.$_grid.'\'');
    throw new Exception('执行失败');
}