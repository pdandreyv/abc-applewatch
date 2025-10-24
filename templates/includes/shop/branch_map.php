<?php

if ($abc['branches']) {
	//кнопки с филиалами
	foreach ($abc['branches'] as $k=>$v) {
		?>
		<a href="#" class="click" data-i="<?=$k?>" data-lat="<?=$v['lat']?>" data-lng="<?=$v['lng']?>"><?=$v['name']?></a>
		<?php
	}
	?>
	<div id="map_google" style="display:block; width:100%; height:400px"></div>
	<?=html_sources('return','google_map google_markerclusterer');?>
	<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", function () {
			var	marker = [],
				map = new google.maps.Map(document.getElementById('map_google'), {
					center: new google.maps.LatLng(<?=$abc['branches'][0]['lat']?>,<?=$branches[0]['lng']?>),
					zoom: 12
				});
			var markers = [
	<?php
	foreach ($abc['branches'] as $qk=>$v) {
		?>
				['<?=$v['id']?>',<?=$v['lat']?>,<?=$v['lng']?>],
		<?php
	}
	?>
			];
			markers.forEach(function(item, i) {
				marker[i] = new google.maps.Marker({
					position: new google.maps.LatLng(markers[i][1],markers[i][2]),
					map: map,
					id:markers[i][0]
				});
				google.maps.event.addListener(marker[i], 'click', function() {
					infowindow = new google.maps.InfoWindow();
					infowindow.open(map,marker[i]);
					$.get('/api/branch_map/', {'id':marker[i]['id']},function(data){
						if (data.content) {
							infowindow.setContent(data.content);
						}
					});
				});
			});
			new MarkerClusterer(map, marker);

			/* */
			$('.click').click(function(){
				var lat = $(this).data('lat');
				var lng = $(this).data('lng');
				var i = $(this).data('i');
				//alert(lat+'-'+lng);
				map.setCenter({'lat':lat,'lng':lng});
				infowindow = new google.maps.InfoWindow();
				infowindow.open(map, marker[i]);
				$.get('/api/branch_map/', {'id':marker[i]['id']},function(data){
					if (data.content) {
						infowindow.setContent(data.content);
					}
				});
				return false;
			});
			/* */
		});
	</script>
	<?php
}