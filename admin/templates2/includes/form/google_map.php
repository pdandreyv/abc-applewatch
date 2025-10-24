<?php
//v1.2.73
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
	<?php
	//если с поиском
	if (@$q['autocomplete']) {
		echo form('select td6 google_map_search',$key['address'],array(
			'name'=>'Адрес',
			'value'=>array(@$post['address'],array(@$post['address']=>@$post['address']))
		));
	}
	else {
		echo form('input td6 google_map_input', $key['address'], array(
			'name' => 'Адрес
			<a href="#" class="google_map_input_search" ><i data-feather="search"></i></a>',
			'value' => @$post['address']
		));
	}
	?>
	<?=form('input td3 lat',$key['lat'],array(
		'name'=>'широта',
		'value'=>@$post['lat']
	))?>
	<?=form('input td3 lng',$key['lng'],array(
		'name'=>'долгота',
		'value'=>@$post['lng']
	))?>
	<div
		class="google_map_box"
		id="<?=$rand?>"
		data-lat="<?=@$post['lat']?>"
		data-lng="<?=@$post['lng']?>"
		data-lat_default="<?=$config['map_lat']?>"
		data-lng_default="<?=$config['map_lng']?>"
	></div>
</div>
