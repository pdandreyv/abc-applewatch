<?php

$count_max = 10; //максимальное количество страниц пагинатора для отображения
$n = $q['n']; //номер старницы пагинатора
if ($n==0) $n=1;
//$count - фактическое количество страниц пагинатора
//$q['limit'] - выводимое количество записей на одной странице
//$q['num_rows'] - количество записей итого
$list = array(); //массив страниц пагинатора

//пагинатор показываем только если есть больше 1 страницы
if ($q['limit']<$q['num_rows']) {
	//фактическое количество страниц пагинатора
	$count = ceil($q['num_rows']/$q['limit']);
	//если фактическое количество страниц меньше максимального то показываем все
	if ($count <= $count_max) {
		for ($i = 1; $i <= $count; $i++) $list[] = array($i, $i);
	}
	//если страниц пагинатора больше $count_max, так как пагинатор расчитан только на $count_max ссылок
	else {
		//активная в начале  [1][2][3][4][5][..][100], если она не замыкает группу (5)
		if ($n < ($e = $count_max - 2)) {
			for ($i = 1; $i <= $e; $i++) $list[] = array($i, $i);            //$count_max-2 первых ссылок
			$list[] = array(ceil(($count + $e) / 2), 0);                //[..]
			$list[] = array($count, $count);                        //последняя ссылка
		}
		//активная в коце [1][..][96][97][98][99][100], если она не начинает группу (96)
		elseif ($n > ($s = $count - $count_max + 2 + 1)) {
			$list[] = array(1, 1);                                        //первая ссылка
			$list[] = array(ceil(($s + 1) / 2), 0);                        //[..]
			for ($i = $s; $i <= $count; $i++) $list[] = array($i, $i);    //$count_max-2 последних ссылок
		}
		//активная в середине [1][..][49][50][51][..][100]
		else {
			$s = $n - ceil(($count_max - 4 - 1)/2);
			$e = $n + floor(($count_max - 4 - 1)/2);
			$list[] = array(1,1);										//первая ссылка
			$list[] = array((ceil(($s + 1)/2)),0);						//[..]
			for ($i = $s; $i<=$e; $i++) $list[] = array ($i,$i);		//$count_max-4 средних ссылок
			$list[] = array(ceil(($count + $e)/2),0);				//[..]
			$list[] = array($count,$count);						//последняя ссылка
		}
	}
	?>
	<div class="pagination_normal">
		<ul class="pagination">
			<?php
			//предыдущая
			if ($n<=1) {
				?>
			<li class="disabled"><span class="button"><?=i18n('common|pagination_prev')?></span></li>
				<?php
			}
			else {
				?>
			<li><a class="button" href="<?= pagination_link('n', 1, 1) ?>"><?= i18n('common|pagination_prev') ?></a></li>
				<?php
			}
			//список страниц
			foreach ($list as $k=>$v) {
				$name = $v[1]==0 ? '...' : $v[0];
				//текущая
				if ($v[0]==$n) echo '<li class="current"><span class="button">'.$name.'</span></li>';
				//остальные
				else echo '<li><a class="button" href="'.pagination_link ('n',$v[0],1).'">'.$name.'</a></li>';
			}
			?>
			</li>
			<?php
			//следующая
			if ($n>=$count) {
				?>
			<li class="disabled"><span class="button"><?=i18n('common|pagination_next')?></span></li>
				<?php
			}
			else {
				?>
			<li><a class="button" href="<?=pagination_link ('n',$n+1,1)?>"><?=i18n('common|pagination_next')?></a></li>
				<?php
			}
			?>
		</ul>
	</div>
<?php } ?>