<style>
	.HzjSerach{margin-top:6px;margin-left:5px;}
	.HzjTop{margin-top:5px;}
	.HzjSelect{height:37px;}
</style>

<div class="row">
	<div class="col-xs-12">
        <div class="well">
        	  <div class="btn-group">
              	<a class="btn btn-success dropdown-toggle" href="<?php echo UC('Admin/Course/add_level')?>">&nbsp;添&nbsp;&nbsp;加&nbsp;</a>
              </div>		 

        </div>   
		<div class="table-responsive dataTables_wrapper">
			{$top_html}
			<table id="sample-table-2" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="center">
							<label>
								<input type="checkbox" class="ace" />
								<span class="lbl"></span>
							</label>
						</th>
						<th>订单ID</th>
						<th>下单人</th>
						<th>支付金额</th>
						<th>下单时间</th>
						<th>操作</th>
					</tr>
				</thead>

				<tbody id="list" role="alert" aria-live="polite" aria-relevant="all">
					<include file="Order:ticketOrderlib" />
				</tbody>
			</table>
			{$bottom_html}
		</div>
	</div>
</div>

<script type="text/javascript">
	
	function remove_vote(id, cfm)
	{
		if (confirm(cfm))
	    {
			$.post("<?php echo UC('Admin/Course/del_level')?>", {"id":id}, 
			  function(data){
				alert(data.info);
	            if (data.status == 1) {
	            	window.location.reload();
	            } 
	        },'json');
	    }
	}

//搜索
function search(){
	$('#Hpagesize').val($('#pagesize option:selected').val());//分页参数
	var data = $("form").serialize();
	$.post("<?php echo UC('Course/course')?>", data, 
	  function(data){
		  $('#list').html(data.info);
		  supage('pageNav','cengp_Page','',1, data.url, pagesize);//分页参数
    },'json');
}

// 刷新数据字典缓存信息
function resetSysDictCache()
{
    $.post("<?php echo UC('Admin/Setting/resetSysDictCache')?>",
      function(data){
        alert(data.info);
        if (data.status == 1) {
            window.location.reload();
        } 
    },'json');
}
</script>