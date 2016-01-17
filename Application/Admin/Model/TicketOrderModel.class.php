<?php

namespace Admin\Model;
use Think\Model;

class TicketOrderModel extends Model {

    protected $tableName = 'ticket_order';
    const TICKET_ORDER = 'ticket_order';
    
    public static $orderStatus = array('待付款','已支付','已取消');

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        //array('role_id', 'require', '角色不能为空！', 1, 'regex', 3),
    );
    
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
        
        $tbUser = \Admin\Model\UserModel::USER;//需要数据表
        $count  = $this->count();
        //药品各种参数
        $result = $this->table(self::TICKET_ORDER.' a')
                       ->join('left join '.$tbUser.' b ON a.open_id=b.open_id')
                       ->where($cond)
                       ->field('a.*,b.nickname')
                       ->order('a.id DESC')
                       ->limit($page,$pagesize)
                       ->select();

        foreach($result as $k=>$v){
            $result[$k]['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s',$v['add_time']) : '';
            $result[$k]['statusName'] = $this->orderStatus[$v['status']];
        }
        $array['count'] = $count ? $count : 0;
        $array['data']  = $result;
        return $array;
    }
    
    



}

?>