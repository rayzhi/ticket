<div class="row">
    <div class="col-xs-12">
        <div class="well">
              <div class="btn-group ">
                <a class="btn btn-success dropdown-toggle" href="<?php echo UC('Admin/Coupon/couponAdd')?>">&nbsp;添&nbsp;&nbsp;加&nbsp;</a>
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
                        <th>价值</th>
                        <th>有效期</th>
                        <th>备注</th>
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
                        <td>{$vo.title}</td>
                        <td>{$vo.price}</td>
                        <td>{$vo.begin_time|date="Y.m.d",###} - {$vo.end_time|date="Y.m.d",###}</td>
                        <td>{$vo.note}</td>
                        <td>
                            <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
                                <a class="green" href="<?php echo UC('Admin/Coupon/couponEdit')?>?uid={$vo.id}" title="编辑">
                                    编辑
                                </a>
                                <a class="red" href="javascript:;" onclick="remove_option({$vo.id}, '您确认要删除这条数据吗?')" title="删除">
                                    删除
                                </a>
                                <a href="#">
                                    查看领取情况
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
            $.post("<?php echo UC('Admin/Coupon/couponDel')?>", {"id":id}, 
              function(data){
                alert(data.info);
                if (data.status == 1) {
                    window.location.reload();
                }
            },'json');
        }
    }
</script>