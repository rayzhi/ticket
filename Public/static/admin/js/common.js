
window.AJAX_LOADING_TIP =  window.AJAX_DEFAULT_TIP = "处理中...";	
function ajax_start_loading(){
	$('#ajaxInditext').html(ajax_loading_tip());
	$('#ajaxIndicator').show();
	$("BODY").append('<div class="dialog_bg"></div>');
}

function ajax_stop_loading(){
	$('#ajaxInditext').html("");
	$('#ajaxIndicator').hide();
	$(".dialog_bg").remove();
}

function ajax_loading_tip(tip){
	if(typeof(tip)=='undefined'){
		return window.AJAX_LOADING_TIP;
	}
	window.AJAX_LOADING_TIP = tip;
}

function ajax_reset_default_tip(is_reset){
	if(typeof(is_reset)!='undefined' && is_reset==true)
	{
		window.AJAX_LOADING_TIP = window.AJAX_DEFAULT_TIP;
	}else{
		return window.AJAX_DEFAULT_TIP;
	}
	
}

//ajax_start_loading();

jQuery(function($) {
	$( document ).ajaxStart(function() {
  		ajax_start_loading();
	}).ajaxStop(function() {
  		ajax_stop_loading();
	}).ajaxError(function() {
  		ajax_stop_loading();
	});

	$('#gridTipIndicator button.close').click(function(){
			$('#gridTipIndicator').hide();
	});
	
	$("body").click(function(){     
		$('#gridTipIndicator').hide();
	 });
	 
	 
});






				