<template id="hypertext_html">
	<?=html_render('form/hypertext_html',array(
		'type'=>'html',
		'n'=>'{n}',
		'key'=>$q['key'],
		'content'=>''
	))?>
</template>

<template id="hypertext_images">
	<?=html_render('form/hypertext_images',array(
		'type'=>'images',
		'n'=>'{n}',
		'name'=>'',
		'style'=>'',
		'key'=>$q['key'],
		'images'=>array()
	))?>
</template>

<template id="hypertext_img">
	<?=html_render('form/hypertext_img',array(
		'type'=>'img',
		'n'=>'{n}',
		'key'=>$q['key'],
		'content'=>'',
		'video'=>'',
		'img'=>'',
		'path'=>'/admin/templates2/imgs/no_img.png',
	))?>
</template>

<template id="hypertext_video">
	<?=html_render('form/hypertext_video',array(
		'type'=>'video',
		'n'=>'{n}',
		'key'=>$q['key'],
		'content'=>''
	))?>
</template>