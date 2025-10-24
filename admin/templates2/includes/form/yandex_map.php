<?php
//v1.2.70
$rand = rand(100000,999999);
//v1.2.77
$post = $q['value'];
//v1.4.45 - правки в карты
$key = array(
	'address'=>'address',
	'lat'=>'lat',
	'lng'=>'lng'
);
if ($q['key']) {
	$key = array(
		'address'=>$q['key'].'[address]',
		'lat'=>$q['key'].'[lat]',
		'lng'=>$q['key'].'[lng]'
	);
}
?>
<div class="field <?=$q['class']?> form-row">
	<?=form('input td6 yandex_map_input', $key['address'], array(
		'name' => 'Поиск по карте
		<a href="#" class="yandex_map_input_search" ><i data-feather="search"></i></a>',
		'value' => @$post['address']
	))?>
	<?=form('input td3 lat',$key['lat'],array(
		'name'=>'широта',
		'value'=>@$post['lat']
	))?>
	<?=form('input td3 lng',$key['lng'],array(
		'name'=>'долгота',
		'value'=>@$post['lng']
	))?>
	<div
			class="yandex_map_box"
			id="<?=$rand?>"
			data-lat="<?=@$post['lat']?>"
			data-lng="<?=@$post['lng']?>"
			data-lat_default="<?=$config['map_lat']?>"
			data-lng_default="<?=$config['map_lng']?>"
	></div>
</div>

