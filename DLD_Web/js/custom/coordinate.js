const coordinateButton = document.getElementById("buttonGetCoordinates");
const closeCoordinateModalButton = document.getElementById("closeCoordinateModal");
const grabCoordinatesButton = document.getElementById("grabCoordinates");
const inputTextCoordinates = document.getElementById("txtCoordinates");
const accuracyTag = document.getElementById("accuracyTag"); 
const spinner = document.getElementById("spinner");
const icon = document.getElementById("icon");

let id, map, marker, lat, lon;

coordinateButton.addEventListener("click", () => {

    spinner.classList.remove("d-none");
    icon.classList.add("d-none");

    if ("geolocation" in navigator) {
        id = navigator.geolocation.watchPosition(
            (gps) => {
                
                if (gps.coords.accuracy <= 50) { // Accuracy must be lower or equal to 50 meters to show the map
                    lat = gps.coords.latitude;
                    lon = gps.coords.longitude;
                    accuracy = gps.coords.accuracy.toFixed(0);

                    let zoom = map ? map.getZoom() : 17;

                    if (!map) {
                        map = L.map('map').setView([lat, lon], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        marker = L.marker([lat, lon]).addTo(map)
                            .bindPopup('Você está aqui')
                    } else {
                        map.setView([lat, lon], zoom);
                        marker.setLatLng([lat, lon]);
                    }

                    $('#locationModal').on('shown.bs.modal', function () {
                        map.invalidateSize();
                        map.setView([lat, lon], zoom);
                    });

                    document.getElementById('mapText').classList.add("d-none");

                    //Show accuracy with emoji and disable grabCoordinatesButton in case of low accuracy
                    let pinTag
                    switch (true) {
                        case accuracy > 50:
                            pinTag = "🔴";
                            grabCoordinatesButton.disabled = true;
                        break;

                        case accuracy <= 50 && accuracy > 16:
                            pinTag = "🟠";
                            grabCoordinatesButton.disabled = true;
                        break;

                        case accuracy <= 16:
                            pinTag = "🟢";
                            grabCoordinatesButton.disabled = false;
                        break;

                        default:
                            pinTag = "⌛";
                            grabCoordinatesButton.disabled = true;
                    }

                    accuracyTag.innerText = "Precisão: " + pinTag;
                } else {
                    document.getElementById('mapText').classList.remove("d-none");
                    document.getElementById('mapText').innerText = "Precisão muito baixa. Coordenadas não disponíveis.";
                }

                spinner.classList.add("d-none");
                icon.classList.remove("d-none");

                $('#locationModal').modal('show');
            },
            (error) => {
                console.error("Erro ao obter a geolocalização:", error);
                document.getElementById('mapText').innerText = "Erro ao obter a geolocalização.";

                spinner.classList.add("d-none");
                icon.classList.remove("d-none");
                $('#locationModal').modal('show');
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    } else {
        console.error("Geolocalização não disponível nesse dispositivo.");
    }
});

grabCoordinatesButton.addEventListener("click", () => {
    if (lat && lon) {
        let txtCoord = lat + ", " + lon;

        inputTextCoordinates.value = txtCoord;
    }

    navigator.geolocation.clearWatch(id);
    $('#locationModal').modal('hide');
});

closeCoordinateModalButton.addEventListener("click", () => {
    navigator.geolocation.clearWatch(id);
});