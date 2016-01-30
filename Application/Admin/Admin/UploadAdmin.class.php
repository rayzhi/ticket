<?php

/**
 * 药师帮后台----后台上传控制器
 *
 * PHP version 5.5
 *
 * @category	qrug
 * @package     Pharmacist
 * @subpackage  config
 * @copyright   2014 GZSD
 * @version     SVN: $Id: class.php 43 2014-12-12 16:23:59 Cengp $
 */
namespace Admin\Admin;

class UploadAdmin extends CommonAdmin {
	
	/**
	 * 编辑器图片上传
	 * 
	 * @return json
	 */
	public function keditor_uploadAct() {
		$upload = new \Think\Upload (); // 实例化上传类
		$upload->maxSize = 314572800; // 设置附件上传大小
		$upload->exts = array (
				'jpg',
				'gif',
				'png',
				'jpeg' 
		); // 设置附件上传类型
		$dir = I ( 'get.dir' );
		$upload->rootPath = './'; // 保存根路径
		$upload->savePath = 'data/keditor/' . $dir . '/' . date ( 'Y-m' ) . '/';
		// 创建目录
		check_dir ( $upload->savePath );
		
		// 执行上传
		if (! $info = $upload->uploadOne ( $_FILES ['imgFile'], $upload->savePath )) {
			// 上传错误提示错误信息
			echo json_encode ( array (
					'error' => 1,
					'message' => "1" . $upload->getError () 
			) );
		} else {
			// 上传成功 获取上传文件信息
			$fileUrl = $info ['savepath'] . $info ['savename'];
			$fullPath = __BASE__ . $fileUrl;
			echo json_encode ( array (
					'error' => 0,
					'url' => $fullPath 
			) );
		}
		exit ();
	}
	public function swfuploadAct() {
		if (IS_POST) {
			$config = array (
					'rootPath' => './',
					'savePath' => 'data/images/' . date ( 'Y-m' ) . '/',
					'maxSize' => 11048576,
					'saveName' => array (
							'uniqid',
							'' 
					),
					'exts' => array (
							'jpg',
							'gif',
							'png',
							'jpeg',
							"txt",
							'zip' 
					),
					'autoSub' => false 
			);
			$upload = new \Think\Upload ( $config );
			check_dir ( $upload->savePath );
			$info = $upload->upload ();
			if ($info) {
				$first = array_shift ( $info );
				echo $upload->savePath . $first ['savename'];
				exit ();
			} else {
				exit ( $upload->getError () );
			}
		} else {
			$this->display ();
		}
	}
	public function img_uploadAct() {
		
		$file = str_replace ( ' ', '+', $_POST ['Data'] );
		$Fname = $_POST ['Name'];
		$tmparr = explode ( '.', $Fname );
		$suffix = $tmparr [count ( $tmparr ) - 1];
		$imgData = base64_decode ( $file );
		$savename = uniqid () . '.' . $suffix;
		//如果有传savemodule，则用savemodule
		if($_POST ['module'] == 'other' && $_REQUEST['savemodule']){
			$_POST ['module'] = $_REQUEST['savemodule'];
		}
		
		$p = UPLOAD_PATH . '/' . $_POST ['module'] . '/' . date ( 'Y' ) . '/' . date ( 'm' ) . '/' . date ( 'd' ) . '/';
		
		check_dir ( $p );
		$paths = $p . $savename;
		// 生成图片
		if (file_put_contents ( $paths, $imgData )) {
			$data ['url'] = 'data/img/pharmacy/' . $_POST ['module'] . '/' . date ( 'Y' ) . '/' . date ( 'm' ) . '/' . date ( 'd' ) . '/' . $savename;
			echo $data ['url'];
			exit ();
		}
	}

	/**
	 * 上传图片更新数据库数据
	 */
	function updateTableAct(){
		$id_name = I("POST.id_name");
		$id = I("POST.id");
		$table = I("POST.table");
		$primary_key = I("POST.primary_key");
		$key_value = I("POST.key_value");
		$url_name = I("POST.url_name");
		$url_value = I("POST.url_value");
		$field_name = I("POST.field_name");
		$field_value = I("POST.field_value");
		$del = I("POST.del");
/*		$str = implode('-', array($id_name,$id,$table,$primary_key,$key_value,$url_name,$url_value,$field_name,$field_value,$del));
$this->success($str);*/
		if ($del == TRUE) {
			D($table)->delete($key_value);
			$this->success("图片删除成功！");
		}else{
			if ($key_value) {
				$data = array(
					$primary_key=> $key_value,
					$id_name	=> $id,
					$url_name	=> $url_value
					);
				$result = D($table)->save($data);
				$this->success('图片修改成功！');
			}else{
				$data = array(
					$id_name	=> $id,
					$url_name	=> $url_value,
					$field_name	=> $field_value
					);
				$result = D($table)->add($data);
				$this->success($result);			
			}			
		}


	}	

	/**
	 * 删除文件
	 */
	function delAct() {
		$id = I("POST.id");
		$src = APP_ROOT.'public/'.str_replace(__BASE__, '', $_POST['src']);
		if (file_exists($src)){
			unlink($src);			
		}
		if($id){
			$table = I("POST.table");
			$p_key = I("POST.p_key");
			$field_name = I("POST.field_name");
			if($table && $p_key && $field_name){
				$cond[$p_key] = $id;
				$data[$field_name] = '';
				D($table)->where($cond)->save($data);
			}
		}
		$this->success('删除成功');
		exit();
	}
	
	/**
	 * 删除文件(适应新图片添加方法)
	 */
	function del2Act() {
		$id = I ( "POST.id" );
		$src = APP_ROOT . 'website/' . str_replace ( __BASE__, '', $_POST ['src'] );
		$table = I ( "POST.table" );
		$p_key = I ( "POST.p_key" );
		$field_name = I ( "POST.field_name" );
		$cond [$p_key] = $id;
		$idExist = D($table)->where($cond)->find();		//判断id是否存在
		if (file_exists ( $src ) && !$idExist) {
			unlink($src);
			$this->success('删除成功！');
		}
		if (file_exists ( $src ) && $id && $table && $p_key && $field_name) {
			$data [$field_name] = NULL;
			D ( $table )->where ( $cond )->save ( $data ); // 删除数据库字段值
			unlink ( $src );
			$this->success ( '删除成功！' );
		} else {
			$this->success ( '删除失败！' );
		}
		exit ();
	}
	
	/**
	 * 添加预览图 删 除缩略图
	 */
	public function del_one_imgAct() {
		if (IS_POST) {
			$url = I ( 'post.src' );
			$url = APP_ROOT . 'website' . $url;
			$del_img = unlink ( $url );
			
			if ($del_img) {
				$this->success ( "删除成功！" );
			} else {
				$this->success ( "删除失败！" );
			}
		}
	}
	
	/**
	 * 删除已存在的预览图
	 */
	public function del_exist_imgAct() {
		if (IS_POST) {
			$url = I ( 'post.url' );
			$url = APP_ROOT . 'website' . $url;
			$del_img = unlink ( $url );
			if ($del_img) {
				$this->success ( "删除成功！" );
			} else {
				$this->success ( "删除失败！" );
			}
		}
	}
	
	
	/*******************************  拖拽上传图片       ****************************************/
	
	public function ImageCompressionAct() {
		
		$file = str_replace ( ' ', '+', $_POST ['Data'] );
		$Fname = $_POST ['Name'];
		$tmparr = explode ( '.', $Fname );
		$suffix = $tmparr [count ( $tmparr ) - 1];
		$imgData = base64_decode ( $file );
		
		
		$tmparr = explode ( '.', $Fname );
		$suffix = $tmparr [count ( $tmparr ) - 1];
		
		$savename = uniqid () . '.' . $suffix;
	
		$p = UPLOAD_PATH . '/' . $_POST ['module'] . '/' . date ( 'Y' ) . '/' . date ( 'm' ) . '/' . date ( 'd' ) . '/';
	
		check_dir ( $p );
		$paths = $p . $savename;
		// 生成图片
		if (file_put_contents ( $paths, $imgData )) {
			$data ['url'] = 'data/img/pharmacy/' . $_POST ['module'] . '/' . date ( 'Y' ) . '/' . date ( 'm' ) . '/' . date ( 'd' ) . '/' . $savename;
			echo $data ['url'];
			exit ();
		}
		
	}
	
	/**
	 * 删除已存在的预览图
	 */
	public function del_exist_picAct() {
		if (IS_POST) {
			$url = I ( 'post.pname' );		
			$url = APP_ROOT . 'website/' . $url;
			$del_img = unlink ( $url );
			if ($del_img) {
				$this->success ( "删除成功！" );
			} else {
				$this->success ( "删除失败！" );
			}
		}
	}
	
	/**
	 * 用dropzone上传文件
	 */
	function dropzoneAct() {

		$image = $_FILES["file"]["tmp_name"];
		$Fname = $_FILES["file"]["name"];
		$tmparr = explode('.', $Fname);
		$suffix = $tmparr[count($tmparr)-1];
		$savename =  uniqid().'.'.$suffix;
		
	   	$module = $_POST['module'] ? $_POST['module'] : 'other';
   		$p = UPLOAD_PATH.'/'. $module .'/'.date('Y').'/'.date('m').'/'.date('d').'/';
   		
   		check_dir($p);
   		$paths = $p.$savename;
		$result = move_uploaded_file($image,$paths);
		
		if($result){
			$data ['url'] = 'data/img/pharmacy/'. $module .'/'.date('Y').'/'.date('m').'/'.date('d').'/'.$savename;
			echo $data ['url'];
		}
		exit;
		
	}


	/**
	 * 用dropzone上传文件
	 */
	function dropzoneUploadAndSaveAct() {

		$image = $_FILES["file"]["tmp_name"];
		$Fname = $_FILES["file"]["name"];
		$tmparr = explode('.', $Fname);
		$suffix = $tmparr[count($tmparr)-1];
		$savename =  uniqid().'.'.$suffix;
		
	   	$module = $_POST['module'] ? $_POST['module'] : 'other';
	   	$keyId = $_POST['key_id'];
	   	$tableName = $_POST['table_name'];
	   	$updateField = $_POST['update_field'];
	   	$pkeyField = $_POST['pkey_field'];
	   	$logoUrlSrc = $_POST['logourl_src'];
	   	if (!$keyId || !$tableName || !$updateField || !$pkeyField) exit;

   		$p = UPLOAD_PATH.'/'. $module .'/'.date('Y').'/'.date('m').'/'.date('d').'/';
   		if (stripos($logoUrlSrc, __BASE__) !== FALSE) $logoUrlSrc = str_replace( __BASE__, '', $logoUrlSrc);
   		if (stripos($logoUrlSrc, __IMG_URL__) !== FALSE) $logoUrlSrc = str_replace( __IMG_URL__, '', $logoUrlSrc);
   		$src = APP_ROOT . 'website/' . $logoUrlSrc;

   		$whereSql[$pkeyField] = $keyId;
   		$isExist = D($tableName)->where($whereSql)->count(1);  // 判断数据是否存在
   		if (!$isExist) {  // 不存在
   			if (file_exists($src)) unlink($src);  // 存在文件就删除
   		}else{
   			check_dir($p);
	   		$paths = $p.$savename;
			$result = move_uploaded_file($image,$paths);
			
			if($result){  // 上传图片成功
				$uploadUrl = 'data/img/pharmacy/'. $module .'/'.date('Y').'/'.date('m').'/'.date('d').'/'.$savename;
				$saveData[$updateField] = $uploadUrl;
				$update_result = D($tableName)->where($whereSql)->save($saveData);
				if ($update_result !== false) {  // 更新数据库字段成功
					if (file_exists($src)) unlink($src);  // 存在文件就删除
					echo $uploadUrl;
				}
			}
   		}
		exit;	
	}
}
