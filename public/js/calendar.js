"use strict";
let dt = new Date();
let my_data = document.getElementById('hereData');
let tooltip_text1 = "";
let tooltip_text2 = "";
let lien1 = "#";
let lien2 = "#";
if (my_data.dataset.date1.localeCompare(my_data.dataset.date2) == 0)
{
  tooltip_text1 = "motocross<br>adultes&kids"; // "" empty text to hide the tooltip
}
else
{
  tooltip_text1 = "motocross<br>adultes";
  tooltip_text2 = "motocross<br>kids";
}
// google events
if (my_data.dataset.link1)
{
  lien1 = my_data.dataset.link1;
}
if (my_data.dataset.link2)
{
  lien2 = my_data.dataset.link2;
}
function renderDate() {
    dt.setDate(1);
    let day = dt.getDay();

    let endDate = new Date(
      dt.getFullYear(),
      dt.getMonth() + 1,
      0
    ).getDate();

    let prevDate = new Date(dt.getFullYear(), dt.getMonth(), 0).getDate();

    let today = new Date();
    let event1 = new Date(my_data.dataset.date1);
    let event2 = new Date(my_data.dataset.date2);

    let months = [
      "Janvier",
      "Février",
      "Mars",
      "Avril",
      "Mai",
      "Juin",
      "Juillet",
      "Août",
      "Septembre",
      "Octobre",
      "Novembre",
      "Décembre",
    ];

    document.getElementById("icalendarMonth").innerHTML =
      months[dt.getMonth()] + " " + dt.getFullYear();

    let cells = "";
    let countDate = 0;

    for (let x = day; x > 1; x--) {
      cells +=
        "<div class='icalendar__prev-date'>" +
        (prevDate - x + 2) +
        "</div>";
    }

    for (let i = 1; i <= endDate; i++) {
      if (
        i === event1.getDate() &&
        dt.getMonth() === event1.getMonth() &&
        dt.getFullYear() === event1.getFullYear()
      ) {
        cells +=
          "<div class='icalendar__event'" +
          " data-html='true' data-toggle='tooltip' data-placement='top' title='" +
          tooltip_text1 +
          "' ><a class='text-white' href='" +
          lien1 +
          "'>" +
          i +
          "</a></div>";
      } 
      else if (
        i === event2.getDate() &&
        dt.getMonth() === event2.getMonth() &&
        dt.getFullYear() === event2.getFullYear()
      ) {
        cells +=
          "<div class='icalendar__event'" +
          " data-html='true' data-toggle='tooltip' data-placement='top' title='" +
          tooltip_text2 +
          "' ><a class='text-white' href='" +
          lien2 +
          "'>" +
          i +
          "</a></div>";
      }
      else if (
        i === today.getDate() &&
        dt.getMonth() === today.getMonth() &&
        dt.getFullYear() === today.getFullYear()
      ) {
        cells += "<div class='icalendar__today'>" + i + "</div>";
      } else {
        cells += "<div>" + i + "</div>";
      }

      countDate = i;
    }

    let reservedDateCells = countDate + day + 1;
    for (let j1 = reservedDateCells, j2 = 1; j1 <= 42; j1++, j2++) {
      cells += "<div class='icalendar__next-date'>" + j2 + "</div>";
    }

    document.getElementsByClassName("icalendar__days")[0].innerHTML = cells;
}

renderDate();

function moveDate(param) {
  if (param === "prev") {
    dt.setMonth(dt.getMonth() - 1);
  } else if (param === "next") {
    dt.setMonth(dt.getMonth() + 1);
  }

  renderDate();
  goTooltip();
}
// tooltip function
$(document).ready(function(){
     goTooltip();
});
function goTooltip(){
  $("[data-toggle='tooltip']").tooltip();  
}