// Scroll icon function
let el = document.querySelector(".scroll-container");
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