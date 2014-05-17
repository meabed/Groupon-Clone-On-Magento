$j_mw(function(){	
	$j_mw("#credit_config_credit_money_rate, #credit_options_exchange_to_point_credit_point_rate").addClass('required-entry');
	$j_mw("#credit_capcha_perturbation").addClass('validate-number');
	$j_mw("#credit_options_max_credit_to_checkout, #credit_options_send_credit_max_recipients, #credit_options_send_credit_max_credit_to_send, #credit_capcha_image_width, #credit_capcha_image_height, #credit_capcha_code_length, #credit_capcha_text_transparency_percentage, #credit_capcha_num_lines").addClass('validate-digits');

	//check Rewardpoints module
	url = window.location.toString();
	url = url.substring(0,url.indexOf('admin'));
	url = url+"credit/rewardpoints/check";
	$j_mw.ajax({ 
		url: url, 
		context: document.body, 
		success: function(data){
			objs = "#credit_options_exchange_to_point_enabled, #credit_options_exchange_to_point_credit_point_rate";
			if(data =="0")
			{
				$j_mw(objs).attr('disabled','disabled');
				$j_mw(objs).parent().children("p").append('<br />Reward Points Module require');
				$j_mw(objs).parent().children("p").css("color","#FF0000").css("font-weight","bold");
			}else{
				$j_mw(objs).parent().children("p").remove();
			}
      	}
	});
});