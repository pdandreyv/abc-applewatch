<?php
$content = '';
$m = @$_GET['m'];
foreach ($q as $key => $value) {
	$value['name'] = isset($value['name']) ? $value['name'] : $value['module'];
	if (is_array($value['module'])) {
		$i=0;
		$bottom2 = '';
		$first_url = '';
		foreach ($value['module'] as $k=>$v) {
			if (access('admin module',$v['module'])) {
				$v['name'] = isset($v['name']) ? $v['name'] : $v['module'];
				$i++;
				$link = $m==$v['module'] ? ' class="a"' : '';
				$bottom2.='<li><a href="/admin.php?m='.$v['module'].'">&bull; '.a18n($v['name']).'</a></li>';
				//первый урл для урл родителя
				if ($i==1) {
					$first_url = $v['module'];
				}
			}

		}

		//если есть хотябы один подраздел
		if ($first_url) {
			$content.= '<li><a href="/admin.php?m='.$first_url.'"><b>'.a18n($value['name']).'</b></a>';
			$content.= '<div><span class="'.$value['image'].'"></span></div>';
			$content.= '<ul>';
			$content.= $bottom2;
			$content.= '</ul></li>';
		}
	}
	elseif (access('admin module',$value['module'])) {
		if ($value['module']=='index') {

		}
		else {
			$content.= '<li><a class="one" href="/admin.php?m='.$value['module'].'"><b>'.a18n($value['name']).'</b><span class="'.$value['image'].'"></span></a>';
			$content.= '</li>';
		}
	}
}
if ($content) {
	?>
	<ul class="modules">
		<?=$content?>
		<div class="clear"></div>
	</ul>
	<?php
}