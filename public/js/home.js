// Scroll function to avoid weird home page on load
let el = document.querySelector(".scroll-container");
$(document).ready(function () {
  if (el.scrollTop == 0) {
    let accueil = document.querySelector("#accueil");
    accueil.scrollIntoView({
      behavior: "smooth",
      block: "start",
      inline: "nearest",
    });
  }
});
// Scroll icon function
el.addEventListener("scroll", function () {
  // console.log(window.screen.width);
  if (window.screen.width >= 925) {
    let scrollHeight = el.scrollHeight - el.clientHeight;
    let currentHeight = el.scrollTop;
    // console.log(scrollHeight+" "+currentHeight);
    if (currentHeight > scrollHeight - 10) {
      document.querySelector(".my-scroll-down-arrow").style.visibility =
        "collapse";
    } else {
      document.querySelector(".my-scroll-down-arrow").style.visibility =
        "visible";
    }
  } else {
    document.querySelector(".my-scroll-down-arrow").style.visibility =
      "collapse";
  }
});
// Allow the arrow to actually have an impact on the scroll on click
let arrow = document.querySelector(".my-scroll-down-arrow");
arrow.addEventListener("click", function () {
  el.scrollBy(0, window.innerHeight);
});
// This changes the shape of the divider according to wrap behaviour
let detectWrap = function (className)
{
  let firsts = document.querySelectorAll("div.flex-container div."+className+":first-of-type");
  let first = [];
  let inline = [];
  let block = [];
  let prevItem = null;
  let items = document.getElementsByClassName(className);
  for ([key, item] of Object.entries(items)) {
	let index = Array.prototype.indexOf.call(firsts, item); //if the item is in my NodeList of firsts, I set it appart
	if (prevItem==null || index!=-1)
	{
		first.push(item);
	}
	else if (prevItem.getBoundingClientRect().top < item.getBoundingClientRect().top)
	{
	  block.push(item);
    }
	else
	{
	  inline.push(item);
	}
    prevItem = item;
  }
  return {"first": first, "inline": inline, "block": block};
};

function grid()
{
  var wrappedItems = detectWrap("flex-divider");
  // When my items are side by side, I want the divider to be vertical
  for ([key, item] of Object.entries(wrappedItems["inline"]))
  {
	item.classList.remove("line-top");
    item.classList.add("line-left");
  }
  // When my items are on top of each other, I want the divider to be horizontal
  for ([key, item] of Object.entries(wrappedItems["block"]))
  {
    item.classList.remove("line-left");
    item.classList.add("line-top");
  }
}

window.onresize = function (event)
{
  grid();
};

//when document ready
document.addEventListener("DOMContentLoaded", function () { grid(); });
