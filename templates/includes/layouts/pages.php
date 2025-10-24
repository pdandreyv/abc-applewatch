<?=$abc['children']?html_render('pages/children', $abc['children']):''?>
<div <?=editable('pages|text|'.$abc['page']['id'])?>><?=$abc['page']['text']?></div>
