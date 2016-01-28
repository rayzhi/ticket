

<div class="row">
	<div class="col-xs-12">
         
		<div class="table-responsive">
			<table id="sample-table-2" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
					    <th>票类型</th>
						<th>票sn</th>
						<th>票价</th>
						<th>有效时间</th>
					</tr>
				</thead>

				<tbody>
					<volist name="data" id="vo">
					<tr id="div_{$vo.id}">	
					    <td>{$vo.ticket_type_name}</td>					
						<td>{$vo.ticket_sn}</td>
						<td>{$vo.t_price}</td>
						<td>{$vo.etime}</td>
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
		      { "bSortable": false },
		      { "bSortable": false },		     
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
	})
	
</script>
