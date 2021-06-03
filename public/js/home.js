// Scroll function to avoid weird home page on load
let el = document.querySelector(".scroll-container");
$( document ).ready(function() {
	if (el.scrollTop == 0)
	{
		let accueil = document.querySelector('#accueil');
		accueil.scrollIntoView({behavior: "smooth", block: "start", inline: "nearest"});
	}
});
// Scroll icon function
el.addEventListener("scroll", function () {
	// console.log(window.screen.width);
	if (window.screen.width >= 925)
	{
		let scrollHeight = el.scrollHeight - el.clientHeight;
		let currentHeight = el.scrollTop;
		// console.log(scrollHeight+" "+currentHeight);
		if (currentHeight > scrollHeight - 10)
		{
			document.querySelector('.my-scroll-down-arrow').style.visibility = 'collapse';
		} 
		else
		{
			document.querySelector('.my-scroll-down-arrow').style.visibility = 'visible';
		}
	}
	else{
		document.querySelector('.my-scroll-down-arrow').style.visibility = 'collapse';
	}
});
// Allow the arrow to actually have an impact on the scroll on click
let arrow = document.querySelector('.my-scroll-down-arrow');
arrow.addEventListener("click", function () {
	console.log("started from the bottom now we here");
	el.scrollBy(0, window.innerHeight);
});