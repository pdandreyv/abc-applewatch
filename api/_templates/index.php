<?php

//not_visible
// ожидается переменная $api_list
?>
<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>API каталога</title>
	<link rel="stylesheet" href="/templates/assets/css/api.style.css">
</head>
<body>
	<div class="api-container">
		<header class="api-header">
			<h1>API каталога</h1>
			<p>Список доступных эндпоинтов. Кликните, чтобы открыть ответ.</p>
		</header>
		<section class="api-list">
			<?php foreach ($api_list as $e): 
				$method = isset($e['method']) ? strtoupper($e['method']) : 'GET/POST';
				$methodClass = 'method-generic';
				if ($method==='GET') $methodClass='method-get';
				elseif ($method==='POST') $methodClass='method-post';
				elseif ($method==='PUT') $methodClass='method-put';
				elseif ($method==='DELETE') $methodClass='method-delete';
			?>
				<a class="api-row <?=$methodClass?>" href="<?=htmlspecialchars($e['url'])?>">
					<span class="api-badge"><?=htmlspecialchars($method)?></span>
					<code class="api-path"><?=htmlspecialchars($e['url'])?></code>
					<span class="api-right">
						<span class="api-desc"><?=htmlspecialchars($e['description'])?></span>
						<?php if (!empty($e['params'])): ?>
							<span class="api-params">
								<?php foreach ($e['params'] as $i => $p): ?>
									<?= $i>0 ? ', ' : '' ?><code><?=htmlspecialchars($p)?></code>
								<?php endforeach; ?>
							</span>
						<?php endif; ?>
					</span>
					<?php if (!empty($e['examples'])): ?>
						<div class="api-examples">
							<?php foreach ($e['examples'] as $ex): ?>
								<div>пример: <code><?=htmlspecialchars($ex)?></code></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if (!empty($e['example_response'])): ?>
						<pre class="api-response">ответ: <code><?=htmlspecialchars($e['example_response'])?></code></pre>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</section>
	</div>
</body>
</html>
