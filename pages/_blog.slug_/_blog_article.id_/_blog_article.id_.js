$(document).ready(function() {
	map_initialize();
});





function map_initialize() {
	var mapOptions = {
			zoom: 18,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: locs[0].latlng,
			scrollwheel: false,
			disableDeafaultUI : false
		};
	
	map = new google.maps.Map(document.getElementById('map'), mapOptions);
	var mapbounds = new google.maps.LatLngBounds();
	for (var i = 0; i < locs.length; i++) {
		var type;
		var n = String.fromCharCode( 65 + i );
		if (locs[i].is_registration_point == 1) type = '_green';
		else type = '_yellow';
		var icon = 'http://www.google.com/intl/en_ALL/mapfiles/marker' + type + n + '.png';
		var marker = new google.maps.Marker({
			map: map,
			draggable: false,
			animation: google.maps.Animation.DROP,
			position: locs[i].latlng,
			title: locs[i].venue_name,
			icon: icon
		});
		mapbounds.extend(marker.position);
		do_marker_click(marker, locs[i]);
	}

	map.fitBounds(mapbounds);

}

function do_marker_click(marker, loc) {
	
	google.maps.event.addListener(marker, 'click', function() {
		if (infowindow) infowindow.close();
		infowindow = new google.maps.InfoWindow({
			content: loc.venue_name  + '<div class="small-desc">' + loc.address + '</div>'
		});
		infowindow.open(map, marker);
	});
}