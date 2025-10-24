<?php
$id = rand(1,9999);
?>
<div class="filter form-group col-xl-2">
	<input id="datepicker_<?=$id?>" name="<?=$q['key']?>" placeholder="<?=$q['key']=='date_from'?'от':'до'?>" type="text" class="form-control" value="<?=htmlspecialchars(stripslashes_smart(isset($_GET[$q['key']]) ? $_GET[$q['key']] : ''))?>">
</div>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	$('#datepicker_<?=$id?>').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		showDropdowns: true,
		locale: {
			format: 'YYYY-MM-DD',
			"applyLabel": "Принять",
			"cancelLabel": "Отклонить",
			"fromLabel": "От",
			"toLabel": "До",
			"customRangeLabel": "Custom",
			"daysOfWeek": [
				"Вс",
				"Пн",
				"Вт",
				"Ср",
				"Чт",
				"Пт",
				"Сб"
			],
			"monthNames": [
				"Январь",
				"Февраль",
				"Март",
				"Апрель",
				"Май",
				"Июнь",
				"Июль",
				"Август",
				"Сентябрь",
				"Октябрь",
				"Ноябрь",
				"Декабрь"
			]
			, "firstDay": 1
		}
	});
	$('#datepicker_<?=$id?>').on('apply.daterangepicker', function(ev, picker) {
		var date = picker.startDate.format('YYYY-MM-DD');
		var url = '/admin.php?<?=$q['url']?>&<?=$q['key']?>=',
			search = date;
		search = encodeURIComponent(search);
		$(this).val(date);
		top.location = url+search;
	});
	$('#datepicker_<?=$id?>').on('cancel.daterangepicker', function(ev, picker) {
		var url = '/admin.php?<?=$q['url']?>',
		search = encodeURIComponent(search);
		top.location = url;
	});
});
</script>