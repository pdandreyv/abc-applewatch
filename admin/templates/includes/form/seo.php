<div class="seo-optimization"><a href="#"><?=a18n('seo_optimization')?></a></div>
<div style="display:none">
	<?php
	echo form('checkbox td3','seo',isset($q['value']['seo']) ? $q['value']['seo'] : '',array('name'=>a18n('seo_generate')));
	foreach (explode(' ',$q['key']) as $k) {
		switch ($k) {
			case 'url':			echo form('input td9','url',isset($q['value']['url']) ? $q['value']['url'] : '',array('name'=>a18n('url'))); break;
			case 'title':		echo form('input td12','title',isset($q['value']['title']) ? $q['value']['title'] : '',array('name'=>a18n('title'))); break;
			case 'description':	echo form('input td12','description',isset($q['value']['description']) ? $q['value']['description'] : '',array('name'=>a18n('description'))); break;
			case 'noindex':		echo form('checkbox td12 line','noindex',isset($q['value']['noindex']) ? $q['value']['noindex'] : '',array('name'=>a18n('noindex'))); break;
		}
	}?>
</div>
