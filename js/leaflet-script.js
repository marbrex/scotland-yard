// Prompt an alert message when user tries to leave the game page
window.onbeforeunload = () => true;

// Loading quarters' data from a GeoJSON file
var promiseQuartierData = fetch("js/quartierData.geojson")
    .then(response => response.json())
    .catch(error => console.error(`promiseQuartierData: ${error}`));


function drawMap (idQChoisi, transportChoisi) {

    console.log(document.cookie);

    let dataToSend = "isFetch=true&onlyController=true";
    if (idQChoisi && transportChoisi)
        dataToSend += `&idQChoisi=${idQChoisi}&transportChoisi=${transportChoisi}`;

    // ===== Requesting $_SESSION variables from PHP =====
    var promiseVars = fetch("index.php?page=show-quartier-map", {
        method : "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: dataToSend
    })

    .then(response => response.json())

    // Checking if the game is ended, and if so, displaying corresponding message
    .then(data => {
        console.log('Session: ');
        console.log(data);
        if (data.gameEnded) {
            window.onbeforeunload = null; // Remove prompting alert on page quit
            if (data.victory)
                // Victory
                document.getElementById('game-ended-title').innerHTML = 'Victory';
            else if (data.gameover)
                // Gameover
                document.getElementById('game-ended-title').innerHTML = 'Gameover';

            document.getElementById('game-ended-window').style.display = 'flex';
            document.getElementById('game-ended-message').innerHTML = data.gameEndedMessage;
        }
        // GAME IS STILL GOING - Passing data to the next promise
        else return data;
    });


    // When all data is loaded
    promiseQuartierData.then(quartierData => {
        promiseVars.then(vars => {

            // Map sources
            var accessToken = 'pk.eyJ1IjoibWFyYnJleCIsImEiOiJja2tpcnA5ZDgwbndkMnVrN2t4MWs4NjA5In0.SyweHd2yUjuEfmnQ_TFiDg';
            var mapboxUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + accessToken;

            // BASE Layers (Tile Layers or tiles)
            // base means that only one can be visible on the map at a time
            var dark = L.tileLayer(mapboxUrl, {
                id: 'mapbox/dark-v10'
            });

            var satellite = L.tileLayer(mapboxUrl, {
                id: 'mapbox/satellite-v9'
            });


            // Creation of an instance of the main class in Leaflet
            // 'mapid' is the ID of a tag HTML in which the map will be shown
            var map = L.map('mapid', {
                center: [45.763420, 4.834277],
                zoom: 13,
                minZoom: 11,
                maxZoom: 17,
                maxBounds: L.latLngBounds(L.latLng(45.842, 4.721), L.latLng(45.657, 5.011)), // user cannot drag the map outside these points (does not affect zoom)
                attributionControl: false, // to hide a "Leaflet" annotation at the right bottom corner
                layers: dark // array of Layers or only one Layer which will be added initially
            });

            // console.log(`Center: [${vars.mapCenterLat}, ${vars.mapCenterLng}]`);


            // Creation of a notice at bottom left corner of the map
            // that will contain a Name and an ID of the hovered region
            var info = L.control({position: 'bottomleft'});

            info.onAdd = function (map) {
                this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
                this.update();
                return this._div;
            };

            info.update = function (feature) {
                this._div.innerHTML = feature ?
                    '<b>' + feature.properties.name + ' (' + feature.id + ')</b><br />' + feature.properties.commune
                    : '';
            };

            info.addTo(map);


            // Creation of a notice at bottom right corner of the map
            // that will contain a history of moves for each player
            var legend = L.control({position: 'bottomright'});

            legend.onAdd = function (map) {
                var div = L.DomUtil.create('div', 'info legend');

                div.innerHTML += '<b>TOUR:</b> ' + vars.tourActuel + '<br>';

                if (vars.strategie == 'econome') {
                    div.innerHTML += '<b>Tickets Taxi:</b> ' + vars.nbTicketsTaxi[0] + '<br>';
                    div.innerHTML += '<b>Tickets Bus:</b> ' + vars.nbTicketsBus[0] + '<br>';
                    div.innerHTML += '<b>Tickets Metro:</b> ' + vars.nbTicketsMetro[0] + '<br>';
                }

                if (vars.tourActuel == 1) {
                    div.innerHTML += '<b>Player:</b> ' + vars.idQNextPlayer[0] + '<br>';
                } else {
                    div.innerHTML += '<b>Player:</b><br>';
                    div.innerHTML += vars.idQPosActuel[0] + ' -> ' + vars.idQNextPlayer[0] + '<br>';
                    div.innerHTML += vars.idQNextPlayer[1] + '<br>';

                    if (vars.strategie == "pistage" && vars.tourActuel > 2) {
                        div.innerHTML += "<b>Shortest path:</b><br>";
                        for (var j = 0; j < pathGamer.length; j++) {
                            if (j != pathGamer.length - 1)
                                div.innerHTML += pathGamer[j] + "-->";
                            else
                                div.innerHTML += pathGamer[j];
                        }
                        div.innerHTML += "<br>";
                    }
                }

                for (var i=0; i<vars.nbDetectives; ++i) {
                    if (vars.tourActuel == 1) {
                        div.innerHTML += '<b>Detective' + (i+1) + ':</b> ' + vars.idQNextDet[i][0] + '<br>';
                    } else {
                        div.innerHTML += '<b>Detective' + (i+1) + ':</b><br>';
                        div.innerHTML += vars.idQPosActuel[i+2] + ' -> ' + vars.idQNextDet[i][0] + '<br>';
                        div.innerHTML += vars.idQNextDet[i][1] + '<br>';
                    }
                }
                
                div.innerHTML += '<b>MisterX:</b><br>';
                //div.innerHTML += vars.idQPosActuel[0] + ' -> ' + vars.idQNextMX[0] + '<br>';
                div.innerHTML += vars.idQNextMX[1] + '<br>';

                return div;
            };

            legend.addTo(map);


            // ==================== Styling Functions ====================
            // Coloring function, based on a quarter
            function getFillColor(c) {
                return c == 'Lyon 1er' ? '#e7298a' :
                    c == 'Lyon 2e'  ? '#41ab5d' :
                    c == 'Lyon 3e'  ? '#1d91c0' :
                    c == 'Lyon 4e'  ? '#ec7014' :
                    c == 'Lyon 5e'  ? '#807dba' :
                    c == 'Lyon 6e'  ? '#ef3b2c' :
                    c == 'Lyon 7e'  ? '#8c6bb1' :
                    c == 'Lyon 8e'  ? '#006837' :
                    c == 'Lyon 9e'  ? '#3690c0' :
                                      '#feb24c';
            }

            // Contour coloring function, based on a quarter
            function getContourColor(id) {
                return id == vars.idQNextMX[0] ? 'red' : // quarter = quarter of Mister X
                       id == vars.idQNextPlayer[0] ? 'blue' : // quarter = quarter of Player
                       '#666'; // quarter = quarter of one of the Detectives
            }

            // Styling function for FREE Quarters
            function style(feature) {
                return {
                    fillColor: getFillColor(feature.properties.commune),
                    weight: 2,
                    opacity: 1,
                    color: 'white',
                    dashArray: '5',
                    fillOpacity: 0.4
                };
            }

            // Styling function for DETECTIVE Quarters
            function styleLayerPosActJoueurs(feature) {
                return {
                    weight: 4,
                    dashArray: '',
                    color: getContourColor(feature.id),
                    fillColor: getFillColor(feature.properties.commune),
                    opacity: 1,
                    fillOpacity: 0.5
                };
            }

            // Styling function for Quarters where Player can go
            function styleLayerQuartiersToGo(feature) {
                return {
                    fillColor: getFillColor(feature.properties.commune),
                    weight: 4,
                    opacity: 1,
                    color: 'lime',
                    dashArray: '',
                    fillOpacity: 0.5
                };
            }


            // ==================== Creating variables for different layers ====================
            var geojson;
            var layerPosActJoueurs;
            var layerQuartiersToGo;
            var layerPlayerMarkers;
            var layerMX;

            // ==================== Function that will be called on 'mouseover' event ====================
            function highlightFeature(e) {
                var layer = e.target;

                layer.setStyle({
                    weight: 5,
                    dashArray: '',
                    fillOpacity: 0.6
                });

                if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                    layer.bringToFront();
                }

                info.update(layer.feature);
            }

            // ==================== Functions that will be called on 'mouseout' event for ====================
            // FREE Quarters
            function resetHighlight(e) {
                var layer = e.target;

                geojson.resetStyle(layer);

                if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                    layer.bringToBack();
                }

                info.update();
            }

            // DETECTIVE Quarters
            function resetHighlightLayerPosActJoueurs(e) {
                layerPosActJoueurs.resetStyle(e.target);

                info.update();
            }

            // Quarters where Player can go
            function resetHighlightLayerQuartiersToGo(e) {
                var layer = e.target;

                if (layer.options.fillColor != 'lime') {
                    layerQuartiersToGo.resetStyle(layer);
                }

                info.update();
            }

            // Mister X Quarter
            function resetHighlightLayerMX(e) {
                layerMX.resetStyle(e.target);

                info.update();
            }

            // ==================== Functions for 'click' event ====================
            // Function that zooms on a quarter passed in parameters
            function zoomToFeature(e) { map.fitBounds(e.target.getBounds()); }

            // ONLY FOR Quarters where Player can go
            function onClickLayerQuartiersToGo(e) {
                var layer = e.target;

                // console.log(e);
                // console.log(e.latlng.lat);

                var transportChosen = '';

                var taxi = false;
                var bus = false;
                var metro = false;

                for (var i=0; i<vars.nbRoutesPlayer; ++i) {
                    if (layer.feature.id == vars.tabRoutesPlayer[i][0]) {
                        if (vars.tabRoutesPlayer[i][1] == 'Taxi')
                            taxi = true;
                        if (vars.tabRoutesPlayer[i][1] == 'Bus')
                            bus = true;
                        if (vars.tabRoutesPlayer[i][1] == 'Métro/tramway')
                            metro = true;
                    }
                }

                if (taxi && bus && metro) {
                    var content = '<div class="chooseTransport" id="taxi">Taxi</div><div class="chooseTransport" id="bus">Bus</div><div class="chooseTransport" id="metro">Métro</div>';
                } else if (taxi && bus && !metro) {
                    var content = '<div class="chooseTransport" id="taxi">Taxi</div><div class="chooseTransport" id="bus">Bus</div>';
                } else if (taxi && !bus && metro) {
                    var content = '<div class="chooseTransport" id="taxi">Taxi</div><div class="chooseTransport" id="metro">Métro</div>';
                } else if (!taxi && bus && metro) {
                    var content = '<div class="chooseTransport" id="bus">Bus</div><div class="chooseTransport" id="metro">Métro</div>';
                } else if (taxi && !bus && !metro) {
                    var content = '<div class="chooseTransport" id="taxi">Taxi</div>';
                } else if (!taxi && bus && !metro) {
                    var content = '<div class="chooseTransport" id="bus">Bus</div>';
                } else if (!taxi && !bus && metro) {
                    var content = '<div class="chooseTransport" id="metro">Métro</div>';
                }

                var popup = L.popup()
                .setLatLng(e.latlng)
                .setContent(content)
                .openOn(map);

                $('.chooseTransport').click(function(){
                    if (vars.strategie == 'econome') {
                        if (this.innerHTML == 'Métro') {
                            transportChosen = 'Métro/tramway';
                            vars.nbTicketsMetro[0]--;
                        }
                        else if (this.innerHTML == 'Taxi') {
                            transportChosen = 'Taxi';
                            vars.nbTicketsTaxi[0]--;
                        }
                        else {
                            if (this.innerHTML == 'Bus') {
                                transportChosen = 'Bus';
                                vars.nbTicketsBus[0]--;
                            }
                        }
                    } else {
                        if (this.innerHTML == 'Métro') {
                            transportChosen = 'Métro/tramway';
                        }
                        else {
                            transportChosen = this.innerHTML;
                        }
                    }

                    // Ajax
                    map.removeControl(info);
                    map.removeControl(legend);
                    map.remove();
                    drawMap(layer.feature.id, transportChosen);
                });
            }

            // ==================== Functions for 'onEachFeature' parameter ====================
            function onEachFeature(feature, layer) {
                layer.on({
                    mouseover: highlightFeature,
                    mouseout: resetHighlight
                });
            }

            function onEachFeatureLayerPosActJoueurs(feature, layer) {
                if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                    layer.bringToFront();
                }
                layer.on({
                    mouseover: highlightFeature,
                    mouseout: resetHighlightLayerPosActJoueurs
                });
            }

            function onEachFeatureLayerQuartiersToGo(feature, layer) {
                if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                    layer.bringToFront();
                }
                layer.on({
                    mouseover: highlightFeature,
                    mouseout: resetHighlightLayerQuartiersToGo,
                    click: onClickLayerQuartiersToGo
                });
            }

            function onEachFeatureLayerPlayerMarkers(feature, layer) {
                layer.on({
                    click: onClickLayerQuartiersToGo
                });
            }

            function onEachFeatureLayerMX(feature, layer) {
                if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                    layer.bringToFront();
                }
                layer.on({
                    mouseover: highlightFeature,
                    mouseout: resetHighlightLayerMX
                });
            }

            // ==================== Filter Functions ====================
            function filterPosActJoueurs(feature) {
                if (feature.id == vars.idQNextPlayer[0]) {
                    return true;
                }
                for (var i=0; i<vars.nbDetectives; ++i) {
                    if (feature.id == vars.idQNextDet[i][0]) {
                        return true;
                    }
                }
                
                return false;
            }

            function filterQuartiersToGo(feature) {
                for (var i=0; i<vars.nbRoutesPlayer; ++i) {
                    if (feature.id == vars.tabRoutesPlayer[i][0]) {
                        return true;
                    }
                }
                
                return false;
            }

            function filterMX(feature) {
                if (feature.id == vars.idQNextMX[0]) {
                    return true;
                }
                
                return false;
            }


            // ===== Adding routes and stations of public transport =====
            // let dataRoutesTram;
            // let layerRoutesTram;
            // let promiseRoutesTram = fetch("route_tram.geojson")
            //     .then(response => response.json())
            //     .then(data => dataRoutesTram = data)
            //     .then(() => {
            //         layerRoutesTram = L.geoJson(dataRoutesTram, {
            //             style: {color: 'purple'}
            //         }).addTo(map);
            //     });

            // let dataRoutesSubway;
            // let layerRoutesSubway;
            // let promiseRoutesSubway = fetch("route_subway.geojson")
            //     .then(response => response.json())
            //     .then(data => dataRoutesSubway = data)
            //     .then(() => {
            //         layerRoutesSubway = L.geoJson(dataRoutesSubway, {
            //             style: {color: 'red'}
            //         }).addTo(map);
            //     });

            // let dataAllStations;
            // let layerAllStations;
            // let promiseAllStations = fetch("all_stations.geojson")
            //     .then(response => response.json())
            //     .then(data => dataAllStations = data)
            //     .then(() => {
            //         layerAllStations = L.geoJson(dataAllStations);
            //         return Object.values(layerAllStations._layers);
            //     })
            //     .then((stationsArr) => {
            //         stationsArr.map((item) => {
            //             console.log(item.feature.geometry.coordinates);
            //             L.circle([item.feature.geometry.coordinates[1],item.feature.geometry.coordinates[0]], {radius: 50}).setStyle(()=>{color: black}).addTo(map);
            //         });
            //     });


            // ==================== Setting All Layers ====================
            geojson = L.geoJson(quartierData, {
                style: style,
                onEachFeature: onEachFeature
            }).addTo(map);

            layerPosActJoueurs = L.geoJson(quartierData, {
                style: styleLayerPosActJoueurs,
                onEachFeature: onEachFeatureLayerPosActJoueurs,
                filter: filterPosActJoueurs
            }).addTo(map);

            layerQuartiersToGo = L.geoJson(quartierData, {
                style: styleLayerQuartiersToGo,
                onEachFeature: onEachFeatureLayerQuartiersToGo,
                filter: filterQuartiersToGo
            }).addTo(map);

            layerMX = L.geoJson(quartierData, {
                style: styleLayerPosActJoueurs,
                onEachFeature: onEachFeatureLayerMX,
                filter: filterMX
            });

            // ==================== Displaying Mister X Quarter on specific rounds ====================
            switch (vars.tourActuel) {
                case 3:
                    layerMX.addTo(map);
                    break;
                case 8:
                    layerMX.addTo(map);
                    break;
                case 13:
                    layerMX.addTo(map);
                    break;
                case 18:
                    layerMX.addTo(map);
                    break;
                default:
                    layerMX.removeFrom(map);
            }

            // ==================== Additional Layers that can be turned on ====================
            var playerPos; // needed to save the Player position to display routes

            // Layer that contains Markers of all Detectives, including Player
            var layerPosJoueurs = L.layerGroup();
            layerPosActJoueurs.eachLayer(function(layer){
                var coords = layer.getBounds().getCenter();
                var pos = L.marker(coords);

                // saving player position
                if (layer.feature.id == vars.idQPosActuel[1]) playerPos = layer;

                layerPosJoueurs.addLayer(pos);
            });

            // Layer that contains Lines from Player's position to all Quarters where he can go
            var layerRoutesPlayer = L.layerGroup();
            layerQuartiersToGo.eachLayer(function(layer){
                var coords = [
                    playerPos.getBounds().getCenter(),
                    layer.getBounds().getCenter()
                ];
                var route = L.polyline(coords);

                layerRoutesPlayer.addLayer(route);
            });

            // ==================== Adding different layers ====================
            // Simple objects with key/value pairs.
            // Keys are what is shown in the Control panel,
            // and corresponding value is a reference to the layer.

            // Base means that only one can be visible on the map at a time.
            // Also called Tile layers or Tiles.
            var baseMaps = {
                'Dark': dark,
                'Satellite': satellite
            };

            var overlayMaps = {
                'Markers': layerPosJoueurs,
                'Routes': layerRoutesPlayer
            }

            L.control.layers(baseMaps, overlayMaps).addTo(map);

        }).catch(error => console.error(`promiseVars: ${error}`));

    });

}

drawMap();