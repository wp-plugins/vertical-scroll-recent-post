/**
 *     Vertical scroll recent post
 *     Copyright (C) 2011 - 2014 www.gopiplus.com
 *     http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */ 

;!(function ($) {
    $.fn.classes = function (callback) {
        var classes = [];
        $.each(this, function (i, v) {
            var splitClassName = v.className.split(/\s+/);
            for (var j in splitClassName) {
                var className = splitClassName[j];
                if (-1 === classes.indexOf(className)) {
                    classes.push(className);
                }
            }
        });
        if ('function' === typeof callback) {
            for (var i in classes) {
                callback(classes[i]);
            }
        }
        return classes;
    };
})(jQuery);

function slideDown( element ) {
    var divs;
    var speed = element.attr( 'data-speed' ) * 1000;
    var classes = element.classes();
    var special_class = jQuery.grep( classes, function( n, i ) {
        if ( n.indexOf( 'vsrp_id' ) > -1 )
            return i;
    });
    special_class += 'vsrp_remove';

    var count = jQuery( '.'+special_class );
    if ( count.length == 0 ) {
        divs = element.children();
        var tmp = jQuery( divs[divs.length-1] ).clone();
        element.prepend( tmp );
        jQuery( divs[divs.length-1] ).addClass( special_class );
    }
    divs = element.children();
    var height = jQuery( divs[0] ).outerHeight();
    var wanted_height = 0;

    var top_tmp = divs.css( 'top' );
    top_tmp = parseInt( top_tmp, 10 ) || 0;
    if( top_tmp < 0 ) {
        wanted_height = top_tmp * -1;
    } else {
        divs.css( 'top', height * -1 );
    }

    var top = jQuery( divs ).css( 'top' );
    top = parseInt( top, 10 ) || 0;
    if( wanted_height == 0 ) {
        wanted_height = 0 - top;
    }

    jQuery( divs ).animate(
        { top: "+="+wanted_height },
        speed,
        'linear',
        function(){
            jQuery( '.'+special_class ).remove();
            divs.css( "top", 0 );
        }
    );
}

function slideUp( element ) {
    var speed = element.attr( 'data-speed' ) * 1000;
    var classes = element.classes();
    var special_class = jQuery.grep( classes, function( n, i ) {
        if ( n.indexOf( 'vsrp_id' ) > -1 )
            return i;
    });
    special_class += 'vsrp_remove';

    var divs = element.children();
    var tmp = jQuery( divs[0] ).clone();
    var height = jQuery( divs[0] ).outerHeight();
    element.append( tmp );
    jQuery( divs[0] ).addClass( special_class );
    var divs = element.children();

    var top = jQuery( divs ).css( 'top' );
    top = parseInt( top, 10 ) || 0;
    var wanted_height = height + top;

    jQuery( divs ).animate(
        { top: "-="+wanted_height },
        speed,
        'linear',
        function(){
            jQuery( '.'+special_class ).remove();
            divs.css( "top", 0 );
        }
    );
}

jQuery( document ).ready( function(){
    var tmp = 0;
    jQuery.each( jQuery( '.vsrp_wrapper' ), function() {
        var element = jQuery( this );
        var class_element = 'vsrp_id_' + (tmp++);
        var direction = element.attr( 'data-direction' );
        var delay = element.attr( 'data-delay-seconds' ) * 1000;
        element.addClass( class_element );
        var intervalID;

        if ( direction == 1 ) {
            intervalID = setInterval( slideUp, delay, element );
        } else {
            intervalID = setInterval( slideDown, delay, element );
        }
        element.on( 'mouseenter', function() {
            var tmp = jQuery( this ).children();
            tmp.stop();
            clearInterval( intervalID );
        });
        element.on( 'mouseleave', function() {
            if ( direction == 1 ) {
                intervalID = setInterval( slideUp, delay, element );
            } else {
                intervalID = setInterval( slideDown, delay, element );
            }
        });
    });

    if ( jQuery( ".nav-tab-wrapper > a" ).length >= 1 ) {
        jQuery( ".nav-tab-wrapper > a" ).click( function() {
            jQuery( ".nav-tab-wrapper > a" ).removeClass( "nav-tab-active" );
            jQuery( this ).addClass( "nav-tab-active" );
            jQuery( ".table" ).addClass( "ui-tabs-hide" );

            var item_clicked = jQuery( this ).attr( "href" );
            jQuery( item_clicked ).removeClass( "ui-tabs-hide" );
            return false;
        });
    }
    if ( jQuery( ".fade" ).length >= 1 ) {
        jQuery( ".fade" ).delay( 1500 ).fadeOut();
    }
} );