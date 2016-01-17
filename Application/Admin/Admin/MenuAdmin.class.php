<?php

namespace Admin\Admin;
use \Common\Lib\Pclass as p;

class MenuAdmin extends CommonAdmin {

    /**
     *  显示菜单
     */
    public function indexAct() {
    	$_SESSION['admin_menu_index'] = "Menu/index";
        $result = D("Menu")->order(array("listorder" => "ASC"))->select();
        $tree = new p\Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . UC("Menu/menu_add", array("parentid" => $r['id'], "menuid" => $_GET['menuid'])) . '">添加子菜单</a> | <a href="' . UC("Menu/edit", array("id" => $r['id'], "menuid" => $_GET['menuid'])) . '">修改</a> | <a class="J_ajax_del" href="' . UC("Menu/delete", array("id" => $r['id'], "menuid" => I("get.menuid"))) . '">删除</a> ';
			$r['status'] = $r['status'] ? "显示" : "不显示";
			$array[] = $r;
		}
		
		$tree->init($array);
		
		$str = "<tr id='div_[\$id]'>
						<td class='center'>
							<label>
								<input type='checkbox' class='ace' name='[\$id]' />
								<span class='lbl'></span>
							</label>
						</td>
						<td>\$id</td>
						<td>\$spacer\$name</td>
						<td>\$status</td>
						<td>
							<div class='visible-md visible-lg hidden-sm hidden-xs action-buttons'>
								<a class='green' href='".UC("Menu/menu_add")."?parentid=\$id' title='添加子菜单'>
									<i class='icon-zoom-in bigger-130'></i>
								</a>
								<a class='green' href='".UC("Menu/menu_edit")."?id=\$id' title='编辑'>
									<i class='icon-pencil bigger-130'></i>
								</a>
								<a class='red' href='javascript:;' onclick='remove_vote(\$id)' title='删除'>
									<i class='icon-trash bigger-130'></i>
								</a>
							</div>
						</td>
					</tr>";
        
        
        $categorys = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorys);
        $this->display();
    }
    
    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    protected function _get_level($id, $array = array(), $i = 0) {
    
    	if ($array[$id]['parentid']==0 || empty($array[$array[$id]['parentid']]) || $array[$id]['parentid']==$id){
    		return  $i;
    	}else{
    		$i++;
    		return $this->_get_level($array[$id]['parentid'],$array,$i);
    	}
    
    }
    
    public function lists(){
    	$_SESSION['admin_menu_index']="Menu/lists";
    	$result = D("Menu")->order(array("app" => "ASC","model" => "ASC","action" => "ASC"))->select();
    	$this->assign("menus",$result);
    	$this->display();
    }
	
    /**
     *  添加菜单
     */
    public function menu_addAct(){
    	if (IS_POST) {
    		if (D("Menu")->create()) {
    			if (D("Menu")->add() !== false) {
    				$this->_admin_log(D("Menu")->add(), '添加菜单',"添加菜单成功！");
    				$this->success("添加成功！", UC("Menu/index"));
    			} else {
    				$this->_admin_log(0, '添加菜单',"添加菜单失败！");
    				$this->error("添加失败！");
    			}
    		} else {
    			$this->error(D("Menu")->getError());
    		}
    	} else {
    		$result = D("Menu")->field('id,name,parentid')->order(array("listorder" => "ASC"))->select();
    		$tree = new p\Tree();
    		$parentid = intval(I("get.parentid"));
    		foreach ($result as $r) {
    			$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
    			$array[] = $r;
    		}
    		$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
    		$tree->init($array);
    		$select_categorys = $tree->get_tree(0, $str);
    		$this->assign("select_categorys", $select_categorys);
    		$this->assign("headline",    "添加菜单");
    		$this->assign("action_name", ACTION_NAME);
    		$this->display("Menu:menu_oper");
    	}
    }
    
    /**
     *  编辑菜单
     */
    public function menu_editAct(){
    	if (IS_POST) {
    		if (D("Menu")->create()) {
    			if (D("Menu")->save() !== false) {
    				$this->_admin_log(I('post.id'), '编辑菜单',"成功！");
    				$this->success("更新成功！", UC("Menu/index"));
    			} else {
    				$this->_admin_log(I('post.id'), '编辑菜单',"失败！");
    				$this->error("更新失败！");
    			}
    		} else {
    			$this->_admin_log(I('post.id'), '编辑菜单',"失败！".D("Menu")->getError());
    			$this->error(D("Menu")->getError());
    		}
    	} else {
    		$id = intval(I("get.id"));
    		if(!$id){
    			$this->error('编辑项不存在！');
    		}
    		$rs = D("Menu")->where(array("id" => $id))->find();
    		$result = D("Menu")->field('id,name,parentid')->order(array("listorder" => "ASC"))->select();
    		$tree = new p\Tree();
    		foreach ($result as $r) {
    			$r['selected'] = $r['id'] == $rs['parentid'] ? 'selected' : '';
    			$array[] = $r;
    		}
    		$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
    		$tree->init($array);
    		$select_categorys = $tree->get_tree(0, $str);
    		$this->assign("info", $rs);
    		$this->assign("select_categorys", $select_categorys);
    		$this->assign("headline",    "编辑菜单");
    		$this->assign("action_name", ACTION_NAME);
    		$this->display("Menu:menu_oper");
    	}
    }
    

    /**
     *  删除
     */
    public function del_menuAct() {
        $id = intval(I("post.id"));
        if(!$id){
        	$this->error("删除项不存在！");
        }
        $count = D("Menu")->where(array("parentid" => $id))->count();
        if ($count > 0) {
            $this->error("该菜单下还有子菜单，无法删除！");
        }
        if (D("Menu")->delete($id)!==false) {  
            $this->success("删除菜单成功！");
        } else {
            $this->error("删除失败！");
        }
    }


    //排序
    public function listorders() {
        $status = parent::_listorders(D("Menu"));
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }
    
    public function spmy_export_menu(){
    	$menus=D("Menu")->order(array("app" => "ASC","model" => "ASC","action" => "ASC"))->select();
    	$menus_tree=array();
    	
    	$apps=scandir(SPAPP);
    	import('@.ORG.Dir');
    	$dir=new \Dir();
    	foreach ($apps as $a){
    		if(is_dir(SPAPP.$a)){
    			if(!(strpos($a, ".") === 0)){
    				$menudir=SPAPP.$a."/Menu";
    				$dir->del($menudir);
    			}
    		}
    	}
    	
    	foreach ($menus as $m){
    		$mm=$m;
    		unset($mm['app']);
    		unset($mm['model']);
    		unset($mm['id']);
    		unset($mm['parentid']);
    		$menus_tree[$m['app']][$m['model']][]=$mm;
    	}
    	foreach ($menus_tree as $app=>$models){
    		$menudir=SPAPP.$app."/Menu";
    		foreach ($models as $model =>$a){
    			if(!file_exists($menudir)){
    				mkdir($menudir);
    			}
    			file_put_contents($menudir."/$model.php", "<?php\treturn " . var_export($a, true) . ";?>");
    		}
    		
    	}
    	$this->display("export_menu");
    }
    
    /* public function dev_import_menu(){
    	$menus=F("Menu");
    	if(!empty($menus)){
    		$table_menu=C('DB_PREFIX')."menu";
    		D("Menu")->execute("TRUNCATE TABLE $table_menu;");
    		 
    		foreach($menus as $menu){
    			D("Menu")->add($menu);
    		}
    	}
    	
    	$this->display();
    } */
    
    public function spmy_import_menu(){
    	$apps=scandir(SPAPP);
    	$error_menus=array();
    	foreach ($apps as $app){
    		if(is_dir(SPAPP.$app)){
    			if(!(strpos($app, ".") === 0)){
    				$menudir=SPAPP.$app."/Menu";
    				$menu_files=scandir($menudir);
    				if(count($menu_files)){
    					foreach ($menu_files as $mf){
    						if(!(strpos($mf, ".") === 0) && strpos($mf,".php")){//是php文件
    							$mf_path=$menudir."/$mf";
    							if(file_exists($mf_path)){
    								$model=str_replace(".php", "", $mf);
    								$menudatas=include   $mf_path;
    								if(is_array($menudatas) && !empty($menudatas)){
    									foreach ($menudatas as $menu){
    										$action=$menu['action'];
    										
    										$where['app']=$app;
    										$where['model']=$model;
    										$where['action']=$action;
    										$old_menu=D("Menu")->where($where)->find();
    										if($old_menu){
    											$newmenu=array_merge($old_menu,$menu);
    											$result=D("Menu")->save($newmenu);
    											if($result===false){
    												$error_menus[]="$app/$model/$action";
    											}
    										}
    									}
    									/* $data=$menudatas;
    									$data['parentid']=0;
    									unset($data['items']);
    									$id=D("Menu")->add($data);
    									if(!empty($menudatas['items'])){
    										$this->_import_submenu($rootmenudatas['items'],$id);
    									} */
    								}
    							}
    							
    						}
    					}
    				}
    			}
    		}
    	}
		$this->assign("errormenus",$error_menus);
    	$this->display("import_menu");
    }
    
    private function _import_submenu($submenus,$parentid){
    	foreach($submenus as $sm){
    		$data=$sm;
    		$data['parentid']=$parentid;
    		unset($data['items']);
    		$id=D("Menu")->add($data);
    		if(!empty($sm['items'])){
    				$this->_import_submenu($sm['items'],$id);
    		}else{
    			return;
    		}
    	}
    }
    
    private function _generate_submenu(&$rootmenu,$m){
    	$parentid=$m['id'];
    	$rm=D("Menu")->menu($parentid);
    	unset($rootmenu['id']);
    	unset($rootmenu['parentid']);
    	if(count($rm)){
    		
    		$count=count($rm);
    		for($i=0;$i<$count;$i++){
    			$this->_generate_submenu($rm[$i],$rm[$i]);
    			
    		}
    		$rootmenu['items']=$rm;
    		
    	}else{
    		return ;
    	}
    	
    }
    
    
    public function spmy_getactions(){
    	$apps_r=array("Comment");
    	$groups=C("MODULE_ALLOW_LIST");
    	$group_count=count($groups);
    	$newmenus=array();
    	$g=I("get.app");
    	if(empty($g)){
    		$g=$groups[0];
    	}
    	
    	if(in_array($g, $groups)){
    		if(is_dir(SPAPP.$g)){
    			if(!(strpos($g, ".") === 0)){
    				$actiondir=SPAPP.$g."/Controller";
    				$actions=scandir($actiondir);
    				if(count($actions)){
    					foreach ($actions as $mf){
    						if(!(strpos($mf, ".") === 0)){
    							if($g=="Admin"){
    								$m=str_replace("Controller.class.php", "",$mf);
    								$noneed_models=array("Public","Index","Main");
    								if(in_array($m, $noneed_models)){
    									continue;
    								}
    							}else{
    								if(strpos($mf,"adminController.class.php") || strpos($mf,"Admin")===0){
    									$m=str_replace("Controller.class.php", "",$mf);
    								}else{
    									continue;
    								}
    							}
    							$class=A($g."/".$m);
    							$adminbaseaction=new \Common\Controller\AdminbaseController();
    							$base_methods=get_class_methods($adminbaseaction);
    							$methods=get_class_methods($class);
    							$methods=array_diff($methods, $base_methods);
    							
    							foreach ($methods as $a){
    								if(!(strpos($a, "_") === 0) && !(strpos($a, "spmy_") === 0)){
    									$where['app']=$g;
    									$where['model']=$m;
    									$where['action']=$a;
    									$count=D("Menu")->where($where)->count();
    									if(!$count){
    										$data['parentid']=0;
    										$data['app']=$g;
    										$data['model']=$m;
    										$data['action']=$a;
    										$data['type']="1";
    										$data['status']="0";
    										$data['name']="未知";
    										$data['listorder']="0";
    										$result=D("Menu")->add($data);
    										if($result!==false){
    											$newmenus[]=   $g."/".$m."/".$a."";
    										}
    									}
    								}
    							}
    						}
    						 
    		
    					}
    				}
    			}
    		}
    		
    		$index=array_search($g, $groups);
    		$nextindex=$index+1;
    		$nextindex=$nextindex>=$group_count?0:$nextindex;
    		if($nextindex){
    			$this->assign("nextapp",$groups[$nextindex]);
    		}
    		$this->assign("app",$g);
    	}
    	 
    	$this->assign("newmenus",$newmenus);
    	$this->display("getactions");
    	
    }
    
    
    
    
    
}

?>