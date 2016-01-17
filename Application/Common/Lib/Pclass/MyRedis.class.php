<?php 
/**
 * 封装redis部分操作(主要是短连接)
 * 目前使用的Redis键如下：
 *         键名                        类型                   作用
 * identifyCode:$type:$phone        字符串类型           保存手机验证码
 * token.to.userid:$userToken       字符串类型           保存userToken对应的userId
 * user.info:$uid                   哈希类型             保存用户的各种信息
 *                                                       usertoken：主要是为了删除旧token
 *                                                       logintype：登录类型(1-手机号码  2-邮箱  3-QQ号码  4-Q药网旧用户)
 *                                                       logintime：登录时间(时间戳)
 *
 */
namespace Common\Lib\Pclass;

class MyRedis {

    const _HOST = '192.168.0.34';    //REDIS服务主机IP
    const _PORT = 6379;           //REDIS服务端口

    /**
     * 查看redis连接是否断开
     * @return $return bool true:连接未断开 false:连接已断开
     */
    public static function ping()
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null; 
        $return = $redis->ping();
        $redis->close();
        $redis = null;
        return 'PONG' ? true : false;
    }

    /**
     * 设置redis模式参数
     * @param $option array 参数数组键值对
     * @return $return true/false 
     */
    public static function setOption($option=array())
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->setOption($option);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 获取redis模式参数
     * @param $option array 要获取的参数数组
     */
    public static function getOption($option=array())
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->getOption();
        $redis->close();
        $redis = null;
        return $return;
    }

    //+++-------------------------字符串操作-------------------------+++//
    /**
     * 写入key-value
     * @param $key string 要存储的key名
     * @param $value mixed 要存储的值
     * @param $time longint 过期时间(S)  默认值为0-不设置过期时间
     * @return $return bool true:成功 flase:失败
     */
    public static function set($key,$value,$time = 0)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($time && is_numeric($time)) 
            $return = $redis->setex($key, $time, $value);
        else 
            $return = $redis->set($key, $value);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 获取某个key值 如果指定了start end 则返回key值的start跟end之间的字符
     * @param $key string/array 要获取的key或者key数组
     * @param $start int 字符串开始index
     * @param $end int 字符串结束index
     * @return $return mixed 如果key存在则返回key值 如果不存在返回false
     */
    public static function get($key=null,$start=null,$end=null)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if (is_array($key) && !empty($key)) {
            $return = $redis->getMultiple($key);
        } else {
            if (isset($start) && isset($end)) $return = $redis->getRange($key);
            else $return = $redis->get($key);
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 删除某个key值
     * @param $key array key数组
     * @return $return longint 删除成功的key的个数
     */
    public static function delete($key=array())
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->delete($key);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 判断某个key是否存在
     * @param $key string 要查询的key名
     */
    public static function exists($key)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->exists($key);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * key值自增或者自减
     * @param $key string key名
     * @param $type int 0:自减 1:自增 默认为1
     * @param $n int 自增步长 默认为1
     */
    public static function deinc($key,$type=1,$n=1)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $n = (int)$n;
        switch ($type) {
            case 0:
                if ($n == 1) $return = $redis->decr($key);
                else if ($n > 1) $return = $redis->decrBy($key, $n);
            break;
            case 1:
                if ($n == 1) $return = $redis->incr($key);
                else if ($n > 1) $return = $redis->incrBy($key, $n);
            break;
            default:
                $return = false;
            break;
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 同时给多个key赋值
     * @param $data array key值数组 array('key0'=>'value0','key1'=>'value1')
     */
    public static function mset($data)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->mset($data);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 查询某个key的生存时间
     * @param $key string 要查询的key名
     */
    public static function ttl($key) 
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->ttl($key);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 删除到期的key
     * @param $key string key名
     */
    public static function persist($key)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->persist($key);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 获取某一key的value
     * @param $key string key名
     */
    public static function strlen($key)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->strlen($key);
        $redis->close();
        $redis = null;
        return $return; 
    }

    //+++-------------------------哈希操作-------------------------+++//
    /**
     * 将key->value写入hash表中
     * @param $hash string 哈希表名
     * @param $data array 要写入的数据 array('key'=>'value')
     * @param $time longint 过期时间(S)  默认值为0-不设置过期时间
     */
    public static function hashSet($hash,$data,$time = 0)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if (is_array($data) && !empty($data)) {
            $return = $redis->hMset($hash, $data);
            if ($time && is_numeric($time)) $redis->expire($hash, $time);
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 获取hash表的数据
     * @param $hash string 哈希表名
     * @param $key mixed 表中要存储的key名 默认为null 返回所有key->value
     * @param $type int 要获取的数据类型 0:返回所有key 1:返回所有value 2:返回所有key->value
     */
    public static function hashGet($hash,$key=array(),$type=0)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($key) {
            if (is_array($key) && !empty($key))
                $return = $redis->hMGet($hash,$key);
            else
                $return = $redis->hGet($hash,$key);
        } else {
            switch ($type) {
                case 0:
                    $return = $redis->hKeys($hash);
                break;
                case 1:
                    $return = $redis->hVals($hash);
                break;
                case 2:
                    $return = $redis->hGetAll($hash);
                break;
                default:
                    $return = false;
                break;
            }
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 获取hash表中元素个数
     * @param $hash string 哈希表名
     */
    public static function hashLen($hash)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->hLen($hash);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 删除hash表中的key
     * @param $hash string 哈希表名
     * @param $key mixed 表中存储的key名
     */
    public static function hashDel($hash,$key)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->hDel($hash,$key);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 查询hash表中某个key是否存在
     * @param $hash string 哈希表名
     * @param $key mixed 表中存储的key名
     */
    public static function hashExists($hash,$key) 
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->hExists($hash,$key);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 自增hash表中某个key的值
     * @param $hash string 哈希表名
     * @param $key mixed 表中存储的key名
     * @param $inc int 要增加的值
     */
    public static function hashInc($hash,$key,$inc) 
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->hIncrBy($hash, $key, $inc);
        $redis->close();
        $redis = null;
        return $return;
    }

    //+++-------------------------链表(或队列)操作-------------------------+++//
    /**
     * 入队列
     * @param $list string 队列名
     * @param $value mixed 入队元素值
     * @param $deriction int 0:数据入队列头(左) 1:数据入队列尾(右) 默认为0
     * @param $repeat int 判断value是否存在  0:不判断存在 1:判断存在 如果value存在则不入队列
     */
    public static function listPush($list,$value,$direction=0,$repeat=0)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        switch ($direction) {
            case 0:
                if ($repeat)
                    $return = $redis->lPushx($list,$value);
                else
                    $return = $redis->lPush($list,$value);
            break;
            case 1:
                if ($repeat)
                    $return = $redis->rPushx($list,$value);
                else
                    $return = $redis->rPush($list,$value); 
            break;
            default:
                $return = false;
            break;
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 出队列
     * @param $list1 string 队列名
     * @param $deriction int 0:数据入队列头(左) 1:数据入队列尾(右) 默认为0
     * @param $list2 string 第二个队列名 默认null
     * @param $timeout int timeout为0:只获取list1队列的数据 
     *        timeout>0:如果队列list1为空 则等待timeout秒 如果还是未获取到数据 则对list2队列执行pop操作
     */
    public static function listPop($list1,$deriction=0,$list2=null,$timeout=0)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        switch ($direction) {
            case 0:
                if ($timeout && $list2)
                    $return = $redis->blPop($list1,$list2,$timeout);
                else
                    $return = $redis->lPop($list1);
            break;
            case 1:
                if ($timeout && $list2)
                    $return = $redis->brPop($list1,$list2,$timeout);
                else
                    $return = $redis->rPop($list1);
            break;
            default:
                $return = false;
            break;
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 获取队列中元素数
     * @param $list string 队列名
     */
    public static function listSize($list)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->lSize($list);
        $redis->close();
        $redis = null;
        return $return; 
    }

    /**
     * 为list队列的index位置的元素赋值
     * @param $list string 队列名
     * @param $index int 队列元素位置
     * @param $value mixed 元素值
     */
    public static function listSet($list,$index=0,$value=null)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->lSet($list, $index, $value);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 获取list队列的index位置的元素值
     * @param $list string 队列名
     * @param $index int 队列元素开始位置 默认0
     * @param $end int 队列元素结束位置 $index=0,$end=-1:返回队列所有元素
     */
    public static function listGet($list,$index=0,$end=null)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($end) {
            $return = $redis->lRange($list, $index, $end);
        } else {
            $return = $redis->lGet($list, $index);
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 截取list队列，保留start至end之间的元素
     * @param $list string 队列名
     * @param $start int 开始位置
     * @param $end int 结束位置
     */
    public static function listTrim($list,$start=0,$end=-1)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->lTrim($list, $start, $end);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 删除list队列中count个值为value的元素
     * @param $list string 队列名
     * @param $value int 元素值
     * @param $count int 删除个数 0:删除所有 >0:从头部开始删除 <0:从尾部开始删除 默认为0删除所有
     */
    public static function listRemove($list,$value,$count=0)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->lRem($list, $value, $count);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 在list中值为$value1的元素前Redis::BEFORE或者后Redis::AFTER插入值为$value2的元素
     * 如果list不存在，不会插入，如果$value1不存在，return -1
     * @param $list string 队列名
     * @param $location int 插入位置 0:之前 1:之后
     * @param $value1 mixed 要查找的元素值
     * @param $value2 mixed 要插入的元素值
     */
    public static function listInsert($list,$location=0,$value1,$value2)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        switch ($location) {
            case 0:
                $return = $redis->lInsert($list, Redis::BEFORE, $value1, $value2);
            break;
            case 1:
                $return = $redis->lInsert($list, Redis::AFTER, $value1, $value2);
            break;
            default:
                $return = false;
            break;
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * pop出list1的尾部元素并将该元素push入list2的头部
     * @param $list1 string 队列名
     * @param $list2 string 队列名
     */
    public static function rpoplpush($list1, $list2)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->rpoplpush($list1, $list2);
        $redis->close();
        $redis = null;
        return $return;
    }

    //+++-------------------------集合操作-------------------------+++//
    /**
     * 将value写入set集合 如果value存在 不写入 返回false
     * 如果是有序集合则根据score值更新该元素的顺序
     * @param $set string 集合名
     * @param $value mixed 值
     * @param $stype int 集合类型 0:无序集合 1:有序集和 默认0
     * @param $score int 元素排序值
     */
    public static function setAdd($set,$value=null,$stype=0,$score=null)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($stype && $score !== null) {
            $return = $redis->zAdd($set, $score, $value);
        } else {
            $return = $redis->sAdd($set, $value);
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 移除set1中的value元素 如果指定了set2 则将该元素写入set2
     * @param $set1 string 集合名
     * @param $value mixed 值
     * @param $stype int 集合类型 0:无序集合 1:有序集和 默认0
     * @param $set2 string 集合名
     */
    public static function setMove($set1, $value=null, $stype=0, $set2=null)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($set2) {
            $return = $redis->sMove($set1, $set2, $value);
        } else {
            if ($stype) $return = $redis->zRem($set1, $value);
            else $return = $redis->sRem($set1, $value);
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 查询set中是否有value元素
     * @param $set string 集合名
     * @param $value mixed 值
     */
    public static function setSearch($set, $value=null)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->sIsMember($set, $value);
        $redis->close();
        $redis = null;
        return $return; 
    }

    /**
     * 返回set中所有元素个数 有序集合要指定$stype=1
     * 如果是有序集合并指定了$start和$end 则返回score在start跟end之间的元素个数
     * @param $set string 集合名
     * @param $stype int 集合类型 0:无序集合 1:有序集和 默认0
     * @param $start int 开始index
     * @param $end int 结束index
     */
    public static function setSize($set,$stype=0,$start=0,$end=0)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($stype) {
            if ($start && $end) $return = $redis->zCount($set, $start, $end);
            else $return = $redis->zSize($set);
        } else {
            $return = $redis->sSize($set);
        }
        $redis->close();
        $redis = null;
        return $return;
    }
    /**
     * 随机返回set中一个元素并可选是否删除该元素
     * @param $set string 集合名
     * @param $isdel int 是否删除该元素 0:不删除 1:删除 默认为0
     */
    public static function setPop($set,$isdel=0)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($isdel) {
            $return = $redis->sPop($set);
        } else {
            $return = $redis->sRandMember($set);
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 求交集 并可选是否将交集保存到新集合
     * @param $set array 集合名数组
     * @param $newset string 要保存到的集合名 默认为null 即不保存交集到新集合
     * @param $stype int 集合类型 0:无序集合 1:有序集和 默认0
     * @param $weight array 权重 执行function操作时要指定的每个集合的相同元素所占的权重 默认1
     * @param $function string 不同集合的相同元素的取值规则函数 SUM:取元素值的和 MAX:取最大值元素 MIN:取最小值元素
     */
    public static function setInter($set,$newset=null,$stype=0,$weight=array(1),$function='SUM')
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = array();
        if (is_array($set) && !empty($set)) {
            if ($newset) {
                if ($stype) $return = $redis->zInter($newset, $set, $weight, $function);
                else $return = $redis->sInterStore($newset, $set);
            } else {
                $return = $redis->sInter($set);
            }
        }
        $redis->close();
        $redis = null;
        return $return; 

    } 

    /**
     * 求并集 并可选是否将交集保存到新集合
     * @param $set array 集合名数组
     * @param $newset string 要保存到的集合名 默认为null 即不保存交集到新集合
     * @param $stype int 集合类型 0:无序集合 1:有序集和 默认0
     * @param $weight array 权重 执行function操作时要指定的每个集合的相同元素所占的权重 默认1
     * @param $function string 不同集合的相同元素的取值规则函数 SUM:取元素值的和 MAX:取最大值元素 MIN:取最小值元素
     */
    public static function setUnion($set,$newset=null,$stype=0,$weight=array(1),$function='SUM')
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = array();
        if (is_array($set) && !empty($set)) {
            if ($newset) {
                if ($stype) $return = $redis->zUnion($newset, $set, $weight, $function);
                else $return = $redis->sUnionStore($newset, $set);
            } else {
                $return = $redis->sUnion($set); 
            }
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 求差集 并可选是否将交集保存到新集合
     * @param $set array 集合名数组
     * @param $newset string 要保存到的集合名 默认为null 即不保存交集到新集合
     */
    public static function setDiff($set,$newset=null)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = array();
        if (is_array($set) && !empty($set)) {
            if ($newset) {
                $return = $redis->sDiffStore($newset, $set);
            } else {
                $return = $redis->sDiff($set);
            }
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * 返回set中所有元素
     * @param $set string 集合名
     */
    public static function setMembers($set)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->sMembers($set);
        $redis->close();
        $redis = null;
        return $return;
    }
    /**
     * 排序 分页等
     * @param $set string 集合名
     * @param $option array 选项
     */
    public static function setSort($set,$option)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $default_option = array(
            'by'    => 'some_pattern_*', //要匹配的排序value值
            'limit' => array(0, 1), //array(start,length)
            'get'   => 'some_other_pattern_*', //多个匹配格式:array('some_other_pattern1_*','some_other_pattern2_*')
            'sort'  => 'asc', // asc|desc 默认asc
            'alpha' => TRUE,
            'store' => 'some_need_pattern_*' //永久性排序值
        );
        $option = array_merge($default_option, $option);
        $return = $redis->sort($set, $option);
        $redis->close();
        $redis = null;
        return $return;
    }

    //+++-------------------------有序集合操作-------------------------+++//
    /**
     * ***只针对有序集合操作
     * 返回set中index从start到end的所有元素
     * @param $set string 集合名
     * @param $start int 开始Index
     * @param $end int 结束Index
     * @param $order int 排序方式 0:从小到大排序 1:从大到小排序 默认0
     * @param $score bool 元素排序值 false:返回数据不带score true:返回数据带score 默认false
     */
    public static function setRange($set,$start,$end,$order=0,$score=false)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($order) {
            $return = $redis->zRevRange($set, $start, $end, $score);
        } else {
            $return = $redis->zRange($set, $start, $end, $score);
        }
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * ***只针对有序集合操作
     * 删除set中score从start到end的所有元素
     * @param $set string 集合名
     * @param $start int 开始score
     * @param $end int 结束score
     */
    public static function setDeleteRange($set,$start,$end)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        $return = $redis->zRemRangeByScore($set, $start, $end);
        $redis->close();
        $redis = null;
        return $return;
    }

    /**
     * ***只针对有序集合操作
     * 获取set中某个元素的score 
     * 如果指定了inc参数 则给该元素的score增加inc值
     * 如果没有该元素 则将该元素写入集合
     * @param $set string 集合名
     * @param $value mixed 元素值
     * @param $inc int 要给score增加的数值 默认是null 不执行score增加操作
     */
    public static function setScore($set,$value,$inc=null)
    {
        $redis = new \Redis();
        $redis->connect(self::_HOST,self::_PORT);
        $return = null;
        if ($inc) {
            $return = $redis->zIncrBy($set, $inc, $value);
        } else {
            $return = $redis->zScore($set, $value);
        }
        $redis->close();
        $redis = null;
        return $return;
    }
}