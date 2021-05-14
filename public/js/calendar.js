'use strict';
let dt = new Date();
let tooltip_text = "motocross<br>10h30-16h30" // "" empty text to hide the tooltip
let jour_event = 25;
let mois_event = 4; // Mois - 1
let annee_event = 2021;
// google event
let lien = "https://calendar.google.com/event?action=TEMPLATE&amp;tmeid=MHZvZTZ1MzZ1MXNqZGxlYzBldGs2bWtuNnAgbm8za3B1Zm8zaGE5MnBkOTRiMTlrdGUwa29AZw&amp;tmsrc=no3kpufo3ha92pd94b19kte0ko%40group.calendar.google.com"


function renderDate() {
    let dateString = new Date();

    dt.setDate(1);
    let day = dt.getDay();

    let endDate = new Date(
        dt.getFullYear(),
        dt.getMonth() + 1,
        0
    ).getDate();

    let prevDate = new Date(
        dt.getFullYear(),
        dt.getMonth(),
        0
    ).getDate();

    let today = new Date();
    let event = new Date(annee_event, mois_event, jour_event);

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
        "Décembre"
    ];

    document.getElementById("icalendarMonth").innerHTML = months[dt.getMonth()] + " " + dt.getFullYear();

    let cells = "";
    let countDate = 0;

    for (let x = day; x > 1; x--) {
        cells += "<div class='icalendar__prev-date'>" + (prevDate - x + 2) + "</div>";
    }

    for (let i = 1; i <= endDate; i++) {
        if (i === event.getDate() && dt.getMonth() === event.getMonth() && dt.getFullYear() === event.getFullYear()) {
            cells += "<div class='icalendar__event'" + " data-html='true' data-toggle='tooltip' data-placement='top' title='" + tooltip_text +"' ><a class='text-white' href='"+ lien +"'>" + i + "</a></div>"; // tooltip'>" + i + tooltip_string +
        }
        else if (i === today.getDate() && dt.getMonth() === today.getMonth() && dt.getFullYear() === today.getFullYear()) {
            cells += "<div class='icalendar__today'>" + i + "</div>";
        }
        else {
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
    if (param === 'prev') {
        dt.setMonth(dt.getMonth() - 1);
    } else if (param === 'next') {
        dt.setMonth(dt.getMonth() + 1);
    }

    renderDate();
}