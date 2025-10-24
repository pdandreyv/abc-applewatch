<?php
if ($i==1) echo '<ul id="menu2">';
$title = htmlspecialchars($q['name']);
$url = get_url('page',$q);
$class = @$u[1]==$q['url'] ? ' class="active"' : '';
echo '<li'.$class.'>';
if ($url==$_SERVER['REQUEST_URI']) {
	echo '<span class="a">' . $q['name'] . '</span>';
}
else {
	echo '<a href="' . $url . '" title="' . $title . '">' . $q['name'] . '</a>';
}
echo '</li>';
if ($i==$num_rows) echo '</ul>';
?>