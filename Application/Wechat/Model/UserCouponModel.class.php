<?php

namespace Wechat\Model;
use Think\Model;

class UserCouponModel extends Model{
    
    protected $tableName = 'user_coupon';
    const USER_COUPON = 'user_coupon';
    
    public static $couponStatus = array('未使用','已使用','过期');
 
    /**
     * 获取用户面额最大的一张优惠券
     * @param string $openid
     * @return boolean
     */
    public function getMaxCoupon($openid){
        
        $cond['open_id'] = $openid;
        $cond['status']  = 0;
        $couponIds = $this->where($cond)->group('coupon_id')->getField('coupon_id',true);
        if($couponIds){
            $arr['id']         = array('in',$couponIds);
            $arr['begin_time'] = array('lt',time());
            $arr['end_time']   = array('gt',time());
            $couponInfo = D('Coupon')->where($arr)->order('price DESC')->find();
            return $couponInfo;
        }else{
            return false;
        }
        
    }
    
    /**
     * 使用优惠券
     * @param int $order_id
     * @param int $coupon_id
     */
    public function useCoupon($order_id,$coupon_id){
        
        $arr['id']         = $coupon_id;
        $arr['begin_time'] = array('lt',time());
        $arr['end_time']   = array('gt',time());
        $couponInfo = D('Coupon')->where($arr)->find();
        if($couponInfo){
            $cond['open_id']   = session('openid');
            $cond['status']    = 0;
            $cond['coupon_id'] = $coupon_id;
            $userCouponInfo = $this->where($cond)->find();
            if($userCouponInfo){
                $orderInfo = D('TicketOrder')->getOrderInfo($order_id);
                if($orderInfo['third_party_pay'] > $couponInfo['price']){
                    $save['third_party_pay'] = $orderInfo['third_party_pay']-$couponInfo['price'];
                    $save['coupon_id'] = $coupon_id;
                    $save['coupon_pay'] = $couponInfo['price'];
                    $saveResult = D('TicketOrder')->where(array('id'=>$order_id))->save($save);
                    //用户优惠券状态修改
                    $cSave['status'] = 1;
                    $cSave['usetime'] = time();
                    $cSave['coupon_pay'] = $couponInfo['price'];
                    $cSave['order_id'] = $order_id;
                    $cResult = $this->where(array('id'=>$userCouponInfo['id']))->save($cSave);
                    if($saveResult && $cResult){
                        return true;
                    }
                }else{
                    return array('code'=>2,'msg'=>'优惠券金额太大');
                }
            }else{
                return array('code'=>2,'msg'=>'用户优惠券不存在');
            }
        }else{
            return array('code'=>2,'msg'=>'优惠券已过期');
        }
        
    }
    
    public function getCoupon(){
        
        $tbCoupon = \Wechat\Model\CouponModel::COUPON;//需要数据表
        $cond['a.open_id'] = session('openid');
        //药品各种参数
        $result =  $this->table(self::USER_COUPON.' a')
                        ->join('left join '.$tbCoupon.' b ON a.coupon_id=b.id')
                        ->field('b.title,b.begin_time,b.end_time,a.status')
                        ->where($cond)
                        ->order('a.id DESC')
                        ->select();
        
        foreach($result as $k=>$v){
            $result[$k]['begin_time'] = $v['begin_time'] ? date('Y.m.d',$v['begin_time']) : '';
            $result[$k]['end_time'] = $v['end_time'] ? date('Y.m.d',$v['end_time']) : '';
        }
        
        return $result;
    }


}