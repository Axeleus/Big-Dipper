/**
 * Created by Vitaly Kukin on 23.04.2016.
 */
jQuery(function($){

    var $window = $(window),
        seo = $('#seo_description');

    $window.load(function(){

        if( seo.length )
            simbolcount(seo);
    });

    function simbolcount( el ) {
        var s = el.val().length,
            c = (s > 160) ? "red" : "",
            p = el.parents('tr').find('p');

        el.after('<div class="max-simbol"><span class="chars '+c+'">'+s+'</span> <span class="description">'+p.html()+'</span></div>');

        p.remove();
    }

    seo.on("keypress", function() {

        changeChars( $(this) );
    });

    seo.on('keydown', function(e){
        if(e.keyCode === 8 || e.keyCode === 46 ){
            changeChars( $(this) );
        }
    });

    function changeChars( el ) {
        var s = el.val().length,
            c = (s > 160) ? "red" : "";

        el.parents('tr').find('.max-simbol span.chars').text(s).removeClass('red').addClass(c);
    }

    $('#bidi-tabs .tab-menu').on('click', 'a', function(e){

        e.preventDefault();

        var tb = $(this).parents('#bidi-tabs'),
            th = $(this).attr('href').replace('#', '');

        tb.find('.tab-menu li').removeClass('active');
        tb.find('.tab-content .tab-item-content').hide();
        tb.find('.tab-content #bidi-tab-item-'+th).show();

        $(this).parent('li').addClass('active');
    });
});