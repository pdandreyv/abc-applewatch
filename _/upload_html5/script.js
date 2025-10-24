$(document).ready(function(){
	var doc = $(this);
	//изменение состояния файлового инпута file
	doc.on('change','.files.file .add_file input',function(){
		var box = $(this).closest('.data').find('.img');
		//удаляем инпут с подгруженным файлом и заменяем на пустой чтобы картинка не отправлялась при отправке формы
		this.outerHTML = this.outerHTML;
		upload(box,this.files[0]);
	});
	//изменение состояния общего файлового инпута file_multi
	doc.on('change','.files.file_multi .add_file input',function(){
		upload_multi ($(this).closest('.files'),this.files);
	});
	//изменение состояния индивидуального файлового инпута file_multi
	doc.on('change','.files.file_multi li input[type=file]',function(){
		var box = $(this).closest('li').find('.img');
		//удаляем инпут с подгруженным файлом и заменяем на пустой чтобы картинка не отправлялась при отправке формы
		this.outerHTML = this.outerHTML;
		upload(box,this.files[0]);
	});
	//загрузка перемещением file
	doc.delegate('.files.file .img, .files.file_multi .img',{
		dragenter: function() {
			//$(this).addClass('highlighted');
			return false;
		},
		dragover: function() {
			return false;
		},
		dragleave: function() {
			//$(this).removeClass('highlighted');
			return false;
		},
		drop: function(e) {
			var img = $(this),
				box = img.closest('.files'),
				dt = e.originalEvent.dataTransfer;
			if (box.hasClass('file')) {
				upload(img,dt.files[0]);
			} else {
				upload_multi(box,dt.files);
			}
			return false;
		}
	});
});
//загрузка картинки
function upload(uploadItem,file) {
	if (file) {
		var img = $('img',uploadItem).prop({src:''}),
			reader = new FileReader(),
		//отключаем возможность отправки формы до загрузки всех изображений
			form = uploadItem.addClass('loading').trigger('form.disable');
		$('.progress',uploadItem).remove();
		var bar = $('<div class="progress" rel="0">загрузка</div>').appendTo(uploadItem);
		$(reader).load(function(e){
			var path = '';
			// Отсеиваем не картинки
			if (!file.type.match(/image.*/)) {
				if (path=='' && file.type.match(/text.*/))	path = 'icons/doc.png';
				if (path=='' && file.type.match(/.*word/))	path = 'icons/doc.png';
				if (path=='' && file.type.match(/.*excel/))	path = 'icons/xls.png';
				if (path=='' && file.type.match(/.*pdf/))	path = 'icons/pdf.png';
				if (path=='' && file.name.match(/.*zip/))	path = 'icons/zip.png';
				if (path=='' && file.name.match(/.*rar/))	path = 'icons/zip.png';
				if (path=='') path = 'icons/blank.png';
			}
			else path = e.target.result;
			//alert(file.type+' '+path);
			img.prop({src:path});
			uploadItem.prop({file: file});
			new uploaderObject({
				file:		file,
				url:		'uploader.php',
				fieldName:	'temp',
				onprogress: function(percents) {
					var value = bar.width() * (percents/100 - 1);
					bar.attr('rel', percents).text(percents+'%').css('background-position', value+'px center');
				},
				oncomplete: function(done, data) {
					if (done && data) {
						$('input[type="hidden"]',uploadItem).val(data);
						bar.text('загружено');
					}
					else {
						alert(this.lastError.text);
					}
					//включаем возможность отправки формы
					uploadItem.removeClass('loading').trigger('form.enable');
				}
			});
		});
		reader.readAsDataURL(file);
	}
}
function upload_multi (box,files) {
	var n = 0,
		ul = $('ul',box),
		key = box.data('i');
	$('li',ul).each(function(){
		var i = $(this).data('i');
		if (i > n) n = i;
	});
	$.each(files, function(i, file) {
		n++;
		var name = file.name.split('.',file.name.split('.').length-1),
			li = $('<li/>').data('i',n).attr('title','для изменения последовательности картинок переместите блок в нужное место').appendTo(ul),
			img = '<div class="img"><span>&nbsp;</span><img src="" /><input type="hidden" name="'+key+'['+n+'][temp]" /></div>';
		img+='<a href="#" class="sprite delete" title="удалить"></a>';
		img+='<div>'+file.name+'</div><input class="input" name="'+key+'['+n+'][name]" value="'+name+'"/><br/><label><input name="'+key+'['+n+'][display]" type="checkbox" checked="checked" value="1" /><span>показывать</span></label>';
		$(img).appendTo(li);
		upload(li.find('.img'),file);
	});
	//$('.sortable',box).sortable();
	$('.data input',box).replaceWith('<input type="file" multiple="multiple" title="выбрать файл" />');
}
