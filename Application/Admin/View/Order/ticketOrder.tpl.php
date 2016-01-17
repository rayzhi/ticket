<style>
	.HzjSerach{margin-top:6px;margin-left:5px;}
	.HzjTop{margin-top:5px;}
	.HzjSelect{height:37px;}
</style>

<div class="row">
	<div class="col-xs-12">
        
        <div class="well">
             <!-- 搜索 -->        
              <form class="form-horizontal btn-group h-form" role="form" method="post" enctype="multipart/form-data" action="javascript:;">
			  	
					<label class="col-xs-1 control-label no-padding-right width-auto">下单日期</label>
                    <div class="col-sm-2 grid-search-field width-auto HzjSerach">
                    	<input class="form-control search-query date-picker" name="add_date" id="dateRangePicker" type="text" value="" />
                    </div>
                    
					<div class="col-sm-2 grid-search-field width-auto HzjSerach">
						<label class="col-xs-1 control-label no-padding-right width-auto">订单状态</label>
						<select class="col-xs-10 col-sm-2 HzjSelect" name="status" id="status" style="width:120px;">
							<option value="">----</option>
							<volist name="status" id="vo">
							 <option value="{$key}">{$vo}</option>
							</volist>
						</select>          	
					</div>
					
                    <div class="btn HzjTop btn-success" style="margin-left:10px;margin-top:5px;" onclick="search();">搜&nbsp;&nbsp;索</div>
					<input name="pagesize" type="hidden" id="Hpagesize" value="" />
                                                     
			  </form>
			  <!-- 搜索 -->
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
						<th>订单状态</th>
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

<script src="__STATIC__/theme/ace/assets/js/date-time/daterangepicker.min.js"></script>
<link rel="stylesheet" href="__STATIC__/theme/ace/assets/css/daterangepicker.css" />
<script src="__STATIC__/theme/ace/assets/js/date-time/moment.min.js"></script>

<script type="text/javascript">
	
	function detail(id){
		index = layer.open({
            type: 2,
            title: '订单详情',
            maxmin: true,
            shadeClose: true, //点击遮罩关闭层
            area : ['600px', '500px'],
            content: "<?php echo UC('Admin/Order/orderDetail')?>?id="+id
        });   
        layer.full(index);
	}

//搜索
function search(){
	$('#Hpagesize').val($('#pagesize option:selected').val());//分页参数
	var data = $("form").serialize();
	$.post("<?php echo UC('Order/ticketOrder')?>", data, 
	  function(data){
		  $('#list').html(data.info);
		  supage('pageNav','cengp_Page','',1, data.url, pagesize);//分页参数
    },'json');
}

//时间控件
$('#dateRangePicker').daterangepicker({format : 'YYYYMMDD'}).prev().on(ace.click_event, function(){
    $(this).next().focus();
});

</script>