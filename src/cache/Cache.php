<?php

namespace PeterSms\cache;

use think\facade\Cache as TpCache;
class Cache
{
    /**
     * 缓存key
     *
     * @param string $verify_id 验证码id
     *
     * @return string
     */
    public static function key($verify_id = '')
    {
        $key = 'Verify:' . $verify_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param string  $verify_id   验证码id
     * @param string  $verify_code 验证码
     * @param integer $expire      有效时间（秒）
     *
     * @return bool
     */
    public static function set($verify_id = '', $verify_code = '', $expire = 0)
    {
        $key = self::key($verify_id);
        $val = $verify_code;
        $exp = $expire ?: 1800;

        $res = TpCache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param string $verify_id 验证码id
     *
     * @return string
     */
    public static function get($verify_id = '')
    {
        $key = self::key($verify_id);
        $res = TpCache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $verify_id 验证码id
     *
     * @return bool
     */
    public static function del($verify_id = '')
    {
        $key = self::key($verify_id);
        $res = TpCache::delete($key);

        return $res;
    }
}