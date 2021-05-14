// tooltip function
$(document).ready(function(){
	$("[data-toggle='tooltip']").tooltip();   
});

// map function
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

// Scroll icon function
let el = document.querySelector(".scroll-container");
el.addEventListener("scroll", function () {
	console.log(window.screen.width);
	if (window.screen.width >= 925)
	{
		let scrollHeight = el.scrollHeight - el.clientHeight;
		let currentHeight = el.scrollTop;

		if (currentHeight > scrollHeight - 10)
		{
			document.querySelector('.my-scroll-down-arrow').style.display = 'none';
		} 
		else
		{
			document.querySelector('.my-scroll-down-arrow').style.display = 'block';
		}
	}
	else{
		document.querySelector('.my-scroll-down-arrow').style.display = 'none';
	}
});