ABC - построенная на функциях, простая для понимания и масштабирования система управления сайтом.
<br><br>
Основные принципы CMS
<ul>
	<li>простота кода</li>
	<li>низкий порог для понимания</li>
	<li>универсальность и масштабируемость</li>
	<li>скорость работы</li>
</ul>

<style>
	/*спрайты*/
	.sprite {display:inline-block; font-size:0px; background-image: url('/admin/templates/imgs/sprite.png?1542795656'); }
	.sprite.pdf {width:100px; height:100px; background-position:-0px -1px}
	.sprite.attention {width:38px; height:38px; background-position:-101px -1px}
	.sprite.photo {width:32px; height:32px; background-position:-140px -1px}
	.sprite.loupe {width:27px; height:27px; background-position:-173px -1px}
	.sprite.display_0 {width:20px; height:29px; background-position:-173px -29px}
	.sprite.display_1 {width:20px; height:29px; background-position:-101px -59px}
	.sprite.img {width:26px; height:22px; background-position:-122px -59px}
	.sprite.yandex_index_0 {width:14px; height:30px; background-position:-149px -59px}
	.sprite.yandex_index_1 {width:14px; height:30px; background-position:-164px -59px}
	.sprite.close:hover {width:24px; height:24px; background-position:-179px -59px}
	.sprite.close {width:24px; height:24px; background-position:-179px -84px}
	.sprite.boolean_0 {width:22px; height:22px; background-position:-0px -109px}
	.sprite.boolean_1 {width:22px; height:22px; background-position:-23px -109px}
	.sprite.delete {width:22px; height:22px; background-position:-46px -109px}
	.sprite.edit {width:22px; height:22px; background-position:-69px -109px}
	.sprite.market_0 {width:22px; height:22px; background-position:-92px -109px}
	.sprite.market_1 {width:22px; height:22px; background-position:-115px -109px}
	.sprite.noindex_0 {width:22px; height:22px; background-position:-138px -109px}
	.sprite.noindex_1 {width:22px; height:22px; background-position:-161px -109px}
	.sprite.plus2 {width:22px; height:22px; background-position:-184px -109px}
	.sprite.sorting {width:22px; height:22px; background-position:-0px -132px}
	.sprite.tree {width:22px; height:22px; background-position:-23px -132px}
	.sprite.view {width:22px; height:22px; background-position:-46px -132px}
	.sprite.home {width:20px; height:20px; background-position:-69px -132px}
	.sprite.question {width:18px; height:18px; background-position:-90px -132px}
	.sprite.level {width:17px; height:17px; background-position:-109px -132px}
	.sprite.settings {width:17px; height:17px; background-position:-127px -132px}
	.sprite.settings2 {width:17px; height:17px; background-position:-145px -132px}
	.s-size .sprite.img {width:18px; height:14px; background-position:-163px -132px}
	.s-size .sprite.boolean_0 {width:16px; height:16px; background-position:-182px -132px}
	.s-size .sprite.boolean_1 {width:16px; height:16px; background-position:-163px -149px}
	.s-size .sprite.delete {width:16px; height:16px; background-position:-180px -149px}
	.s-size .sprite.edit {width:16px; height:16px; background-position:-0px -166px}
	.s-size .sprite.noindex_0 {width:16px; height:16px; background-position:-17px -166px}
	.s-size .sprite.noindex_1 {width:16px; height:16px; background-position:-34px -166px}
	.s-size .sprite.plus2 {width:16px; height:16px; background-position:-51px -166px}
	.s-size .sprite.sorting {width:16px; height:16px; background-position:-68px -166px}
	.s-size .sprite.tree {width:16px; height:16px; background-position:-85px -166px}
	.s-size .sprite.view {width:16px; height:16px; background-position:-102px -166px}
	.sprite.plus {width:16px; height:16px; background-position:-119px -166px}
	.s-size .sprite.display_0 {width:12px; height:17px; background-position:-136px -166px}
	.s-size .sprite.display_1 {width:12px; height:17px; background-position:-149px -166px}
	.sprite.search {width:15px; height:15px; background-position:-162px -166px}
	.s-size .sprite.level {width:14px; height:14px; background-position:-178px -166px}
	.sprite.x {width:14px; height:14px; background-position:-178px -181px}
	.sprite.asc {width:7px; height:4px; background-position:-193px -181px}
	.sprite.desc {width:7px; height:4px; background-position:-193px -186px}
	/*уточнения*/
	.sprite.attention {float:left; margin:0 8px 0 0}
	.sprite.search {position: absolute;}
</style>

<h3>ИКОНКИ</h3>
<span class="sprite display_1"></span>
<span class="sprite display_0"></span> - влияет на видимость объекта на сайте
<br>
<span class="sprite boolean_1"></span>
<span class="sprite boolean_0"></span> - да/нет, вкл/выкл
<br>
<span class="sprite edit"></span> - открыть форму редактирования
<br>
<span class="sprite plus2"></span> - создать новую запись
<br>
<span class="sprite level"></span> - используется для деревовидных разделов (например, категории магазина).
Если зажать и держать, то можно перетащить в другое место и поменять вложенность элемента
<br>
<span class="sprite delete"></span> - удаление материала с сайта

<br><br>
<h3>ОПИСАНИЕ РАЗДЕЛОВ</h3>
<h4>ДЕРЕВО САЙТА</h4>
Ссылка: <a target="_blank" href="/admin.php?m=pages">/admin.php?m=pages</a>
<br>В данном разделе идет настройка меню и текстовых страниц сайта.
<br>Страница может быть обычной текстовой старницей а так же отдельным модулем (новости, каталог товаров, корзина),
в котором могут содержаться и другие записи (например, модуль новости содержит в себе новостные записи,
которые создаются в отдельном разделе админпанели).
<br>Для редактирования текстовых старниц присуствует визуальных html-редактор, с помощью которого можно форматировать текст
аналогично как в WORD.
<br>Ниже сылки на описание, как работать с данным редактором
<br><a target="_blank" href="http://pro-wordpress.ru/novichkam/kak-rabotat-v-vizualnom-redaktore-tinymce.php">http://pro-wordpress.ru/novichkam/kak-rabotat-v-vizualnom-redaktore-tinymce.php</a>
<br><a target="_blank" href="https://wiki.insales.ru/wiki/Как_работать_в_HTML-редакторе_TinyMCE">https://wiki.insales.ru/wiki/Как_работать_в_HTML-редакторе_TinyMCE</a>
<br>Описание может немного отличаться от сборки, которая используется в данной cms

<h4>СЛОВАРЬ</h4>
Ссылка: <a target="_blank" href="/admin.php?m=languages">/admin.php?m=languages</a>
<br>В данном разделе указываются все слова, которые не относятся к табличным данным (страницы, статьи, новости, товары, пользователи и т.д.)
<br>Например - телефоны и контактные данные в шапке и в подвале сайта, название различных кнопок и полей в формах,
meta данные для верификации сайта в различных сервисах (yandex/google webmaster), коды счетчиков и всемозможных плагинов.

<h4>РЕЗЕРВНОЕ КОПИРОВАНИЕ И ВОССТАНОВЛЕНИЕ</h4>
Ссылка: <a target="_blank" href="/admin.php?m=backup">/admin.php?m=dumper</a>
<br>В данном разделах можно сделать резервную копию базы данных и при необходимости восстановить.
<br>В базе данных содержится только табличная информация (страницы, статьи, новости, товары, пользователи и т.д.)
<br>Картинки и словарь хранится в виде файлов на сервере.

<h4>НАСТРОЙКИ</h4>
Ссылка: <a target="_blank" href="/admin.php?m=config">/admin.php?m=config</a>
<br>В данном разделе указываются основные настройки для сайта.
В часности тут можно указать электронный адрес, с которого будут отправляться письма пользователям,
а так же электронный адрес администратора сайта, на который должны приходить уведомления с сайта.
