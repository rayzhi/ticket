<?php

namespace Admin\Model;
use Think\Model;

class TicketOrderModel extends Model {

    protected $tableName = 'ticket_order';
    const TICKET_ORDER = 'ticket_order';

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
        
        $tbUser = \Admin\Model\UserModel::USER;//需要数据表
        $count  = $this->count();
        //药品各种参数
        $result = $this->table(self::TICKET_ORDER.' a')
                       ->join('left join '.$tbUser.' b ON a.open_id=b.open_id')
                       ->field('a.*,b.nickname')
                       ->order('a.id DESC')
                       ->limit($page,$pagesize)
                       ->select();

        foreach($result as $k=>$v){
            $result[$k]['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s',$v['add_time']) : '';
        }
        $array['count'] = $count ? $count : 0;
        $array['data']  = $result;
        return $array;
    }



}

?>