<?php
$top=$bottom='';
$parent = $child = 0;
//$modules2 = array_merge_recursive(array('<span class="sprite home"></span>'=>'index'),$q);
$modules2 = $q;
$m = @$_GET['m'];
foreach ($modules2 as $key => $value) {
	$value['name'] = isset($value['name']) ? $value['name'] : $value['module'];
	if (is_array($value['module'])) {
		$i=0;
		$bottom2 = '';
		$first_url = '';
		$parent_active = '';
		foreach ($value['module'] as $k=>$v) {
			if (access('admin module',$v['module'])) {
				$v['name'] = isset($v['name']) ? $v['name'] : $v['module'];
				$parent++;
				$child++;
				$i++;
				$link = $m==$v['module'] ? ' class="a"' : '';
				$bottom2.='<a href="/admin.php?m='.$v['module'].'"'.$link.'>'.a18n($v['name']).'</a>';
				//первый урл для урл родителя
				if ($i==1) {
					$first_url = $v['module'];
				}
				//активный подраздел
				if ($m==$v['module']) {
					$parent_active = 1;
				}
			}

		}
		//если есть активный подраздел
		if ($parent_active) {
			$top.='<a href="/admin.php?m='.$first_url.'" class="a">'.a18n($value['name']).'</a>';
			$bottom = $bottom2;
		}
		//если есть хотябы один подраздел
		elseif ($first_url) {
			$top.='<a href="/admin.php?m='.$first_url.'" >'.a18n($value['name']).'</a>';
		}
	}
	elseif (access('admin module',$value['module'])) {
		$parent++;
		$link = $m==$value ? ' class="a"' : '';
		if ($value['module']=='index') {
			$top.='<a href="/admin.php?m='.$value['module'].'"'.$link.'><span class="sprite home"></span></a>';
		}
		else {
			$top.='<a href="/admin.php?m='.$value['module'].'"'.$link.'>'.a18n($value['name']).'</a>';
		}
	}
}
if ($parent>1) {
	?>
	<div class="menu_parent gradient"><?= $top ?></div>
	<?php
	if ($bottom AND $child > 1) {
		?>
		<div class="menu_child corner_bottom"><?=$bottom?>
			<div class="clear"></div>
		</div>
		<?php
	}
}
