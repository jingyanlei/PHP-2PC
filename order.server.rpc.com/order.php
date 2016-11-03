<?php
require('./vendor/autoload.php');
use Hprose\Http\Server;

/**
 * 订单
 * User: jingyanlei
 * Date: 2016/10/19
 * Time: 14:01
 */
class Order {

    private $_dsn;
    private $_grid;

    public function __construct() {
        //order
//        $this->_dsn = new PDO('mysql:host=10.211.55.101;dbname=vtown_order', 'orders', '123456aA!', [PDO::ATTR_PERSISTENT => true]);
        $this->_dsn = new PDO('mysql:host=10.211.55.100;dbname=vtown_order', 'orders', '123456aA!');
    }

    //XA 开始
    private function XAstart($params) {
        $this->_grid = $params['grid'];
        $result = $this->_dsn->query('XA START \''.$this->_grid.'\'');
        return $this->_result($result);
    }

    //XA 准备事务
    public function XAend($params) {
        $this->_grid = $params['grid'];
        $result = $this->_dsn->query('XA END \''.$this->_grid.'\'');
        return $this->_result($result);
    }

    //XA 准备事务
    public function XAprepare($params) {
        $this->_grid = $params['grid'];
        $result = $this->_dsn->query('XA PREPARE \''.$this->_grid.'\'');
        return $this->_result($result);
    }

    //XA 提交事务
    public function XAcommit($params) {
        $this->_grid = $params['grid'];
        $result = $this->_dsn->query('XA COMMIT \''.$this->_grid.'\'');
        return $this->_result($result);
    }

    //XA 回滚事物
    public function XArollback($params) {
        $this->_grid = $params['grid'];
        $result = $this->_dsn->query('XA ROLLBACK \''.$this->_grid.'\'');
        return $this->_result($result);
    }

    /**
     * 返回信息
     * @param $result
     * @return array
     */
    private function _result($result) {
        if ($result === false) {
            return [
                'code'=>false,
                'msg'=>$this->_dsn->errorInfo()
            ];
        } else {
            return [
                'code'=>true,
                'msg'=>''
            ];
        }
    }

    /**
     * 添加订单
     * @param array $params
     * @return int
     */
    public function add($params=[]) {
        if (isset($params['grid'])) {
            $this->_grid = $params['grid'];
            if (isset($params['xa']) && $params['xa'] == true) {
                $this->XAstart($params);
            }
            $_b = false;
            try {
                $sql = 'INSERT INTO `order` (order_no, goods_id, goods_name, goods_num, create_time) VALUES (\''.time().'\', 1, \'test\', 1, \''.time().'\')';
                $resultOrder = $this->_dsn->query($sql);
                if ($resultOrder !== false) {
                    if ($resultOrder->rowCount() > 0) {
                        $_b = true;
                    }
            }
            } catch (Exception $e) {
                $_b = false;
            }
            if (isset($params['xa']) && $params['xa'] == true) {
                $this->XAend($params);
                if ($_b) {
                    $this->XAprepare($params);
                }
            }
            if ($_b) {
                return [
                    'code'=>200,
                    'msg'=>'成功'
                ];
            } else {
                return [
                    'code'=>202,
                    'msg'=>'order更新失败'
                ];
            }
        } else {
            return [
                'code'=>203,
                'msg'=>'grid不能为空'
            ];
        }
    }

    public function test() {
        return 'test';
    }

}

function add($params=[]) {
    $a = $params;
    return $a;
}

//
$server = new Server();
//$server->crossDomain = true;
//$server->add('add', new Order());
$server->add(new Order());
//$server->addFunction('add');
//$server->addMethod('add', new Order());
$server->handle();
//$server->start();

