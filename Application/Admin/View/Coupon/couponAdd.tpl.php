<div class="page-header">
    <h1>
        {$headline}
        <a href="<?php echo UC('Admin/Coupon/home')?>">
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
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">名称 </label>

                <div class="col-sm-9">
                    <input type="text" name="name" class="required" value="{$info.name}" placeholder="名称" class="col-xs-10 col-sm-5" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">价值 </label>

                <div class="col-sm-9">
                    <input type="number" name="price" class="required" value="{$info.price}" placeholder="价值" class="col-xs-10 col-sm-5" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">有效期 </label>

                <div class="col-sm-9">
                    <input type="number" name="price" class="required" value="{$info.price}" placeholder="有效期" class="col-xs-10 col-sm-5" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">备注 </label>

                <div class="col-sm-9">
                    <input type="number" name="price" class="required" value="{$info.price}" placeholder="备注" class="col-xs-10 col-sm-5" />
                </div>
            </div>
            
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
                    $.post("<?php echo UC('Coupon/couponAdd')?>", data, 
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

