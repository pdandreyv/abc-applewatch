<?php
//todo - вообще не готово
$hypertext = $q['value'] ? unserialize($q['value']) : array();
if (!$hypertext) {
	$hypertext[1] = array(
		'type' => 'html',
		'text' => ''
	);
}
?>
<div class="field <?=$q['class']?>">
	<label<?=$q['title']?' title="'.$q['title'].'"':''?>>
		<span><?=$q['name']?></span>
		<?php if ($q['help']) {?>
		<a href="#" class="sprite question" title="<?=$q['help']?>"></a>
		<?php } ?>
	</label>
	<div>
		<ul class="hypertext_blocks">
			<?php
			foreach ($hypertext as $k=>$v) {
				if ($v['type']=='html') {
					$rand = rand(100000,999999);
					?>
				<li>
					<a href="#" class="sprite delete" title="удалить"></a>
					<textarea name="<?=$q['key']?>[<?=$k?>][text]"><?=$v['text']?></textarea>
					<div id="<?=$q['key']?>[<?=$k?>][text]" class="hypertext_html"><?=$v['text']?></div>
					<input type="hidden" name="<?=$q['key']?>[<?=$k?>][type]" value="html"/>
				</li>
					<?php
				}
			}
			?>

		</ul>
		<ul class="hypertext_actions">
			<li><a href="#">+ текст</a></li>
			<li><a href="#">+ фото</a></li>
			<li><a href="#">+ видео</a></li>
		</ul>
	</div>
	<div class="clear"></div>
</div>