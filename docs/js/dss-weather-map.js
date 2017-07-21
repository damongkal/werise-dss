window.google_map_ready = 0;
function initWeriseGoogleMap()
{
    window.google_map_ready = 1;
    console.log('map ready');
}
var WeriseGoogleMap = {
    apikey: 'AIzaSyB3CWH7h3gqcvIn7mKdiXYEIpbfDWHEkrw',
    showPOI: function(container_id,markers){        
        
        if (window.google_map_ready===0)
        {
            return;
        }
        var position = new google.maps.LatLng(markers[0][1], markers[0][2]);
        console.log('lat:'+position.lat());
        console.log('lon:'+position.lng());
        var mapOptions = {
            zoom: 6,
            center: position
        };
        // display a map on the page
        var map = new google.maps.Map(document.getElementById(container_id), mapOptions);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: markers[0][0]
        });        
        map.setCenter(position);
        map.setZoom(6);
        google.maps.event.addListenerOnce(map, 'idle', function(){
            console.log('map rendered');
        });            
    },
    showStaticPOI: function(container_id,marker){            
        console.log(marker);
        var latlon = marker[1]+','+marker[2];
        var location = marker[0];
        var mapurl = 'https://maps.googleapis.com/maps/api/staticmap?key='+WeriseGoogleMap.apikey+'&zoom=10&size=560x300&center='+latlon+'&markers=color:blue|'+latlon;
        console.log(mapurl);
        var mapimg = '<img src="'+encodeURI(mapurl)+'" width="560" height="300" /><p style="text-align:center">'+location+'</p>';
        jQuery('#'+container_id).html(mapimg);
    },
    showEmbedPOI: function(container_id,marker){            
        console.log(marker);
        var latlon = marker[1]+','+marker[2];
        var location = marker[0];
        var mapurl = 'https://www.google.com/maps/embed/v1/view?key='+WeriseGoogleMap.apikey+'&zoom=12&center='+latlon;
        console.log(mapurl);
        var mapimg = '<iframe width="560" height="300" frameborder="0" style="border:0" src="'+encodeURI(mapurl)+'" allowfullscreen></iframe><p style="text-align:center">'+location+'</p>';
        jQuery('#'+container_id).html(mapimg);
    }    
};
/**
 * 
 * @param {string} country
 * @returns {undefined}
 */
function draw_google_maps(country, source) {
    if (window.show_googlemaps===false)
    {
        return;
    }
    // get markers
    jQuery.ajax({
        type: "GET",
        url: "ajax.php",
        data: "pageaction=map&country="+country+"&source="+source,
        dataType : 'json',
        timeout : 5000,
        success: function (data) {
            if (data===false)
            {
                showErrorChart('');
                return;
            }
            if(data!=''){
                draw_google_maps2(data);
            }
        },
        error: function (e, t, n) {
            weriseApp.error('ajax error: get_map_markers');
            return;
        }
    });    
}

function draw_google_maps2(markers) {
    var map;
    var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        zoom: 4
    };
                    
    // Display a map on the page
    map = new google.maps.Map(document.getElementById("homeimages"), mapOptions);
    map.setTilt(45);        

    // Display multiple markers on a map
    var infoWindow = new google.maps.InfoWindow(), marker, i;
    
    // Loop through our array of markers & place each one on the map  
    for( i = 0; i < markers.length; i++ ) {
        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: markers[i][0]
        });

        // Allow each marker to have an info window    
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent(marker.title);
                infoWindow.open(map, marker);
            }
        })(marker, i));

    }
    // Automatically center the map fitting all markers on the screen
    map.fitBounds(bounds);
    
    google.maps.event.addListenerOnce(map, 'idle', function(){
        //showErrorChart('map done');
    });    
}

// google.maps.event.addDomListener(window, 'load', initialize_google_maps);