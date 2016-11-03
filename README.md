# PHP-2PC Hprose分布式示例
### 1.服务器或虚拟机
```
虚拟机三台centos6.5
IP:10.211.55.100 10.211.55.101 10.211.55.106
```
### 2.ip:10.211.55.100
```
php5.6
mysql5.7 order数据库
nginx配置
	rpc.com
	order.server.rpc.com
```
### 3.ip:10.211.55.101
```
mysql5.7
```
### 4.ip:10.211.55.106
```
php5.6
nginx配置
	order.server.rpc.com
```
### 5.hosts配置
```
本机hosts
10.211.55.100 rpc.com

虚拟机10.211.55.100 hosts
10.211.55.100 order.server.rpc.com
10.211.55.106 goods.server.rpc.com
```
### 6.数据库
```
10.211.55.100 创建order数据库，增加order表
CREATE TABLE `order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(32) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `goods_name` varchar(255) DEFAULT NULL,
  `goods_num` int(11) DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

10.211.55.101 创建goods数据库，增加goods表
goods数据库
CREATE TABLE `goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `num` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
INSERT INTO `goods` VALUES ('1', '商品1', '1000'), ('2', '商品2', '999');
```
### 7. 测试代码
```
1.未使用RPC HPRose
http://rpc.com/xa_test.php
 
2.RPC HPRose order goods 分布事务XA使用
http://rpc.com/xa_test_hprose.php

3.RPC HPRose goods 测试
http://rpc.com/xa_test_hprose_2.php

4.RPC HPRose order 测试
http://rpc.com/xa_test_hprose_3.php
```