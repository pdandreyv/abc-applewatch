;(function($) {
	$.fn.ajaxSubmit = function(options) {
		var form = this,
			formData = new FormData($(form).get(0)),
			type = 'html',
			iframe = false;
		if (options.dataType) type = options.dataType;
		if (options.iframe) iframe = options.iframe;
		if (iframe) type = 'html';
		//console.log(options);
		//console.log(type+' '+iframe);
		$.ajax({
			url: options.url,
			type: 'POST',
			dataType: type,
			contentType: false,
			processData: false,
			data: formData,
			cache: false,
			beforeSend: function(){
				if (options.beforeSubmit) {
					options.beforeSubmit();
				}
			},
			success: function(response, textStatus){
				var data = response;
				//console.log('response');
				//console.log(response);
				if (iframe) {
					//обрезаем <textarea>
					data = data.slice(0, -11);
					data = data.substring(10);
					//конвертируем в json
					data = JSON.parse(data);
				}
				//console.log('data');
				//console.log(data);
				if (options.success) {
					options.success(data);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				if (options.error) {
					options.error();
				}
			},
			complete: function(XMLHttpRequest, textStatus){
			}
		});
	}
})(jQuery);