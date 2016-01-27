<script src="__STATIC__/theme/ace/assets/js/date-time/bootstrap-datepicker.min.js"></script>
<script src="__STATIC__/theme/ace/assets/js/date-time/bootstrap-timepicker.min.js"></script>
<link rel="stylesheet" href="__STATIC__/theme/ace/assets/css/bootstrap-timepicker.css">

<div class="page-header">
    <h1>
        {$headline}
        <a href="<?php echo UC('Admin/Space/index')?>">
            <button class="btn btn-sm" style="float:right;margin-right:35px;">
                返回场地列表
            </button>
        </a>
    </h1>
</div>
<div class="row">
    <div class="col-xs-12">
        <form class="form-horizontal" role="form" method="post"  enctype="multipart/form-data" action="javascript:;" >
            
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">场地名称 </label>

                <div class="col-sm-9">
                    <input type="text" name="name" class="required" value="{$info.name}" placeholder="场地名称" class="col-xs-10 col-sm-5" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">跳转地址</label>

                <div class="col-sm-9">
                    <input type="text" name="url" class="required" value="{$info.url}" placeholder="跳转地址" class="col-xs-10 col-sm-5" />
                </div>
            </div>

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
                    $.post("<?php echo UC('Space/addSpace');?>", data, 
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

