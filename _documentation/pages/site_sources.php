Скрипты, библиотеки и стили подключаем функцией html_sources()<br>
<br>
<strong>Список ключей всех скриптов:</strong>
<ul>
	<li>css_reset - очистка всех стилей</li>
	<li>css_common - основные стили сайта</li>
	<li>script_common - основные скрипты сайта</li>
	<li>jquery - библиотека jquery</li>
	<li>jquery_cookie - библиотека jquery_cookie</li>
	<li>jquery_ui - библиотека jqueryUI</li>
	<li>query_ui_style - стили jqueryUI</li>
	<li>jquery_localization - локализация (для datepicker)</li>
	<li>jquery_form - отправка форм аджаксом</li>
	<li>jquery_uploader - загрузка файлов нтмл5</li>
	<li>jquery_validate - валидация форм</li>
	<li>jquery_multidatespicker - мультидатапикер</li>
	<li>highslide - просмотр изображений</li>
	<li>highslide_gallery - просмотр изображений (галерея)</li>
	<li>tinymce - текстовый редактор</li>
	<li>editable - быстрое редактирование</li>
</ul>

<strong>Примеры использования</strong>
<ol>
	<li>прямой вывод - используем return
<pre>&lt;?=html_sources('return','css_reset css_common jquery')?></pre>
	</li>
	<li>собираем скрипты в группу footer без вывода на экран
<pre>&lt;?=html_sources('footer','css_reset css_common jquery')?></pre>
	</li>
	<li>выводим группу на экран - вызываем просто функцию с одним параметром footer (название группы, может быть любое)
<pre>&lt;?=html_sources('footer')?></pre>
	</li>
</ol>
таким образом мы можем подключать любые скрипты в шаблонах, не боятся что один скрипт будет подклчон два раза и выводить все сразу в шаблоне либо в подвале страницы