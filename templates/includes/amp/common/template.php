<?php //dd($abc['page'],true);
$abc['page']['title']			= isset($abc['page']['title']) ? filter_var($abc['page']['title'], FILTER_SANITIZE_STRING) : filter_var($abc['page']['name'],FILTER_SANITIZE_STRING);
$abc['page']['description']	= isset($abc['page']['description']) ? filter_var($abc['page']['description'], FILTER_SANITIZE_STRING) : $abc['page']['title'];
?>
<!doctype html>
<html amp lang="<?=$lang['localization']?>">
<head>
	<meta charset="utf-8">
	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<title><?=$abc['page']['title']?></title>
	<link rel="canonical" href="<?=$abc['page']['canonical']?>">
	<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
	<?php /*
	<script type="application/ld+json">
      {
        "@context": "http://schema.org",
        "@type": "NewsArticle",
        "headline": "<?=$abc['page']['name']?>",
        "datePublished": "<?=date2($abc['page']['date'],'%y-%m-%d').'T'.date2($abc['page']['date'],'%H:%M:%S')?>Z",
        "image": [
          "logo.jpg"
        ]
      }
    </script>
 */?>

	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
	<script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>
	<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
	<script async custom-element="amp-accordion" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>

	<style amp-boilerplate>
		body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
	</style>
	<noscript>
		<style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style>
	</noscript>
	<style amp-custom>
		header {padding: 15px 15px;}
		header > div {position: relative;}

		amp-sidebar ul {
			list-style: none;
		}
		amp-sidebar li {
			cursor: pointer;
		}
		.hamburger_btn {
			position: absolute;
			top: 0;
			right: 0;
			display:block;
			background: transparent;
			border: 0px;
			box-shadow: none;
		}
		.hamburger{
			width: 30px;
			height: 15px;
			display:block;
			cursor: pointer;
			border: 5px solid #000;
			border-width: 5px 0;
			z-index: 1;
		}
		.hamburger:before {
			display: block;
			content: '';
			border-top: 5px solid #000;
			margin-top: 5px;
			height: 5px;
			width: 30px;
			cursor: pointer;
		}
		amp-sidebar {
			width: 50vw;
		}
		body > amp-sidebar {
			background: #464646;
		}

		body > amp-sidebar ul {padding: 0px 15px; list-style: none;}
		body > amp-sidebar ul a {color: #FFF; text-transform: uppercase; margin-bottom: 10px;
			display: inline-block; text-decoration: none;}
		body > amp-sidebar ul > ul {padding-left: 15px;}
		body > amp-sidebar ul .menu_categories a {text-transform: none;}

		.content {padding:20px}

		.pagination {margin:0 0 15px; list-style: none; padding:0px;}
		.pagination li {display:inline; margin:0px; padding: 0px}

	</style>
</head>
<body>

<?=html_array('common/header')?>

<?php
$module = '/includes/amp/modules/'.$html['module'].'.php';
//по умолчанию если нет шаблона
if (!file_exists(ROOT_DIR.$config['style'].$module)) {
$module = '/includes/amp/modules/default.php';
}
include(ROOT_DIR.$config['style'].$module);
?>
</body>
</html>