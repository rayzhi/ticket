<div class="row">
	<div class="col-xs-12">
		<if condition="$role eq 1">
        <div class="well">
        	  <div class="btn-group">
              <a class="btn btn-success dropdown-toggle" href="__URL__/user_add">&nbsp;添&nbsp;&nbsp;加&nbsp;</a>
              </div>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <div class="btn-group" id="operation">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle ">
					批操作
					<i class="icon-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-success">
					<li>
						<a href="#">批删除</a>
					</li>
				</ul>
			  </div>
        </div>   
        <else />
        <div class="space-10"></div> 
        </if>    
		<div class="table-responsive">
			<table id="sample-table-2" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="center">
							<label>
								<input type="checkbox" class="ace" />
								<span class="lbl"></span>
							</label>
						</th>
						<th>用户名</th>
						<th class="hidden-480">所属角色</th>
						<th>最后登录IP</th>
						<th>最后登录时间</th>
						<th>操作</th>
					</tr>
				</thead>

				<tbody>
					<volist name="list" id="vo">
					<tr id="div_{$vo.id}">
						<td class="center">
							<label>
								<input type="checkbox" class="ace" name="{$vo.id}" />
								<span class="lbl"></span>
							</label>
						</td>
						<td>{$vo.account}</td>
						<td class="hidden-480">{$roles[$vo['role_id']]}</td>
						<td>{$vo.loginip}</td>
						<td>{$vo.amount}</td>
						<td>
							<div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
								<a class="green" href="__URL__/user_edit?id={$vo.uid}" title="编辑">
									<i class="icon-pencil bigger-130"></i>
								</a>
								<a class="red" href="javascript:;" onclick="remove_vote({$vo.uid}, '您确认要删除这条数据吗?')" title="删除">
									<i class="icon-trash bigger-130"></i>
								</a>
							</div>
						</td>
					</tr>
					</volist>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script src="__STATIC__/theme/ace/assets/js/jquery.dataTables.min.js"></script>
<script src="__STATIC__/theme/ace/assets/js/jquery.dataTables.bootstrap.js"></script>

<script type="text/javascript">
	jQuery(function($) {
		var oTable1 = $('#sample-table-2').dataTable( {
			"aoColumns": [
		      { "bSortable": false },
		      null, null,null,null,
			  { "bSortable": false }
		     ],
		     "bProcessing": true, 
		     'bStateSave': true,
	         "sPaginationType": "bootstrap",
		     "oLanguage": {
	             "oPaginate": {
	                 "sFirst": "首页",
	                 "sLast": "末页",
	                 "sNext": "下页",
	                 "sPrevious": "上页"
	             },
	             "sEmptyTable": "表格是空的",
	             "sZeroRecords": "没有符合条件的数据",
	             "sInfo": "总共_TOTAL_条数据（当前为第_START_条到第_END_条）",
	             "sInfoEmpty": "没有符合条件的数据",
	             "sInfoFiltered": "（从_MAX_条数据中过滤）",
	             "sLengthMenu": "显示_MENU_条数据",
	             "sProcessing": "数据处理中……",
	             "sSearch": "搜索："
	         }
      	} );
		
		$('table th input:checkbox').on('click' , function(){
			var that = this;
			$(this).closest('table').find('tr > td:first-child input:checkbox')
			.each(function(){
				this.checked = that.checked;
				$(this).closest('tr').toggleClass('selected');
			});
			if ($("#sample-table-2 tbody input:checked").length > 0) {
				$("#operation button").removeClass("disabled");
		      } else {
		          $("#operation button").addClass("disabled");
		      }
				
		});
		$("#operation a").click(function(event) {
			alert('不支持批量删除管理员，请逐个删除！');
			return;
		    var HandleList =  new Array();
		    event.preventDefault();
		    $("#sample-table-2 tbody input:checked").each(function(index) {
		        HandleList[index] = this.name;
		    });
		    if (confirm('确定要批删除这些数据吗？'))
		    {
		    	$.post('__URL__/incard_batching', {"handle": HandleList}, 
	    		function(data){
	                if (data.status == 1) {
	                	alert(data.info);
	                	start = $("#sample-table-2").dataTable().fnSettings()._iDisplayStart; 
	                	total = $("#sample-table-2").dataTable().fnSettings().fnRecordsDisplay(); 
	                	window.location.reload(); 
	                	if((total-start)==1){ 
	                		if (start > 0) { 
	                			$("#sample-table-2").dataTable().fnPageChange( 'previous', true ); 
	                		} 
	                	} 
	                } else {
	                   alert(data.info);
	                }
		        },'json');
		      
		    }
		});
	})
	
	function remove_vote(id, cfm)
	{
		if (confirm(cfm))
	    {
			var url = '__URL__/del_user?id='+id;
			$.ajax({  
			  type:"get",  
			  url:url,  
			  success:　function(data){
				  if(data.status == 1){
					  alert(data.info);
	                  window.location.reload();
				  }else{
					  alert(data.info);
				  }        
			  }  
			});
	    }
	}
</script>