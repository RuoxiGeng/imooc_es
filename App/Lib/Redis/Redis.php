<?php
/**
 * Created by PhpStorm.
 * User: ruoxigeng
 * Date: 2019-08-05
 * Time: 22:11
 */

namespace App\Lib\Redis;

use Composer\Config;
use EasySwoole\Component\Singleton;

class Redis {
    use Singleton;

    public $redis = "";

    private function __construct() {
        if(!extension_loaded('redis')) {
            throw new \Exception("redis.so不存在");
        }

        try {
//            $redisConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf("redis");
            $redisConfig = \Yaconf::get('redis');
            $this->redis = new \Redis();
            $res = $this->redis->connect($redisConfig['host'], $redisConfig['port'], $redisConfig['time_out']);
        } catch (\Exception $e) {
            throw new \Exception("redis服务异常");
        }

        if($res === false) {
            throw new \Exception("redis连接失败");
        }
    }

    public function set($key, $value, $time = 0) {
        if(empty($key)) {
            return '';
        }
        if(is_array($value)) {
            $value = json_encode($value);
        }
        if(!$time) {
            return $this->redis->set($key, $value);
        }
        return $this->redis->setex($key, $time, $value);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function get($key) {
        if(empty($key)) {
            return '';
        }

        return $this->redis->get($key);
    }

    /**
     * @param $key
     * @return string
     */
    public function lPop($key) {
        if(empty($key)) {
            return '';
        }

        return $this->redis->lPop($key);
    }

    /**
     * 消息生产者 进入消息队列
     * @param $key
     * @param $value
     * @return bool|int|string
     */
    public function rPush($key, $value) {
        if(empty($key)) {
            return '';
        }

        return $this->redis->rPush($key, $value);
    }
}