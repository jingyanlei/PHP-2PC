<?php
require('./vendor/autoload.php');
/**
 * Created by PhpStorm.
 * User: jingyanlei
 * Date: 2016/10/19
 * Time: 18:22
 */
class Goods {

    private $_dsn;
    private $_grid;

    public function __construct() {
//        $this->_dsn = new PDO('mysql:host=10.211.55.101;dbname=vtown_goods', 'goods', '123456aA!', [PDO::ATTR_PERSISTENT => true]);
        $this->_dsn = new PDO('mysql:host=10.211.55.101;dbname=vtown_goods', 'goods', '123456aA!');
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

    public function update($params) {
        if (isset($params['grid'])) {
            $this->_grid = $params['grid'];
            if (isset($params['xa']) && $params['xa'] == true) {
                $this->XAstart($params);
            }
            $_b = false;
            try {
                $sql = "UPDATE `goods` SET `num` = `num` - 1 WHERE `id` = 2";
                $resultGoods = $this->_dsn->query($sql);
                if ($resultGoods !== false) {
                    $_b = true;
                }
            } catch(Exception $e) {
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
                    'msg'=>'goods更新成功',
                ];
            } else {
                return [
                    'code'=>301,
                    'msg'=>'goods更新失败---'.$_b.'---',
                ];
            }
        } else {
            return [
                'code'=>203,
                'msg'=>'grid不能为空'
            ];
        }
    }

}

$server = new \Hprose\Http\Server();
$server->debug=true;
$server->add(new Goods());
$server->handle();