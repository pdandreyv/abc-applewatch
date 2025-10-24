<?php
$q = array();

$q['years'] = array();
$start = 2019;
$end = date('Y');
for ($i=$end; $i>=$start; $i--) {
	$q['years'][$i]= $i;
}
$year = @$_GET['year'];
if (!isset($q['years'][$year])) $year = date('Y');

$where = "created_at>'".$year."-01-01' AND created_at<'".($year+1)."-01-01'";

$q['totals']['users'] = mysql_select("SELECT COUNT(id) FROM users WHERE id>1 AND ".$where,'string');
$q['totals']['orders'] = mysql_select("SELECT COUNT(id) FROM orders WHERE ".$where,'string');
$q['totals']['feedback'] = mysql_select("SELECT COUNT(id) FROM feedback WHERE ".$where,'string');

$q['orders'] = mysql_select("
		SELECT *
		FROM orders
		ORDER BY created_at DESC
		LIMIT 5
	",'rows');

$q['users'] = mysql_select("
		SELECT *
		FROM users
		WHERE id>1
		ORDER BY created_at DESC
		LIMIT 5
	",'rows');

?>


<style>
	.brands img {width: 50px}
</style>
<div class="row" style="margin-right:-30px; margin-left:-30px; ">
	<div class="form-group col-xl-2">
		<select class="form-control" name="year" onchange="top.location='admin.php?m=index&year='+this.value;"><?=select(@$_GET['year'],$q['years'])?></select>
	</div>
</div>


<div class="row" style="margin-right:-30px; margin-left:-30px; ">
	<div class="col-md-4">
		<div class="card card-body">
			<h3 class="mb-3">
				<?=$q['totals']['users']?>
				<small>Пользователей</small>
			</h3>
			<?php /*
			<div class="progress mb-2" style="height: 5px">
				<div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
			<p class="font-size-11 m-b-0">
				<span class="text-success">+ 1.2%</span> than yesterday
			</p>
            */?>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card card-body">
			<h3 class="mb-3">
				<?=$q['totals']['orders']?>
				<small>Заказов</small>
			</h3>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card card-body">
			<h4 class="mb-3">
				<?=$q['totals']['feedback']?>
				<small>Лидов</small>
			</h4>
		</div>
	</div>
</div>


<div class="row" style="margin-right:-30px; margin-left:-30px; ">
	<div class="col-md-6">
		<div class="card card-body brands">
			<h6 class="card-title">5 последних заказов</h6>
			<div class="list-group list-group-flush">
				<?php foreach ($q['orders'] as $k=>$v) {?>
					<div class="list-group-item p-t-b-10 p-l-r-0 d-flex align-items-center justify-content-between">
						<div class="d-flex align-items-center">
							<?=$v['email']?>
						</div>
						<div class="d-flex align-items-center">
							<strong class="m-r-20"><?=$v['total']?></strong>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="card card-body">
			<h6 class="card-title">5 последних пользователей</h6>
			<div class="list-group list-group-flush">
				<?php foreach ($q['users'] as $k=>$v) {?>
					<div class="list-group-item p-t-b-10 p-l-r-0 d-flex align-items-center justify-content-between">
						<div class="d-flex align-items-center">
							<?=$v['email']?>
						</div>
						<div class="d-flex align-items-center">
							<div class="m-r-20"><?=@$v['events']?></div>
							<div style="width: 100px; text-align: right"></div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>