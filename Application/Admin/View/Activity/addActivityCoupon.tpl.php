<script src="__STATIC__/theme/ace/assets/js/date-time/bootstrap-datepicker.min.js"></script>
<script src="__STATIC__/theme/ace/assets/js/date-time/bootstrap-timepicker.min.js"></script>
<link rel="stylesheet" href="__STATIC__/theme/ace/assets/css/bootstrap-timepicker.css">

<div class="page-header">
    <h1>
        {$headline}
        <a href="<?php echo UC('Admin/Activity/lookupdetail?id='.$activity_id)?>">
            <button class="btn btn-sm" style="float:right;margin-right:35px;">
                返回优惠券列表
            </button>
        </a>
    </h1>
</div>
<div class="row">
    <div class="col-xs-12">
        <form class="form-horizontal" role="form" method="post"  enctype="multipart/form-data" action="javascript:;" >
            
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">优惠券名称 </label>

                <div class="col-sm-9">
                    <input type="text" name="name" class="required" value="{$info.name}" placeholder="名称" class="col-xs-10 col-sm-5" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">面额</label>

                <div class="col-sm-9">
                    <input type="number" name="price" class="required" value="{$info.price}" placeholder="面额" class="col-xs-10 col-sm-5" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">有效期 </label>
                <div class="col-sm-5">
                    开始时间：<input class="form-control search-query date-picker" name="stime" type="text" data-date-format="yyyy-mm-dd" value="<if condition="$info['stime']">{$info['stime']|date="Y-m-d",###}</if>" />
                    结束时间：<input class="form-control search-query date-picker" name="etime" type="text" data-date-format="yyyy-mm-dd" value="<if condition="$info['etime']">{$info['etime']|date="Y-m-d",###}</if>" />
                </div>
            </div>

            <input type="hidden" name="activity_id" value="{$activity_id}">
            <input type="hidden" name="id" value="{$info.id}">
            <div class="clearfix form-actions">
                <div class="col-md-offset-3 col-md-9">
                    <button class="btn btn-info" type="submit" id="tijiao">
                        <i class="icon-ok bigger-110"></i>
                        提交
                    </button>
                    &nbsp; &nbsp; &nbsp;
                </div>
            </div>

            <div class="hr hr-24"></div>
        </form>
    </div>
</div>
<script type="text/javascript">
    jQuery(function($) {
        $('[data-rel=popover]').popover({container:'body'});
        $().ready(function() {
            $("form").validate({
               submitHandler: function(form) 
               {      
                   var data = $("form").serialize();
                    $.post("<?php echo UC('Activity/addActivityCoupon');?>", data, 
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

    $('.date-picker').datepicker({autoclose:true}).on(ace.click_event, function(){
        });

</script>

