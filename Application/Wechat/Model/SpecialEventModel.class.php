<?php

namespace Wechat\Model;
use \Common\Lib\Model\DrugsalesbaseModel;

class SpecialEventModel extends DrugsalesbaseModel{
 
    /**
     * 获取活动信息
     */
    public function getSpecialEventData($SpecalActivityId){

        $data = array();
                       
        $whereMap['state'] = 5;                             //正在进行的活动
        $EventData = $this->where($whereMap)->order('id DESC')->find();

        if($EventData){   
             
            $id = $EventData['id'];
            
            $areaId = $EventData['area_id']; //需要根据area_id 查询具体名称
            unset($whereMap);
            $whereMap['id'] = $areaId;
            $areaData = D('Areas')->where($whereMap)->find();
            $area = $areaData['area_name'];

            $data['EventId']            = $EventData['id'];
            $data['activityName']       = $EventData['title'];
            $data['activityStartTime']  = date('m-d H:m',$EventData['stime']);
            $data['activityEndTime']    = date('m-d H:m',$EventData['etime']);
            $data['areaId']             = $EventData['area_id'];
            $data['activityArea']       = $area; 
            $data['maxNums']            = 1;
            $data['logo']               = $EventData['logo'] ? 'http://wxb.ysbang.cn/'.$EventData['logo'] : '';
            $data['activityDetail'] = $EventData['info'];   //活动简介          

            $tempwhereMap['event_id'] = $id;
            $EventProductData = D('SpecialEventProduct')->where($tempwhereMap)->select();
            
            foreach($EventProductData as $k => $v){
                $tempArray = array('productId' => $v['id'], 'productUrl' => $v['image_url'],'price'=>$v['price'],'title'=>$v['title'],'intro'=>$v['intro']);
                $data['activityDetail'][] = $tempArray;               
            }

        }
        return $data;
                        
    }

    /**
     * 获取产品信息
     */
    public function getProductData($productId){
        $data = array();

        $whereMap['id'] = $productId;
        $data = D('SpecialEventProduct')->where($whereMap)->find();

        return $data;

    }


}