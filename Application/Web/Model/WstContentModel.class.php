<?php 
/**
 * 公告通知表
 * PHP version 5.5
 *
 * @category	Web
 * @package     Web
 * @subpackage  Model
 * @copyright   2016 GDWST
 * @version     GIT: index.php 43 2016-01-14 21:23:59 Huangzj $
 */
namespace Web\Model;
use Think\Model;

class WstContentModel extends Model {
    
    protected $tableName = 'wst_content';
	
    public function getContent($page,$pagesize,$sortid){
        
        $data = $this->field('articleid,title,addtime')
                     ->where(array('sortid'=>$sortid))
                     ->order('articleid DESC')
                     ->limit($page,$pagesize)
                     ->select();
        
        $data = string_gb2312_to_utf8($data);
        foreach($data as $k=>$v){
            $data[$k]['addtime'] = date('Y-m-d',strtotime($v['addtime']));
            $data[$k]['addtime1'] = date('m.d',strtotime($v['addtime']));
        }
        return $data;
        
    }
	
}