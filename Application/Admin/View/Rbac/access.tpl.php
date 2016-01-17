<style>
 	.all-check{border:1px solid grey;width:40px;cursor:pointer;text-align:center;background:green;color:white;height:30px;padding-top:3px;font-size:13px;}
</style>

<div class="page-header">
	<h1>
		添加页面操作权限
		<a href="javascript:history.back()">
			<button class="btn btn-sm" style="float:right;margin-right:35px;">
				返&nbsp;&nbsp;回&nbsp;&nbsp;
			</button>
		</a> 
	</h1>
</div>
<div class="row">
	<div class="col-xs-12">
		<form class="form-horizontal" role="form" method="post"  enctype="multipart/form-data" action="javascript:;" >
			
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-3">角色</label>
				<div class="col-sm-9">
					<select class="col-xs-10 col-sm-2" name="role" id="role" >
						<option value="">----</option>
						<volist name="role" id="vo">						
							<option value="{$vo.id}">{$vo.name}</option>	
						</volist>			
					</select>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1">页面操作</label>
				<div class="col-sm-9" id="allCheck">
					<div class="all-check" onclick="allCheck();">全选</div><br />
					<volist name="operation" id="vo">
			        	<label>
			                <input type="checkbox" class="ace allarea h-operation" value="{$vo.e_name}" >
			            	<span class="lbl">{$vo.name}</span>                       
			        	</label>
			            <span style="margin-left:5px;"></span>
		            </volist>
				</div>
			</div>
			
			<if condition="$info.id neq ''">
				<input type="hidden" name="id" value="{$info.id}" />
			</if>
			
			<div style="height:20px;"></div>

			<div class="clearfix form-actions">
				<div class="col-md-offset-3 col-md-9">
					<button class="btn btn-info" type="submit" id="tijiao">
						<i class="icon-ok bigger-110"></i>
						提交
					</button>
					&nbsp; &nbsp; &nbsp;
					<button class="btn" type="reset">
						<i class="icon-undo bigger-110"></i>
						重置
					</button>
				</div>
			</div>

			<div class="hr hr-24"></div>
		</form>
	</div>
</div>
<script src="__STATIC__/theme/ace/assets/js/date-time/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
jQuery(function($) {
	$().ready(function() {
		$("form").validate({
		   submitHandler: function(form) 
		   {      
			   var data = $("form").serialize();
				$.post("<?php echo UC('Course/'.$action_name)?>", data, 
				  function(data){
					alert(data.info);
		            if (data.status == 1) {
		                window.location.href = data.url;
		            } 
		        },'json');   
		   } 
		});
	});
});

//区确认后
$('#role').change(function(){

	alert($(this).val());
	
	$.post("<?php echo UC('Admin/Logistic/getStreet')?>", {"districtids":districtids}, 
		function(data){
			$('#h-street').html(data.info);
			$('#HFade').hide();
			$('#streetBox').hide();       
	 },'json');
    
})

//全选
function allCheck(){
	$('#allCheck').find('.h-operation').prop("checked","checked");
}
</script>

