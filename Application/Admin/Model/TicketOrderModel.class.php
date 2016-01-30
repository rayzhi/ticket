<?php

namespace Admin\Model;
use Think\Model;

class TicketOrderModel extends Model {

    protected $tableName = 'ticket_order';
    const TICKET_ORDER = 'ticket_order';
    
    public static $orderStatus = array('待付款','已支付','已取消');
    
    /**
     * 订单列表
     * @param int   $page
     * @param int   $pagesize
     * @param array $postData
     */
    public function orderList($page,$pagesize,$postData){
        
        if(is_numeric($postData['status'])) $cond['a.status'] = $postData['status'];
        if($postData['add_date']){
            $time = explode('-',$postData['add_date']);
            $time[0] = strtotime(trim($time[0]).' 00:00:00');
            $time[1] = strtotime(trim($time[1]).' 23:59:59');
            $cond['a.add_time'] = array('between',$time);
        }
        
        $tbUser              = \Admin\Model\UserModel::USER;//需要数据表
        $tbTicketSn          = \Admin\Model\TicketSnModel::TICKET_SN;//需要数据表
        $tbTicketOrderDetail = \Admin\Model\TicketOrderDetailModel::TICKET_ORDER_DETAIL;//需要数据表

        //药品各种参数
        $count =  $this->table(self::TICKET_ORDER.' a')
                       ->where($cond)
                       ->count(1);
        
        //药品各种参数
        $result = $this->table(self::TICKET_ORDER.' a')
                       ->join('left join '.$tbUser.' b ON a.open_id=b.open_id')
                       ->join('left join '.$tbTicketOrderDetail.' c ON a.id=c.order_id')
                       ->join('left join '.$tbTicketSn.' d ON c.did=d.did')
                       ->where($cond) 
                       ->field('a.*,b.nickname,count(d.did) as amount')
                       ->group('a.id')
                       ->order('a.id DESC')
                       ->limit($page,$pagesize)
                       ->select();

        foreach($result as $k=>$v){
            $result[$k]['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s',$v['add_time']) : '';
            $result[$k]['statusName'] = self::$orderStatus[$v['status']];
        }
        $array['count'] = $count ? $count : 0;
        $array['data']  = $result;
        return $array;
    }
    
    



}

?>