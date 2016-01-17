<div class="page-header">
	<h1>
		添加管理员
		<a href="__URL__/user">
			<button class="btn btn-sm" style="float:right;margin-right:35px;">
				返回列表
			</button>
		</a>
	</h1>
</div>
<div class="row">
	<div class="col-xs-12">
		<form class="form-horizontal" role="form" method="post"  enctype="multipart/form-data" action="javascript:;" >
			
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1">用户名</label>

				<div class="col-sm-9">
					<input type="text" name="account" value="{$info.account}" id="form-field-1" placeholder="用户名" class="col-xs-10 col-sm-5" />
				</div>
			</div>
			<div class="space-4"></div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1">密码</label>

				<div class="col-sm-9">
					<input type="text" name="password" id="form-field-1" placeholder="不修改请留空" class="col-xs-10 col-sm-5" />
				</div>
			</div>
			<div class="space-4"></div>

			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1">角色</label>

				<div class="col-sm-9">
					<select class="col-xs-10 col-sm-2" name="role_id">
						<volist name="roles" id="vo">
							<if condition="$vo.ifcheck eq 1">
								<option value='{$vo.id}' selected>{$vo.name}</option>
							<else />
								<option value='{$vo.id}'>{$vo.name}</option>
							</if>
						</volist>
					</select>
				</div>
			</div>
			<input type="hidden" name="id" value="{$info.uid}" />
			<div class="space-4"></div>
			
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
		$('.date-picker').datepicker({autoclose:true}).next().on(ace.click_event, function(){
			$(this).prev().focus();
		});

		$('#tijiao').click(function(){
			var data = $("form").serialize();
			$.post('__URL__/user_edit', data, 
			  function(data){
	            if (data.status == 1) {
	            	if (confirm(data.info))
	        	    {
	            		window.location.href='__URL__/user';
	        	    }
	            } else {
	               	alert(data.info);
	            }
	        },'json');
		})
	});
</script>