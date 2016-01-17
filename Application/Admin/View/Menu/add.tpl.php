<div class="page-header">
	<h1>
		{$headline}
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
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1">英文名称</label>

				<div class="col-sm-9">
					<input type="text" name="e_name" required value="{$info.e_name}" class="col-xs-10 col-sm-4" />
				</div>
			</div>
			
			
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1">中文名称</label>

				<div class="col-sm-9">
					<input type="text" name="name" required value="{$info.name}" class="col-xs-10 col-sm-4" />
				</div>
			</div>
			
			<if condition="$info.id neq ''">
				<input type="hidden" name="id" value="{$info.id}" />
			</if>

			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1">是否启用</label>

				<div class="col-sm-9 inline">
					<if condition="$info.status eq 1">
						<label>
							<input name="status" type="radio" class="ace" value="1"  checked />					
							<span class="lbl">启用</span>
						</label>
						<label>
							<input name="status" type="radio" class="ace" value="0"  />
							<span class="lbl">禁用</span>
						</label>
					<else />
						<label>
							<input name="status" type="radio" class="ace" value="1"  />					
							<span class="lbl">启用</span>
						</label>
						<label>
							<input name="status" type="radio" class="ace" value="0" checked />
							<span class="lbl">禁用</span>
						</label>
					</if>
				</div>
			</div>
			
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
				$.post("<?php echo UC('Menu/'.$action_name)?>", data, 
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
</script>

