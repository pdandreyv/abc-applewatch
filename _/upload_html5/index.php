<?php
/**
 * загрузка файлов через html5
 */
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>html5 загрузка файлов</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="jquery.uploader.js"></script>
	<script src="script.js"></script>
	<style>
		.files .data .img {display:block; background:#333; width:100px; height:100px}
		.files ul {display:block; list-style:none; padding:0px; margin: 0px;}
		.files ul li {display:block; list-style:none; width:100px; height:100px; padding:0px; margin: 0px;}
		.files ul li .img {display:block; background:#333; width:100px; height:100px}
		.files ul li .img img {max-width:100px; max-height:100px}
	</style>
</head>

<body>


<div class="files file_multi" data-i="uploads">
	<div class="data">
		<div class="img" title="Для загрузки картинки переместите её в эту область"></div>
		<a class="add_file button green" title="Выбрать файлы">
			<span><span class="sprite plus"></span>выбрать</span>
			<input type="file" multiple="multiple" title="выбрать файл" />
		</a>
		<div class="clear"></div>
	</div>
	<ul class="sortable">
	</ul>
</div>

</body>
</html>