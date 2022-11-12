if (location.pathname == '/swishp/') {
    slideShow();
    getSwisTime();
}

function slideShow() {
    const slides = $("#js-slide").find("img");
    const slideLength = slides.length;

    let now = 0;
    let next = 1;

    const fade = 1000;
    const show = 8000;

    slides.hide();
    slides.eq(0).show();

    const slidesshow = () => {

        slides.eq(now % slideLength).fadeOut(fade).removeClass("zoom");
        slides.eq(next % slideLength).fadeIn(fade).addClass("zoom");

        now++;
        next++;
    };

    setInterval(slidesshow, show);
}

function getSwisTime() {
    var date = new Date();
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        weekday: 'short',
    }
    var dateArray = date.toLocaleDateString('en-US', options, {
        timeZone: 'Europe/Zurich'
    }).split(' ');

    var year = dateArray[3];
    var month = dateArray[1];
    var datea = dateArray[2].substr(0, 2);
    var week = dateArray[0].substr(0, 3);
    var timearray = date.toLocaleTimeString('en-US', {
        timeZone: 'Europe/Zurich'
    }).split(':');

    var hour = timearray[0];
    var minutes = timearray[1];
    var ampm = timearray[2].substr(-2, 2);

    var elem = document.getElementById('js-slide');
    var new_elem = document.createElement('div');
    new_elem.id = "swis_infomation";
    new_elem.className = "swis_infomation";
    new_elem.innerHTML = `<label class="year">${year}</label>
            <label class="month">${month}</label>
            <label class="date">${datea}</label>
            <label class="week">${week}</label>
            <label class="ampm">${ampm}</label>
            <label class="time">${hour}:${minutes}</label>
    `;
    elem.appendChild(new_elem);
    console.log(date.toLocaleTimeString('en-US', {
        timeZone: 'Europe/Zurich'
    }));
}

const overlay = document.getElementById('modal_content');
var modal_bool = false;
document.addEventListener("click", (e) => {
    console.log("aaaaaaaaaaaaaaaa");
    let pattern = /^ib[0-9]{1,5}/;
    if (pattern.test(e.target.id)) {
        modal_show(e);
    } else {
        modal_close();
    }
});

function modal_show(e) {
    document.getElementById('trigger').checked = true;

    let itemnum = e.target.id.slice(2, 5);
    let item = document.getElementById('ibitem' + itemnum).innerHTML;
    let edittime = document.getElementById('edittime' + itemnum).innerHTML;
    let imageelem = document.getElementById('ibimage' + itemnum);

    let imageURL = "";
    if (imageelem) {
        imageURL = imageelem.getAttribute('src');
    }

    var box_elem = document.createElement('div');
    box_elem.id = 'zoomContent';
    box_elem.className = 'zoomContent';
    box_elem.innerHTML = `<img src='${imageURL}' />
        <p class='edittime'>${edittime}</p>
        <p class='item'>${item}</p>
        `;

    //overlay.style.display = 'block';
    modal_bool = true;
    overlay.appendChild(box_elem);
}

function modal_close() {
    document.getElementById('trigger').checked = false;
    modal_bool = false;
    //overlay.style.display = 'none';
    overlay.removeChild(overlay.firstChild);
}
