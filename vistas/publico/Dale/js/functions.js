/*
Created by Alejandro Palacios https://github.com/AlexSoicalap
*/
/*Define some constants */
var ARTICLE_TITLE = document.title;
var ARTICLE_DESC = document.title;
var ARTICLE_URL = encodeURIComponent(window.location.href);
var MAIN_IMAGE_URL = encodeURIComponent($('meta[property="og:image"]').attr('content'));
$(function () {

    let winTop = (screen.height / 2) - (350 / 2);
    let winLeft = (screen.width / 2) - (520 / 2);



    $('.share-fb').click(function () {
        window.open('https://www.facebook.com/sharer.php?s=100&p[title]=' + ARTICLE_TITLE + '&p[summary]=' + ARTICLE_DESC + '&p[url]=' + ARTICLE_URL + '&p[images][0]=' + MAIN_IMAGE_URL, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=520,height=350');
    });

    $('.share-twitter').click(function () {
        open_window('http://twitter.com/share?url=' + ARTICLE_URL, 'twitter_share');
    });

    $('.share-google-plus').click(function () {
        open_window('https://plus.google.com/share?url=' + ARTICLE_URL, 'google_share');
    });

    $('.share-linkedin').click(function () {
        open_window('https://www.linkedin.com/shareArticle?mini=true&url=' + ARTICLE_URL + '&title=' + ARTICLE_TITLE + '&summary=&source=', 'linkedin_share');
    });

    $('.share-pinterest').click(function () {
        open_window('https://pinterest.com/pin/create/button/?url=' + ARTICLE_URL + '&media=' + MAIN_IMAGE_URL + '&description=' + ARTICLE_TITLE, 'pinterest_share');
    });

    $('.share-tumblr').click(function () {
        open_window('http://www.tumblr.com/share/link?url=' + ARTICLE_URL + '&name=' + ARTICLE_TITLE + '&description=' + ARTICLE_TITLE, 'tumblr_share');
    });

    function open_window(url, name) {
        window.open(url, name, 'height=320, width=640, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no');
    }
});