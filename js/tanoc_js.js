console.log(location.pathname);
if (location.pathname == '/swishp/' || location.pathname == '/index.php' || location.pathname == '/') {
    slideShow();
    getSwisTime();
    HashConvertToLink();
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
    console.log("swis time", date.toLocaleTimeString('en-US', {
        timeZone: 'Europe/Zurich'
    }));
}

const overlay = document.getElementById('modal_content');
document.addEventListener("click", (e) => {
    let pattern = /^ib[0-9]{1,5}/;
    let pt_edit = /^et[0-9]{1,5}/;

    if (pattern.test(e.target.id)) {
        modal_show(e);
    } else if (pt_edit.test(e.target.id)) {
        edit_show(e);
    } else if (e.target.id == 'close_button') {
        modal_close();
    }
});

function modal_show(e) {
    document.getElementById('trigger').checked = true;

    let itemnum = e.target.id.slice(2, 5);
    let item = document.getElementById('ibitem' + itemnum).innerHTML;
    let edittime = document.getElementById('edittime' + itemnum).innerHTML;

    let imageArray = document.getElementById('ib' + itemnum).textContent.split(',');
    let imageURL = "";
    let imgSlider = `<div class="slider6-wrap"><ul class="slider-6" id="js-slider-6">`;
    for (var index = 0; index < imageArray.length; index++) {
        if (imageArray[index] == "") break;
        imageURL = "./stock_images/" + imageArray[index];
        imgSlider += `<li><img src="${imageURL}" /></li>`;
    }
    imgSlider += `</ul><div class="thumbs_dots"></div></div>`;
    console.log("imgsrc:", imgSlider);

    var box_elem = document.createElement('div');
    box_elem.id = 'zoomContent';
    box_elem.className = 'zoomContent';
    box_elem.innerHTML = `${imgSlider}
        <p class='edittime'>${edittime}</p>
        <p class='item'>${item}</p>
        `;
    overlay.appendChild(box_elem);


    var $slider6 = $('#js-slider-6');

    /*--- 連動サムネイル（ドット）設定 -----------------------*/
    // スライダー初期化時
    $slider6.on('init', function (event, slick, currentSlide, nextSlide) {
        // スライドアイテム取得
        slideItem = $('#js-slider-6 .slick-slide')
        // スライドの数だけループ
        for (var i = 0; i < slick.slideCount; i++) {
            // ループ回数をキーにn番目のスライドを取得
            var slideImg = slideItem.filter(function () {
                return $(this).data('slick-index') === i;
            }).find('img').clone();
            // n番目のドットを取得
            var dot = $('.thumbs_list').find("li").eq(i);
            // n番目のスライド画像のURLを取得
            var src = slideImg.attr('src');
            // n番目のドットにn番目のスライド画像を背景に当て込み
            dot.css('background', "url(".concat(src, ")"));
            // 背景の表示の仕方を指定
            dot.css('background-size', 'cover');
        }
    });

    $slider6.slick({
        arrows: false, // 前・次のボタンを表示しない
        dots: true, // ドットナビゲーションを表示する
        dotsClass: 'thumbs_list', // ドットのクラス名を変更
        appendDots: $('.thumbs_dots'), // ドットの生成位置を変更
        customPaging: function (slick, index) { // ドットの中身を空にする
            return '';
        },
        fade: true, // スライド切り替えをフェード
        autoplay: true, //自動再生させない
        slidesToShow: 1, // 表示させるスライド数
    });

}

function edit_show(e) {
    document.getElementById('trigger').checked = true;

    let itemnum = e.target.id.slice(2, 5);
    let itemcat = document.getElementById('ibcat' + itemnum).innerHTML;
    let titleelem = document.getElementById('ibtitle' + itemnum);
    let title = 'nontitle';
    let item = document.getElementById('ibitem' + itemnum).innerHTML.replace(/<br>/g, "");
    // itemのハッシュタグ部分のaタグを削除する
    item = item.replace(new RegExp('<a[^>]+.*?>', 'g'), '');
    item = item.replace(new RegExp('</a>', 'g'), '');
    //let imageelem = document.getElementById('ibimage' + itemnum);
    let imageArray = document.getElementById('ib' + itemnum).textContent.split(',');


    if (titleelem != null) {
        title = titleelem.innerHTML;
    }
    else {
        title = item.slice(0, 12);
    }

    let imageURL = "";
    let imgsrc = "";
    for (var index = 0; index < imageArray.length; index++) {
        if (imageArray[index] == "") break;
        imageURL = "./stock_images/" + imageArray[index];
        imgsrc += `<img src='${imageURL}' />`;
    }

    var box_elem = document.createElement('div');
    box_elem.id = 'zoomContent';
    box_elem.className = 'zoomContent';
    box_elem.innerHTML = `<form id='stkform' method='post' action='' enctype='multipart/form-data'>
    <input id='stk_num' type='hidden' name='stknum' value='${itemnum}' readonly>
    <div id='stk_title_box'>
      <label for='stk_title'>タイトル</label>
      <input id='stk_title' type='text' name='stktitle' value='${title}'>
    </div>
    <label for='stk_item'>記事</label>
    <textarea id='stk_item' type='text' name='stkitem' value=''>${item}</textarea>
    <label for='stk_image'>イメージ画像</label>
    <input id='stk_image' type='file' name='stkimage[]' accept='image/*' multiple>
    <input id='stk_imageurl' type='hidden' name='imageurl' value='${imageArray}' multiple>
    <div id='preview'>${imgsrc}</div>
    <button class='stksubmit' id='stksubmit' name='stksubmit' value='stksubmit'>変更</button>
    <button class='delete' id='delete' name='delete' value='delete'>削除</button>
  </form>`;

    overlay.appendChild(box_elem);

    if (imageArray) {
        var elem = document.getElementById('preview');
        elem.style.display = "block";
    }

    document.getElementById('stk_image').addEventListener('change', function (e) {
        resetPreview();
        var elem = document.getElementById('preview');
        elem.style.display = "block";
        for (var num in e.target.files) {
            var file = e.target.files[num];
            var blobUrl = window.URL.createObjectURL(file);
            var img = new Image();
            img.src = blobUrl;
            elem.appendChild(img);
        }
    });

    if (itemcat == "works") {
        document.getElementById('stk_title_box').style.display = "block";
    }
}

function modal_close() {
    while (overlay.firstChild) {
        overlay.removeChild(overlay.firstChild);
    }

    document.getElementById('trigger').checked = false;
    console.log('modal close', document.getElementById('trigger').checked);
}

$(function () {
    $('a[href^="#"]').click(function () {
        var href = $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top;
        var speed = 500;
        $("html, body").animate({
            scrollTop: position
        }, speed, "swing");
        return false;
    });
});


function resetPreview() {
    var element = document.getElementById("preview");
    while (element.firstChild) {
        element.removeChild(element.firstChild);
    }
}

function HashConvertToLink() {
    var ele = document.getElementsByClassName('item');
    let uri = "https://www.google.com/maps/place/";

    for (var i = 0; i < ele.length; i++) {
        let words = ele[i].textContent.split('#');
        if (words.length >= 2) {
            ele[i].innerHTML = words[0];
            for (var j = 1; j < words.length; j++) {
                ele[i].innerHTML += "<a href='" + uri + spaceToPlus(words[j]) + "' target='_blank' rel='noopener noreferrer'>#" + words[j] + "</a>";
            }
        }
    }
}

function spaceToPlus(str) {
    return str.replace(" ", "+");
}
