// ..................................................ON LOAD........................................................
// Scroll functions to avoid weird home page on load
let el = document.querySelector(".scroll-container");
$(document).ready(function () {
  let whereToBlock = "";
  if (window.matchMedia("(min-width: 1000px)").matches) {
    whereToBlock = "center";
  } else {
    whereToBlock = "start";
  }
  if (el.scrollTop == 0) {
    let accueil = document.querySelector("#accueil");
    accueil.scrollIntoView({
      behavior: "smooth",
      block: whereToBlock,
      inline: "nearest",
    });
  }
  // Call to below function offsetAnchor to arrive at the right place when reloading page
  offsetAnchor(whereToBlock);
  // Calculate stuff for the nice dividers (see below functions detectWrap and grid)
  grid();
});

// ..................................................ARROW........................................................
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
function onclickArrow ()
{
  el.scrollTop += window.innerHeight;
}
let arrow = document.querySelector(".my-scroll-down-arrow");
arrow.addEventListener("click", onclickArrow, false);

// ..................................................DIVIDER........................................................
// This changes the shape of the divider according to wrap behaviour
// Function that classifies my items
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
// Function that remove and add classes to the items according to the classification
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
// Call to previous function on window resize
window.onresize = function (event)
{
  grid();
};

// .............................................CENTER PAGE ON SECTIONS..................................................
// Here are some functions that should help with anchor scroll centering
// The function actually applying the offset
function offsetAnchor(whereToBlock) {
  if (location.hash.length !== 0) {
    let section = document.querySelector(location.hash);
    section.scrollIntoView({
      behavior: "smooth",
      block: whereToBlock,
      inline: "nearest",
    });
  }
}
// Captures click events of all <a> elements with href starting with #
$(document).on('click', 'a[href^="#"]', function(event) {
  let whereToBlock = "";
  if (window.matchMedia("(min-width: 1000px)").matches) {
    whereToBlock = "center";
  } else {
    whereToBlock = "start";
  }
  // Click events are captured before hashchanges. Timeout
  // causes offsetAnchor to be called after the page jump.
  window.setTimeout(function() {
    offsetAnchor(whereToBlock);
  }, 1);
});