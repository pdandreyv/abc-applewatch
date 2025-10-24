<div class="row">
	<div class="card" style="width: 100%;">
		<div class="card-body">
			<div class="table-responsive">
				<button class="btn btn-secondary" data-toggle="modal" data-target="#backup">Сделать BackUp</button>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<th>файл</th>
							<th>размер</th>
							<th>дата</th>
							<th>	</th>
						</tr>
					</thead>
					<tbody>
<?php foreach ($q['files'] as $k=>$v) { ?>
						<tr data-file="<?=$v['name']?>">
							<td><a class="btn btn-warning js-restore" href="#" data-toggle="modal" data-target="#restore">Восстановить</a></td>
							<td><a href="?m=dumper&download=<?=$v['name']?>"><?=$v['name']?></a></td>
							<td><?=$v['size']?>kb</td>
							<td><?=$v['date']?></td>
							<td><a class="delete2" href="?m=dumper&file=<?=$v['name']?>"  title="<?=a18n('delete')?>" data-toggle="tooltip"><i data-feather="x-circle"></i></a></td>
						</tr>
<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="backup">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">BackUP</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form action="" data-action="?m=dumper" method="post" style="min-height: 300px">
				<div class="modal-body">
					<input type="hidden" name="action" value="backup">
					<div class="form-row">
					<?=form('select td6','comp_method',array(
							'value'=>array(1,$q['comp_methods']),
							'name'=>'Метод сжатия'
					))?>
					<?=form('select td6','comp_level',array(
							'value'=>array(5,$q['comp_levels']),
							'name'=>'Степерь сжатия'
					))?>
					<?=form('multicheckbox td12 tr4','tables',array(
							'name'=>'Фильтр таблиц',
							'value'=>array('',$q['tables'])
					))?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
					<button type="submit" class="btn btn-primary">Сделать бекап базы</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="restore">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Восстановить</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form action="" data-action="?m=dumper" method="post" style="min-height: 300px">
				<div class="modal-body">
					<input type="hidden" name="action" value="restore">
					<input type="hidden" name="file" value="">
					<div class="form-row">
						<div class="" style="text-align: center; width: 100%">
							<div class="swal-icon swal-icon--warning">
								<span class="swal-icon--warning__body">
								  <span class="swal-icon--warning__dot"></span>
								</span>
							</div>
							<div>
								Вы собираетесь заменить базу на сайте
								<h2><?=$config['http_domain']?></h2>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
					<button type="submit" class="btn btn-primary">Восстановить базу</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		//удаление
		$(document).on('click',".delete2",function(){
			var path = $(this).attr('href'),
				tr = $(this).closest('tr');
			$(tr).addClass('is_open');
			swal({
				title: "Вы уверены?",
				text: "Архив будет удален безвозвратно!",
				icon: "warning",
				buttons: true,
				dangerMode: true,
			})
				.then((willDelete) => {
				if (willDelete) {

					$.post(path, {'action':'delete'},
						function (data) {
							if (data!=1) {
								swal("ERROR", data, "error");
							}
							else {
								$(tr).remove();
								/*swal("Poof! Item has been deleted!", {
								 icon: "success",
								 });*/
							}
						}
					);
				}
				else {
					$(tr).removeClass('is_open');
		}
		});
			return false;
		});

		//удаление
		$(document).on('click',".js-restore",function(){
			var file = $(this).closest('tr').data('file');
			$(this).closest('tr').addClass('is_open');
			$('#restore form input[name=file]').val(file);
			//console.log('restore'+file);
		});

		//восстановление
		$(document).on('submit',"#backup form,#restore form",function(){
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
				var request = new XMLHttpRequest();
				request.onreadystatechange = function() {
					if(request.responseText) {
						$(form).html(request.responseText);
					}
				};
				request.open('post', action);
				request.send(formData);

			}
			return false;
		});
	});
</script>
