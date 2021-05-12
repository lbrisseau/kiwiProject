// J'ajoute ceci pour le tooltip
$(document).ready(function(){
	$("[data-toggle='tooltip']").tooltip();   
});

// J'ajoute ceci pour la carte
// Initialize and add the map
function initMap() {
	// The location of terrain
	const terrain = { lat: 48.133, lng: -1.640 };
	// The map, centered at terrain
	const map = new google.maps.Map(document.getElementById("map"), {
		zoom: 12,
		center: terrain,
	});
	// The marker, positioned at terrain
	const marker = new google.maps.Marker({
		position: terrain,
		map: map,
	});
}