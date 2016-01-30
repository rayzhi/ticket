<div class="row">
    <div class="col-xs-12">
        <div class="well">
              <div class="btn-group ">
                <a class="btn btn-success dropdown-toggle" href="<?php echo UC('Admin/Activity/addActivityCoupon',array('activity_id'=>$activity_id))?>">添加优惠券</a>
                <a class="btn btn-success dropdown-toggle" href="<?php echo UC('Admin/Activity/index')?>">返回</a>
              </div>
        </div>   
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
                        <th>名称</th>
                        <th>面值</th>
                        <th>有效期</th>
                        <th>领取人数</th>
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
                        <td>{$vo.name}</td>
                        <td>{$vo.price}</td>
                        <td>{$vo.stime|date="Y.m.d",###} - {$vo.etime|date="Y.m.d",###}</td>
                        <td>{$vo.count}</td>
                        <td><a href="<?php echo UC('Admin/Activity/addActivityCoupon',array('activity_id'=>$activity_id,'id'=>$vo['id']))?>">编辑</a> 
                        | <a href="#" onclick="remove_option({$vo.id},'确定要删除吗？')">删除</a></td>
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
             'bStateSave': false,
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
    
    function remove_option(id, cfm)
    {
        if (confirm(cfm))
        {
            $.post("<?php echo UC('Admin/Activity/delActivityCoupon')?>", {"id":id}, 
              function(data){
                alert(data.info);
                if (data.status == 1) {
                    window.location.reload();
                }
            },'json');
        }
    }
</script>