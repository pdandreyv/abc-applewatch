<?php
// Пара "код" => "перевод"
$pairs = array();
if (isset($q['value']) && $q['value']) {
    if (is_array($q['value'])) {
        $pairs = $q['value'];
    } else {
        $decoded = json_decode($q['value'], true);
        if (is_array($decoded)) $pairs = $decoded;
    }
}
?>
<div class="card mt-3">
	<div class="card-header d-flex justify-content-between align-items-center">
		<div><b>Словарь</b></div>
		<button type="button" class="btn btn-sm btn-outline-primary js-add-dict">добавить фразу</button>
	</div>
	<div class="card-body">
		<div class="dictionary-list">
			<?php if ($pairs) foreach ($pairs as $code => $val) { ?>
				<div class="form-row align-items-center mb-2 dictionary-item">
					<div class="col-md-4"><input type="text" class="form-control dict-code" placeholder="Код" value="<?=htmlspecialchars($code)?>"></div>
					<div class="col-md-6"><input type="text" class="form-control dict-val" placeholder="Перевод" value="<?=htmlspecialchars($val)?>"></div>
					<div class="col-md-2 text-right">
						<button type="button" class="btn btn-sm btn-outline-danger js-del-dict" title="удалить">Удалить</button>
					</div>
				</div>
			<?php } ?>
		</div>
		<input type="hidden" name="dictionary" class="js-dict-json" value='<?=htmlspecialchars(json_encode($pairs, JSON_UNESCAPED_UNICODE))?>'>
		<small class="text-muted">Сохраняется как JSON: { "код": "перевод" }</small>
	</div>
</div>

<script>
(function(){
	var script = document.currentScript;
	var card = script.closest('.card') || document;
	var list = card.querySelector('.dictionary-list');
	var hidden = card.querySelector('.js-dict-json');
	if (!list || !hidden) return;
	function syncHidden(){
		var data = {};
		list.querySelectorAll('.dictionary-item').forEach(function(row){
			var codeEl = row.querySelector('.dict-code');
			var valEl  = row.querySelector('.dict-val');
			if (!codeEl || !valEl) return;
			var code = codeEl.value.trim();
			var val  = valEl.value;
			if (code !== '') data[code] = val;
		});
		hidden.value = JSON.stringify(data);
	}
	card.addEventListener('input', function(e){
		if (e.target.classList && (e.target.classList.contains('dict-code') || e.target.classList.contains('dict-val'))) syncHidden();
	});
	var addBtn = card.querySelector('.js-add-dict');
	if (addBtn) addBtn.addEventListener('click', function(){
		var row = document.createElement('div');
		row.className = 'form-row align-items-center mb-2 dictionary-item';
		row.innerHTML = '<div class="col-md-4"><input type="text" class="form-control dict-code" placeholder="Код"></div>'+
			'<div class="col-md-6"><input type="text" class="form-control dict-val" placeholder="Перевод"></div>'+
			'<div class="col-md-2 text-right"><button type="button" class="btn btn-sm btn-outline-danger js-del-dict" title="удалить">Удалить</button></div>';
		list.appendChild(row);
		syncHidden();
	});
	card.addEventListener('click', function(e){
		var del = e.target && e.target.closest ? e.target.closest('.js-del-dict') : null;
		if (del) {
			var row = del.closest('.dictionary-item');
			if (row) row.remove();
			syncHidden();
		}
	});
})();
</script>


