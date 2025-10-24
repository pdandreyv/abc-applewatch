<?php
session_start();
//print_r($_SESSION['urls']); die();
$config['http'] = 'http';
$config['domain'] = $_SERVER['HTTP_HOST'];
$config['http_domain'] = $config['http'].'://'.$config['domain'];
//скаинрование
if (@$_GET['url']) {
	$url = urldecode($_GET['url']);
	//echo $url;
	//заголовок
	$headers = get_headers($config['http_domain'].$url,1);
	$data['status'] = substr($headers[0],9,3);
	if (is_array($headers['Content-Type'])) $type = explode(';',$headers['Content-Type'][0]);
	else $type = explode(';',$headers['Content-Type']);
	$data['type'] = $type[0];
	$data['urls'] = '';
	$_SESSION['urls'][$url]['status'] = $data['status'];
	$_SESSION['urls'][$url]['type'] = $data['type'];
	//если нтмл страница
	if ($data['type']=='text/html') {
		//парсинг ссылок
		$content = @file_get_contents($config['http_domain'] . $url);
		//echo $content;
		//preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $content, $matches);
		//$urls = array_unique($matches[1]); // Берём то место, где сама ссылка (благодаря группирующим скобкам в регулярном выражении)
		$urls = get_links($content);
		$urls = array_merge($urls,get_images($content));
		$urls = array_unique($urls);
		//$urls = get_images($content);
		//print_r($urls);
		foreach ($urls as $k=>$u) {
			if (!isset($_SESSION['urls'][$u])) {
				if (substr($u, 0, 4) != 'http') {
					$_SESSION['urls'][$u] = array(
						'refer'=> $url
					);
					$data['urls'] .= '
				<tr>
					<td>' . $u . '<div>'.$url.'</div></td>
					<td><a href="' . urlencode($u) . '">проверить</a></td>
					<td></td>
				</tr>';
				}
			}
		}
	}
	//echo $data['urls'];
	echo json_encode($data);
	die();
}
if (!isset($_SESSION['urls']) OR @$_GET['clear']==1) {
	$urls = array();
	/*if ($sitemap = file_get_contents($config['http_domain']  .'/sitemap.xml')) {
		$xml = simplexml_load_string($sitemap);
		$json = json_encode($xml);
		$urls = json_decode($json, TRUE);
	}*/
	if ($urls) {
		foreach ($urls['url'] as $k=>$v) {
			$_SESSION['urls'][$v['loc']] = '';
		}
	}
	else {
		$_SESSION['urls'] = array('/'=>'');
	}
}
?>
<style>
	* {font:13px Arial}
	table {float:left; width:800px; border:1px solid #333; margin:0 20px}
	table td div {color:#999; font-size:9px}
	.s200 {background: rgba(0, 255, 73, 0.14)}
	.s301 {color:orange}
	.s302 {color:orange}
	.s404 {color:darkred}
</style>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		urls_start = 0;
		$(document).on("click",'#urls tbody a',function(){
			var td = $(this).closest('td'),
				url = $(this).attr('href');
			$.getJSON('/_/404.php',{
					url:	url,
				},function (data) {
					//alert(data.header);
					$(td).html(data.status);
					$(td).next().html(data.type);
					$(td).closest('tr').attr('class','s'+data.status);
					$('#urls tbody').append(data.urls)
					//console.log(data);
					if (urls_start) {
						$('#urls .start').trigger('click');
					}
				}
			);
			return false;
		});
		$(document).on("click",'#urls .start',function(){
			urls_start = 1;
			$(this).hide();
			$('#urls .stop').show();
			$('#urls tbody a').each(function(){
				$(this).trigger('click');
				return false;
			});
			return false;
		});
		$(document).on("click",'#urls .stop',function(){
			urls_start = 0;
			$(this).hide();
			$('#urls .start').show();
			return false;
		});
	})
</script>
<a href="?clear=1">ОЧИСТИТЬ ПАМЯТЬ</a>
<table id="urls">
	<thead>
	<tr>
		<td>URL</td>
		<td>STATUS
			<a href="#" class="start">старт</a>
			<a href="#" class="stop" style="display:none">стоп</a>
		</td>
		<td>TYPE</td>
	</tr>
	</thead>
	<tbody>
		<?php foreach ($_SESSION['urls'] as $k=>$v) {?>
		<tr class="s<?=@$v['status']?>">
			<td><?=$k?><div><?=@$v['refer']?></div></td>
			<?php if (@$v['status']) {?>
				<td><?=$v['status']?></td>
				<td><?=$v['type']?></td>
			<?php } else {?>
				<td><a href="<?=urlencode($k)?>">проверить</a></td>
				<td></td>
			<?php } ?>
		</tr>
		<?php } ?>
	<tbody>
</table>
<?php /*
<table>
	<thead>
	<tr>
		<td>IMG</td>
		<td>STATUS</td>
	</tr>
	</thead>
	<tbody>
		<tr>
		</tr>
	<tbody>
</table>
 */

function get_links ($content) {
	//$regexp = "/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/";
	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
	$urls = array();
	if (preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			if ($match[2][0]=='/') {
				$urls[] = $match[2];
			}
			// $match[3] = link text
		}
	}
	//return array();
	return $urls;
}
function get_images ($content) {
	$urls = array();
	$regexp = "/<img.*src=(\"??)([^\" >]*?)\\1[^>]*>/";
	$regexp = '/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|\"([^\"]*)\"|\'([^\']*)\'|([^<>\'\"\\s]*))[^<>]*>/i';
	//$regexp = "/<img\s+src\s*=\s*([\"'][^\"']+[\"']|[^>]+)>/";
	$regexp = '/<img[^>]+src="?\'?([^"\']+)"?\'?[^>]*>/i';
	if (preg_match_all($regexp, $content, $matches,PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			if ($match[1][0]=='/') {
				//echo $match[1];
				$urls[] = $match[1];
			}
		}
	}
	//print_R($urls);
	return $urls;
}
