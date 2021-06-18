<?php

namespace PeterSms;

//短信发送类控制
use think\facade\Config;
use think\facade\Event;

class Service
{
    private static $instance;

    private $config;

    private $cache;

    protected function __construct(){
        $this->config = Config::get('petersms.',[
            'cache' =>\PeterSms\cache\Cache::class, //缓存对象
            'expire' => '1800', //过期时间
        ]);

        $this->cache = new $this->config['cache'];
        $this->expire = $this->config['expire'];
    }

    protected function __clone(){}

    public static function getInstance()
    {
        if(!self::$instance instanceof self){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 发送验证码
     *
     * @param   int    $index 索引
     * @param   string $event  事件
     * @return  boolean
     */
    public function send($index,$event = 'default')
    {
        $verify['index'] = $index;
        $verify['event'] = $event;
        $verify['expire'] = $this->expire;
        $verify['key'] = $this->getKey($index,$event);
        $verify['code'] = Random::numeric(6);

        $this->cache::set($verify['key'],$verify['code'], $this->expire);

        try {
            Event::trigger('PeterSmsSend', $verify);
        }catch (\Throwable $e){
            $this->cache::del($verify['key']);
            throw $e;
        }

        return true;
    }

    /**
     * 校验验证码
     *
     * @param   int    $index 索引
     * @param   int    $code   验证码
     * @param   string $event  事件
     * @return  boolean
     */
    public function check($index, $code, $event = 'default')
    {
        $index = $this->getKey($index,$event);
        $verify = $this->cache::get($index);
        if ($verify && ($verify == $code)) {
            return true;
        }
        return false;
    }

    /**
     * 删除指定验证码
     *
     * @param   int    $index 索引
     * @param   string $event  事件
     */
    public function flush($index, $event = 'default'){
        $index = $this->getKey($index,$event);
        $this->cache::del($index);
    }

    /**
     * 获取key
     */
    public function getKey($index, $event){
        return md5($event . '_' . $index);
    }
}