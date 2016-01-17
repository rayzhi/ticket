<?php

namespace Admin\Admin;

class OrderAdmin extends CommonAdmin {

    /**
     * 票订单
     */
    public function ticketOrderAct(){
    
        $this->_set_page();
        $data = D('TicketOrder')->orderList($this->_page, $this->_pagesize, $_POST);

        $count = $data['count'];
        $param = array(
            'result'   => $data['data'],                 //分页用的数组
            'count'    => $count,                 //数组的量
            'listvar'  => 'list',               //分页循环变量
            'template' => 'Order:ticketOrderlib' //ajax更新模板
        );
        $other = 'name:$("input[name=name]").val()';
        
        $this->_cjax_page($param);
        //表单顶部html以及底部html
        $bottom_html = cjax_table_bottom(array('count' => $count,'other'=>$other));
        $this->assign('bottom_html', $bottom_html);
 
        $this->display();
    }
    

}
