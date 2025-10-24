//манипуляции с ответом апи
/*
 v1.4.49 - api_response
 */
function api_response(response) {
	//console.log('2');
	if (response.data) {
		response.data.forEach(function (item) {

			//вставка
			if (item.method == 'append') {
				$(item.selector).append(item.content);
			}
			else if (item.method == 'prepend') {
				$(item.selector).prepend();
			}
			else if (item.method == 'html') {
				$(item.selector).html(item.content);
			}
			else if (item.method == 'text') {
				$(item.selector).text(item.content);
			}
			else if (item.method == 'replaceWith') {
				$(item.selector).replaceWith(item.content);
			}

			//прокрутка
			else if (item.method == 'scrollTop') {
				var div = $(item.selector);
				div.scrollTop(div.prop('scrollHeight'));
			}
			else if (item.method == 'scroll') {
				//скрол
				$('html, body').animate({
					scrollTop: $(item.selector).offset().top
				}, 1000);
			}

			//показать/скрыть
			else if (item.method == 'show') {
				$(item.selector).show();
			}
			else if (item.method == 'hide') {
				$(item.selector).hide();
			}
			else if (item.method == 'remove') {
				$(item.selector).remove();
			}

			//атрибуты
			else if (item.method == 'prop') {
				$(item.selector).prop(item.prop,item.content);
			}
			else if (item.method == 'removeAttr') {
				$(item.selector).removeAttr(item.content);
			}
			else if (item.method == 'addClass') {
				$(item.selector).addClass(item.prop,item.content);
			}
			else if (item.method == 'removeClass') {
				$(item.selector).removeClass(item.content);
			}

			//алерт
			else if (item.method == 'alert') {
				alert(item.content);
			}
			//переадресация
			else if (item.method == 'location') {
				window.location.href = item.content;
			}
			//обновление
			else if (item.method == 'reload') {
				window.location.reload();
			}
			//окно, тут заменить на свое окно если не бутстрап
			else if (item.method == 'modal') {
				$(item.selector).modal();
			}

			//выполнение любого скрипта
			else if (item.method == 'script') {
				$('body').append(item.content);
			}
			else {
				alert(item.method);
			}
		});
	}
	//todo код с ошибкой, сделать в всплывающем окне
	if (response.error_text) {
		alert(response.error_text);
	}
	else {
		if (response._error) alert(response._error);
	}
}

$(document).ready(function(){
	//валидация форм
	if ($.isFunction($.fn.validate)) {
		$('form.validate').each(function(){
			$(this).validate({
				//настройка стилей валидации
				//.form-group обертка для всех полей
				errorPlacement: function(error, element) {
					error.appendTo(element.closest(".form-group"));
				},
				highlight: function(element, errorClass, validClass) {
					//console.log('highlight');
					$(element).addClass(errorClass);
					$(element).closest('.form-group').addClass(errorClass);
				},
				unhighlight: function(element, errorClass, validClass) {
					//console.log('unhighlight');
					$(element).removeClass(errorClass);
					$(element).closest('.form-group').removeClass(errorClass);
				}
			})
		});
	}

	//очитска урл от путстых значений
	$('form.form_clear').submit(function(){
		$(this).find('select,input').each(function(){
			if($(this).val()=='' || $(this).val()=='0-0') $(this).removeAttr('name');
		});
	});

	//отправка формы аджаксом - v1.2.22
	$(document).on('submit','form.ajax',function(){
		var form = $(this),
			formData = new FormData($(this).get(0)),
			//путь куда отправляется форма
			action = $(this).data('action'),
			//окно которое открывается при успехе
			//window_success = $(this).data('window_success'),
			//блок куда показывать ошибки сгенерированные на пхп
			//message_box = $('.message_box',this),
			valid = true;
		//обработку делаем только если указан data-action, иначе форма ведет себя как обычная
		if (action) {
			//валидация
			if ($(this).hasClass('validate')) {
				//jquery_validate.js
				if ($.isFunction($.fn.validate)) {
					valid = false;
					if ($(this).valid()) {
						valid = true;
					}
				}
			}
			//делаем отправку только если форма валидная
			if (valid) {
				$.ajax({
					url: action,
					type: $(form).attr('method'),
					dataType: 'json',
					contentType: false,
					processData: false,
					data: formData,
					cache: false,
					beforeSend: function(){
						$(form).find('button[type=submit]').addClass('disabled');
					},
					success: function(json, textStatus){
						//очистка формы
						if (json.success) {
							$('input:not([type=submit]):not([name=captcha]):not([type=hidden]),textarea,select',form).prop('value', null);
						}
						api_response(json);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert(errorThrown);
					},
					complete: function(XMLHttpRequest, textStatus){
						setTimeout(function(){
							$(form).find('button[type=submit]').removeClass('disabled');
						},1000);
					}
				});
			}
			return false;
		}
	});

	//аналоги form.ajax не для форм (ссылки, кнопки и т.д.)
	$(document).on('click','[data-api*="/"]',function(e){
		e.preventDefault();
		var $data = $(this).data(),
			$url = $data.api,
			$btn = $(this);
		//если уже нажали и небыло ответа аджакса то ничего не делать
		if (!$btn.hasClass('js_wait')) {
			$btn.addClass('js_wait');
			//удаляем датаатрибут чтобы не дублировать в запросе
			delete $data.api;
			$.ajax({
				method: "GET",
				type: 'JSON',
				url: $url,
				data: $data,
				cache: false
			}).done(function ($response) {
				api_response($response);
			}).fail(function () {
				alert("error");
			}).always(function () {
				$btn.removeClass('js_wait');
			});
			//возвращаем назад дата атрибут
			$data.api = $url;
		}
		//return false;
	});

	//мультичексбокс
	$(document).on("change",'.form_multi_checkbox .data input',function(){
		var arr = [];
		var i = 0;
		$(this).parents('.data').find('input:checked').each(function(){
			arr[i] = $(this).val();
			i++;
		});
		$(this).parents('.data').next('input').val(arr);
	});
	//min-max
	$(document).on("change",'.form_input2 input',function(){
		var min = parseInt($(this).parents('.form_input2').find('input.form_input2_1').val());
		var max = parseInt($(this).parents('.form_input2').find('input.form_input2_2').val());
		$(this).parents('.form_input2').find('input[type=hidden]').val(min+'-'+max);
	});


	//v1.2.64 пагинатор на ajax
	$(document).on("click",'.pagination_ajax .pagination a',function(){
		var url = $(this).attr('href'),
			box = $(this).closest('.pagination_ajax');
		$.post(url,{
			action:		'ajax',
			},function (data) {
				$(box).replaceWith(data);
				//добавляем в историю браузера
				//window.history.pushState(null, null, url);
			}
		);
		return false;
	});
	//автоматическая догрузка страниц для ajax_more
	if ($('.pagination_ajax').length) {
		var pagination_ajax_url = ''; //v1.2.90
		$('.pagination a', '.pagination_ajax').trigger('click');
		$(window).scroll(function () {
			//console.log('scroll');
			if ($('.pagination_ajax').length) {
				var box = $('.pagination_ajax'),
					document_top = $(document).scrollTop(),
					window_height = $(window).height(),
					box_top = $(box).offset().top,
					box_height = $(box).height(),
					current_url = $('.pagination a', box).attr('href');
				if (document_top + window_height > box_top && document_top - box_top < box_height) {
					if (current_url != pagination_ajax_url) {//v1.2.90
						$('.pagination a', box).trigger('click');
						pagination_ajax_url = current_url; //v1.2.90
					}
				}
			}
		});
	}
	/**/

});