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
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">用户名 </label>

                <div class="col-sm-9">
                    <input type="text" name="account" class="required" value="{$info.account}" placeholder="用户名" class="col-xs-10 col-sm-5" />
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">密码 </label>

                <div class="col-sm-9">
                    <if condition="$info.account eq ''">
                        <input type="text" name="password" class="required" placeholder="密码" class="col-xs-10 col-sm-5" />
                    <else />
                        <input type="text" name="password" placeholder="密码" class="col-xs-10 col-sm-5" />
                        <input type="hidden" name="uid" value="{$info.uid}" />
                        <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="留空则为不修改。" title="" data-original-title="">?</span>
                    </if>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">角色</label>

                <div class="col-sm-9">
                    <select name="role_id" class="col-xs-10 col-sm-2 valid">
                        <volist name="role_list" id="vo">
                        <if condition="$vo.current eq 1">
                            <option value="{$vo.id}" selected="selected" >{$vo.name}</option>
                        <else />
                            <option value="{$vo.id}">{$vo.name}{$info.current}</option>
                        </if>
                        </volist>                 
                    </select>
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
                    $.post("<?php echo UC('User/'.$action_name)?>", data, 
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

