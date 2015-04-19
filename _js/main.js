$(document).ready(function() {
    cookies();
    $('#close-cookie-bar').click(function(event){
        event.preventDefault();
        $('#cookie-disclaimer').hide();
    });
});

function cookies() {
    if(!$.cookie('a-ok-cookie') && !$.cookie('no-cookies-cookie')) {
        $.cookie("a-ok-cookie", 1, { path: "/", expires : 365*5 });

        var bar,
            host = location.host,
            url = 'http://'+host+'/cookie-policy/';

        bar = '<div id="cookie-disclaimer"><p>';
        bar += 'We use cookies to help improve our site, <a href="';
        bar += url;
        bar +='">find out more</a> <a href="#" id="close-cookie-bar" class="close-cookie-bar"></a>';
        bar += '</p></div>';
        $('body').append(bar);
    }
}