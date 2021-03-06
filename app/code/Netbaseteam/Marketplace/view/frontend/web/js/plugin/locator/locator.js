define([
    'jquery',
    'locator_init',
    'locator_creator',
    'jquery/ui'
], function($,init,creator){
    return function (config) {
        $(document).ready(function(){
            var map;
            var markerclusterer = null;
            var infowindow = new google.maps.InfoWindow();
            var gmarkers = [];
            var directionOjs;
            var panorama;
            var defaultCenter = {lat: parseFloat(config.latDefault),lng:parseFloat(config.lngDefault)};
            var defaultZoomLv =  parseInt(config.zoomLevelDefault);
            var sv = new google.maps.StreetViewService();
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var myOptions = {
                zoom:defaultZoomLv,
                center: defaultCenter,
                streetViewControl: false
            };

            var bounds = new google.maps.LatLngBounds();
            map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

            var storeListData = init.getStoreListData();

            $.each(storeListData,function(key,item){
                var marker = createMarker(item) ;
            });

            markerCluster = new MarkerClusterer(map, gmarkers,{imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

            panorama = map.getStreetView();

            $('.store-item .street-view').click(function(){
                if($(window).width()<=767){
                    window.scrollTo(0,70);
                }
                var eq = $(this).attr('item-number');
                toggleStreetView(storeListData[eq].position);

            });

            $('.store-item .direction').click(function() {
                $('.store-item .option-direction').removeClass('popup-active');
                $(this).parents().eq(3).next().addClass('popup-active');
            });

            $('ul.vertical li.travel').click(function(){
                $(this).parent().children().removeClass('travel-active');
                $(this).addClass('travel-active');
                var str_point = $('.popup-active .directions-box .originA').val();
                if(str_point!=''){
                    var num = $(this).parent().attr('number-box');
                    num = parseInt(num);
                    google.maps.event.trigger(directionOjs[num], "place_changed");
                }

            });

            /*
             Trigger Click event on Marker
             */
            $('.store-item').click(function(){
                var eq = $(this).attr('item-number');
                $('.store-item').removeClass('store-active');
                $(this).addClass('store-active');
                google.maps.event.trigger(gmarkers[eq], "click");
            });

            $('button.get-direction').click(function(){
                var num = $('.popup-active .travel-list').attr('number-box');
                num = parseInt(num);

                google.maps.event.trigger(directionOjs[num], "place_changed");
            });

            $('.search-box .search-tab').click(function(){
                $('.search-box .search-tab').removeClass('active');
                $(this).addClass('active');
                if($(this).hasClass('search-distance')){
                    $('.search-by-distance').removeClass('hide');
                    $('.search-by-area').addClass('hide');
                }else{
                    $('.search-by-distance').addClass('hide');
                    $('.search-by-area').removeClass('hide');
                }
            });

            $('#slider-range-min').slider({
                min: 1,
                max: 500,
                slide: function( event, ui ){
                    creator.showSliderRange(ui.value,config.distanceUnit);
                },
                change: function(event, ui){
                    if(init.hasSearchPoint()){
                        google.maps.event.trigger(autocomplete, "place_changed");
                    }
                }
            });

            for (var i = 0; i < gmarkers.length; i++) {
                bounds.extend(gmarkers[i].getPosition());
            }

            map.fitBounds(bounds);
            if (i === 1) {
                $('.store-item').trigger( "click" );
            }
            /*
             Reset Map Option
             */
            $('.btn-reset-search-distance').click(function(){
                window.location.href = window.location.href;
            });

            $('.btn-reset-search-area').click(function(){
                window.location.href = window.location.href;
            });


            //set Default button search
            $('#auto-fill').keyup(function() {
                var empty = false;
                $('#auto-fill').each(function() {
                    if ($(this).val() == '') {
                        empty = true;
                    }
                });

                if (empty) {
                    $('.btn-search-distance').attr('disabled', true);
                } else {
                    $('.btn-search-distance').attr('disabled', false);
                }
            });

            $('.input-search-area').keyup(function() {
                var empty = true;
                $('input[name=shop_title]').each(function() {
                    if ($(this).val() !== '') {
                        empty = false;
                    }
                });

                $('input[name=shop_zipcode]').each(function() {
                    if ($(this).val() !== '') {
                        empty = false;
                    }
                });

                if (empty) {
                    $('.btn-search-area').attr('disabled', true);
                } else {
                    $('.btn-search-area').attr('disabled', false);
                }
            });

            var input = document.getElementById('auto-fill');

            var autocomplete = new google.maps.places.Autocomplete(input);
            var directionOjs = creator.createDirectionOj(storeListData);
            autocomplete.bindTo('bounds', map);
            var cityCircle = new google.maps.Circle();

            autocomplete.addListener('place_changed', function() {
                infowindow.close();
                cityCircle.setMap(null);
                if (!autocomplete.getPlace()||autocomplete.getPlace() == null||autocomplete.getPlace().geometry==null) {

                    alert("No details available for this location!");
                    return;
                }
                var place = autocomplete.getPlace();
                clearAllMarker();
                gmarkers = [];
                markerCluster.clearMarkers();

                map.setCenter(place.geometry.location);
                var radius = init.getSliderRange();
                var zoomLv = creator.getZoomByDistance(radius);
                map.setZoom(zoomLv);

                var Option = creator.createCircleOption(map,place.geometry.location,radius);
                cityCircle.setOptions(Option);
                getResultSearchDt(radius,place.geometry.location);

            });

            directionsDisplay.setMap(map);
            directionOjs = creator.createDirectionOj(storeListData);
            directionAction();

            function createMarker(store) {
                var point = new google.maps.LatLng(store.position);

                var marker = new google.maps.Marker({
                    position: point
                });

                marker.setMap(map);

                var infoContent = creator.createPopupContent(store.info);

                google.maps.event.addListener(marker, 'click', function() {
                    map.setCenter(marker.getPosition());
                    map.setZoom(17);
                    infowindow.setContent(infoContent);
                    infowindow.open(map,marker);

                });
                gmarkers.push(marker);
            }


            function toggleStreetView(position) {
                sv.getPanorama({location: position, radius: 50}, processSVData);
            }

            function processSVData(data, status) {
                if (status === 'OK') {
                    panorama.setPano(data.location.pano);
                    panorama.setPov({
                        heading: 70,
                        pitch: 0
                    });
                    panorama.setVisible(true);
                } else {
                    panorama.setVisible(false);
                    alert('Street View data not found for this location');

                }
            }

            function clearAllMarker() {
                for (var i = 0; i < gmarkers.length; i++) {
                    gmarkers[i].setMap(null);
                }
            }

            function getResultSearchDt(radius,center){
                var check = false;
                var point;
                var s = 0;
                for(var i = 0; i <storeListData.length; i++){
                    point = new google.maps.LatLng(storeListData[i].position);
                    check =  creator.checkStoreContain(radius,point,center);
                    if(check){
                        createMarker(storeListData[i]);
                        creator.showhideStoreContain(i,'show',s);
                        s++;
                    }else{
                        creator.showhideStoreContain(i,'hide',-1);
                    }

                    creator.updateCountStore(s);

                }
            }


            function directionAction(){
                if(directionOjs.length>0){
                    $.each(directionOjs,function(key,item){
                        item.addListener('place_changed', function(){
                            creator.onloadPage('show');
                            if(creator.handleErrorSearch(item)){
                                directionsDisplay.setMap(null);
                                creator.onloadPage('hide');
                                return;
                            }

                            directionsDisplay.setMap(map);
                            var  idPanel = creator.getPanelDirection(key);
                            directionsDisplay.setPanel(idPanel);
                            var destina = new google.maps.LatLng(storeListData[key].position);
                            var systemUnit = google.maps.UnitSystem.METRIC;
                            if(config.distanceUnit == 'mile'){
                                systemUnit = google.maps.UnitSystem.IMPERIAL;
                            }
                            var request = {
                                origin: item.getPlace().geometry.location,
                                destination: destina,
                                travelMode: creator.getTravelMode(),
                                unitSystem: systemUnit
                            };

                            directionsService.route(request, function(response, status) {
                                if (creator.handleErrDirecResult(status)) {
                                    directionsDisplay.setMap(null);
                                    creator.onloadPage('hide');
                                    return;
                                }
                                directionsDisplay.setDirections(response);
                            });
                            creator.onloadPage('hide');
                        });
                    });
                }

            }
        });
    }
});