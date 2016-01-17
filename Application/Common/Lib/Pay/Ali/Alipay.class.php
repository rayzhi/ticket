<?php

namespace Common\Lib\Pay\Ali;
/**
 * 
 * Alipay.php (支付宝支付模块)
 *
 * @package      	YOURPHP
 * @author          liuxun QQ:147613338 <admin@yourphp.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.yourphp.cn)
 * @license         http://www.yourphp.cn/license.txt
 * @version        	YourPHP企业网站管理系统 v2.1 2012-01-09 yourphp.cn $
 * @此注解信息不能修改或删除,请尊重我们的劳动成果,你的修改请注解在此注解下面。
 */
class Alipay {

    public $config = array();

    public function __construct($config = array()) {

        $this->config['gateway_method'] = 'POST';
        //$this->config['notify_url'] = SITE_URL . '/core/alipay_notify.php';
        //$this->config['return_url'] = SITE_URL . '/core/alipay_return.php';
        
        //$this->config['notify_url'] = 'https://www.baidu.com/';
        //$this->config['return_url'] = 'http://www.sina.com.cn/';

        $this->config['service'] = 'trade_create_by_buyer'; //标准
        $this->config['gateway_url'] = 'https://www.alipay.com/cooperate/gateway.do?';

        $this->config = array_merge($this->config, $config);
    }

    /*
         配置参数

      Array
      (
      [account] => ceo2@menkai.com
      [key] =>
      [partner] =>
      [pay_type] => 2
      [order_sn] => 201206161110134
      [order_amount] => 0.01
      [body] => 201206161110134
      )

      hao    2012/8/9 18:04
     */

    public function setConfig($config = array()) {
    	
        if (empty($config))
            return false;
        $this->config = array_merge($this->config, $config);
        
		// 设置交易方式
        if ($this->config['paytype'] == 1) {
            $this->config['service'] = 'create_partner_trade_by_buyer'; //担保
            $this->config['gateway_url'] = 'https://mapi.alipay.com/gateway.do?';
        } elseif ($this->config['paytype'] == 3) {
            $this->config['service'] = 'create_direct_pay_by_user'; //即时
            $this->config['gateway_url'] = 'https://www.alipay.com/cooperate/gateway.do?';
        } else {
            $this->config['service'] = 'trade_create_by_buyer'; //标准
            $this->config['gateway_url'] = 'https://www.alipay.com/cooperate/gateway.do?';
        }
        
        return true;
    }

    /*
      取出支付宝的链接地址，重要！



      >payurl=https://www.alipay.com/cooperate/gateway.do?_input_charset=utf-8&body=20120717175144&logistics_fee=0&logistics_payment=BUYER_PAY_AFTER_RECEIVE&logistics_type=EXPRESS&notify_url=http%3A%2F%2Fwww.menkai.com%2Findex.php%3Fapp%3Dproduct%26mod%3DOrder%26act%3DpayRespond&out_trade_no=20120717175144&partner=2088702953763082&payment_type=1&price=0.01&quantity=1&return_url=http%3A%2F%2Fwww.menkai.com%2Findex.php%3Fapp%3Dproduct%26mod%3DOrder%26act%3DpayRespond&seller_email=ceo%40menkai.com&service=create_partner_trade_by_buyer&subject=20120717175144&sign=8fea1a53afc88896c4b0c5e415a3221f&sign_type=MD5
      //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）'BUYER_PAY_AFTER_RECEIVE',

      hao 2012/6/17\18:42
     */

    public function get_payurl() {


        $parameter = array(
            'service' => $this->config['service'],
            'partner' => trim($this->config['partner']),
            '_input_charset' => 'utf-8',
            'notify_url' => trim($this->config['notify_url']),
            'return_url' => trim($this->config['return_url']),
            /* 商品信息 */
            'subject' => $this->config['subject'],
            'out_trade_no' => $this->config['order_sn'],
            'price' => $this->config['order_amount'],
            'body' => $this->config['body'],
            'quantity' => 1,
            'payment_type' => 1,
            /* 物流参数 */
            'logistics_type' => 'EXPRESS',
            'logistics_fee' => 0,
            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
            //'agent'             => $this->config['agent'], 

            /* 买卖双方信息 */
            'seller_email' => trim($this->config['account'])
        );
        if ($this->config['paytype'] == 2) {
            
            $parameter['logistics_payment'] = 'SELLER_PAY';
//            $parameter['quantity'] = '1';
            $parameter['show_url'] = 'http://ks.qdrug.cn/index.php?app=product&mod=Order&act=showCartInfo';
//            print_r($parameter);die;
        }
        ksort($parameter);
        reset($parameter);
        $param = '';
        $sign = '';

        foreach ($parameter AS $key => $val) {
            $param .= "$key=" . urlencode($val) . "&";
            $sign .= "$key=$val&";
        }

        $param = substr($param, 0, -1);
        $sign = substr($sign, 0, -1) . $this->config['key'];
        
        $payurl = $this->config['gateway_url'] . $param . '&sign=' . MD5($sign) . '&sign_type=MD5';

        return $payurl;
    }

    public function get_code() {

        $alipay_payurl = $this->get_payurl();
        $button = '<span><input type="button"  class="button" onclick="window.open(\'' . $this->alipay_payurl . '\')" value="提交" /></span>';
    }

    /*
      检查网址的数字签名

      hao 2012/8/9 16:53
     */

    public function checkSign() {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $data) {
                $_GET[$key] = $data;
            }
        }

        $seller_email = rawurldecode($_GET['seller_email']);
        //$order_sn = str_replace($_GET['subject'], '', $_GET['out_trade_no']);
        $order_sn = trim($_GET['out_trade_no']);

        /* 检查数字签名是否正确 */
        ksort($_GET);
        reset($_GET);

        $sign = '';
        foreach ($_GET AS $key => $val) {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code' && $key != 'app' && $key != 'mod' && $key != 'act') {
                $sign .= "$key=$val&";
            }
        }

        $sign = substr($sign, 0, -1) . $this->config['key'];


        if (md5($sign) != $_GET['sign']) {
            return false;
        } else
            return true;
    }

    /* 	

      &out_trade_no=20120718110451&payment_type=1&receive_address=null&seller_email=ceo%40menkai.com&seller_id=2088702953763082&subject=No%3A20120718110451&total_fee=0.01&trade_no=2012071860754910&trade_status=TRADE_FINISHED&sign=fecfa946f0f7a03a71f3ff022b49c60b&sign_type=MD5

      print_r($this->config);
      print_r($_GET);


      echo "<br>sign=".$sign;
      echo "<br>md5(sign)=".md5($sign);
      echo "<br>getsign=".$_GET['sign'];

      exit();

     */

    public function notify() {

        $result = $this->checkSign();
        
        return $result;


//         if (!empty($_POST)) {
//             foreach ($_POST as $key => $data) {
//                 $_GET[$key] = $data;
//             }
//         }

//         $seller_email = rawurldecode($_GET['seller_email']);
//         $order_sn = trim($_GET['out_trade_no']);

//         if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS' || $_GET['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS' || $_GET['trade_status'] == 'WAIT_BUYER_PAY') {
//             /* 改变订单状态 进行中 */
//             order_pay_status($order_sn, '1');
//             return true;
//         } elseif ($_GET['trade_status'] == 'TRADE_FINISHED') {
//             /* 改变订单状态  trade_status=TRADE_FINISHED  */

//             order_pay_status($order_sn, '2');
//             return true;
//         } elseif ($_GET['trade_status'] == 'TRADE_SUCCESS') {
//             /* 改变订单状态 即时交易成功 */
//             order_pay_status($order_sn, '2');
//             return true;
//         } else {
//             return false;
//         }
    }

    public function respond() {

        $result = $this->checkSign();
        
        return $result;
        
//         if (!$result)
//             return false; // 如果检查签名出错则退出

//         if (!empty($_POST)) {
//             foreach ($_POST as $key => $data) {
//                 $_GET[$key] = $data;
//             }
//         }
//         $order_sn = trim($_GET['out_trade_no']);

//         if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS' || $_GET['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS' || $_GET['trade_status'] == 'WAIT_BUYER_PAY') {
//             /* 改变订单状态 进行中 */
//             return true;
//         } elseif ($_GET['trade_status'] == 'TRADE_FINISHED') {
//             /* 改变订单状态  trade_status=TRADE_FINISHED  */

//             return true;
//         } elseif ($_GET['trade_status'] == 'TRADE_SUCCESS') {
//             /* 改变订单状态 即时交易成功 */
//             return true;
//         } else {
//             return false;
//         }
    }

}

?>