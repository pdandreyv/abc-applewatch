Самые основные ручные настройки сайта находятся в файле <code>_config2.php</code>
<br>В файле <code>_config.php</code> находятся настройки генерируемые с админки.
<br>Описание структуры базы данных <a href="/?page=common_db">здесь</a>
<br>
<br><strong>Соединения с базой данных</strong>
<br>Для соединения с базой и настройки типа кодировки используется ряд переменных <code>$config['mysql_*]</code>
<br>
<br><strong>Способы оплаты (платежные агрегаторы)</strong>
<br>Все способы оплаты на сайте описаны в массиве <code>$config['payments']</code>
<br>Шаблоны оплат находятся в файле <code>/templates/includes/payments/</code>
<br>Обработчик оплат находится в корне сайта <code>/api/payments/</code>
<br>
<br><strong>Подключаемые js библиотеки и css файлы</strong>
<br>Массив всех жс скриптов и цсс файлов используемых на сайта описаны в переменной <code>$config['sources']</code>
<br>Для избежания кеширования браузером старых версий файлов можно в конце пути файла прибавить знак <code>?</code>,
на сайте после него добавится дата файла на сервере
<br>Для вывода на сайте используется функция <code>html_sources</code>
<br>Например, код ниже выведет коды подключения скриптов и цсс файлов
<pre>echо html_sources('return','bootstrap.css font.css common.css jquery.js bootstrap.js');</pre>
а тут ниже в первой строке массив собирается для вывода а во второй непосредтвенно выводится
<pre>echo html_sources('head','bootstrap.css font.css common.css jquery.js bootstrap.js');
......
echo html_sources('return','head');</pre>
</pre>
<br><strong>Локальные настройки</strong>
<br>Для установки разных настроек для локальной версии и боевого сайта используем условие
<pre>if ($_SERVER['REMOTE_ADDR']=='127.0.0.1' AND $_SERVER['SERVER_ADDR']=='127.0.0.1') {
	//локальные настройки
}
else {
	//боевой сайт
}</pre>

<br><strong>Вывод ошибок</strong>
<br>- на локальное версии рекомендуется использоватть конструкцию <code>error_reporting(E_ALL);</code>
<br>- на боевой версии все ошибки записывать в логи <code>set_error_handler('error_handler');</code>
