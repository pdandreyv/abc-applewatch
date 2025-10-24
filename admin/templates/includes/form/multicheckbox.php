<div class="field <?=$q['class']?>">
	<label class="all">отметить все<input type="checkbox" onchange="$(this).closest('.multicheckbox').find('input').prop('checked',this.checked)" /></label>
	<label<?=$q['title']?' title="'.$q['title'].'"':''?>>
		<span><?=$q['name']?></span>
		<?php if ($q['help']) {?>
			<a href="#" class="sprite question" title="<?=$q['help']?>"></a>
		<?php } ?>
	</label>
	<?php
	if ($q['data']) {
		$level = -1;
		$curr = current($q['data']);
		$slevel = isset($curr['level']) ? $curr['level'] : 0;
		foreach ($q['data'] as $k=>$v) {
			if (!isset($v['level'])) $v['level'] = 0;
			if ($level>=$v['level'] ) {
				echo '</li>';
			}
			//v1.1.14 - закрываем все предыдущие li
			if ($level>$v['level']) {
				for ($i=$v['level']; $i<$level; $i++) {
					echo '</li></ul>';
				}
			}
			if ($level<$v['level']) echo '<ul class="l'.$v['level'].'">';
				$checked = in_array($v['id'],$q['value']) ? 'checked="checked"' : '';
				$class2 = in_array($v['id'],$q['value']) ? ' class="checked"' : '';
				echo '<li><label'.$class2.'>'.($v['id'] ? '<input name="'.$q['key'].'[]" type="checkbox" value="'.$v['id'].'"'.$checked.' />' : '').'<span>'.mb_substr($v['name'],0,112,"UTF-8").'</span></label>';
					$level = $v['level'];
		}
		for ($i=$slevel; $i<=$v['level']; $i++) echo '</li></ul>';
	}
	else echo '<ul class="l0"></ul>';
	?>
</div>