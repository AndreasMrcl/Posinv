<!DOCTYPE html>
<html lang="en">

<head>
    <title>Set Location</title>
    @include('user.layout.head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 10px;
        }
    </style>

</head>

<body class="font-poppins bg-gray-50">
    <div class="w-full sm:max-w-sm mx-auto h-screen">
        <form action="{{ route('user-ongkir') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="sm:max-w-sm">
                <!-- NAVBAR -->
                <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                    <div class="p-4 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                        <div class="flex">
                            <a href="{{ route('user-home') }}">
                                <img src="{{ asset('/img/home.svg') }}" alt="Home">
                            </a>
                            <div class="mx-auto">
                                <h1 class="text-center text-xl font-extralight">Choose Serve</h1>
                            </div>
                        </div>
                        <hr>
                        <div class="flex justify-between mx-10">
                            <a href="{{ route('user-product') }}">
                                <div class="flex space-x-1">
                                    <div class="bg-black p-1 rounded-md">
                                        <h1 class="text-xs font-light text-white px-1">1</h1>
                                    </div>
                                    <div class="my-auto">
                                        <h1 class="text-sm font-light">Product</h1>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('user-cart') }}">
                                <div class="flex space-x-1">
                                    <div class="bg-black p-1 rounded-md">
                                        <h1 class="text-xs font-light text-white px-1">2</h1>
                                    </div>
                                    <div class="my-auto">
                                        <h1 class="text-sm font-light">Cart</h1>
                                    </div>
                                </div>
                            </a>
                            <div class="flex space-x-1">
                                <div class="bg-black p-1 rounded-md">
                                    <h1 class="text-xs font-light text-white px-1">3</h1>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-sm font-light">Payment</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-32"></div>

                <!-- BODY -->
                <div class="p-4 space-y-4">
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span>Cabang</h1>
                        <input class="border w-full rounded-xl p-2" type="text" name="cabang"
                            value="{{ $order->cabang }}" readonly required>
                    </div>
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Pilih Lokasi</h1>
                        <div class="space-y-2">
                            <div>
                                <input type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                    id="alamat" name="alamat" placeholder="Selected Location" required readonly />
                            </div>
                            <div id="map"></div>
                            <div class="">
                                <input type="text" id="searchLocation" placeholder="Search location"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full" />
                            </div>
                            <div class="flex gap-2">
                                <button type="button" id="searchBtn"
                                    class="bg-blue-500 text-white p-2 rounded-lg w-full">Search</button>
                                <button type="button" id="locateBtn"
                                    class="bg-green-500 text-white p-2 rounded-lg w-full">Use My Location</button>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Ongkos Kirim</h1>
                        <input class="border w-full rounded-xl p-2" type="text" id="ongkir_display" name="ongkir_display"
                            readonly required />
                        <input type="hidden" id="ongkir" name="ongkir">

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="h-20"></div>
                <div class="fixed bottom-4 sm:max-w-sm w-full z-50 p-2">
                    <div class="flex flex-col items-center justify-center">
                        <button class="w-3/4" type="submit">
                            <h1
                                class="bg-black bg-opacity-90 font-bold text-white w-full mx-auto text-base p-3 rounded-full text-center">
                                Payment >
                            </h1>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var cabangLat = -6.981670;
        var cabangLon = 110.454330;

        var map = L.map('map').setView([cabangLat, cabangLon], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var marker = L.marker([cabangLat, cabangLon], {
            draggable: true
        }).addTo(map);

        marker.on('dragend', function() {
            var latLng = marker.getLatLng();
            updateLocation(latLng.lat, latLng.lng);
            calculateDeliveryFee(latLng.lat, latLng.lng);
        });

        function updateLocation(lat, lon) {
            var url =
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        document.getElementById('alamat').value = data.display_name; // Set location name
                    } else {
                        document.getElementById('alamat').value = 'Location not found';
                    }
                })
                .catch(error => {
                    console.error('Error fetching location:', error);
                    document.getElementById('location').value = 'Error retrieving location';
                });
        }

        function calculateDeliveryFee(lat, lon) {
            var distance = getDistance(cabangLat, cabangLon, lat, lon);
            var fee = Math.round(distance * 5000);

            // Display currency for user
            document.getElementById('ongkir_display').value = fee.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            });

            // Store raw number for backend
            document.getElementById('ongkir').value = fee;
        }


        function getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the Earth in kilometers
            const dLat = toRadians(lat2 - lat1);
            const dLon = toRadians(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Distance in kilometers
        }

        function toRadians(degrees) {
            return degrees * (Math.PI / 180);
        }

        document.getElementById('searchBtn').onclick = function() {
            var searchInput = document.getElementById('searchLocation').value.trim();
            if (!searchInput) {
                alert('Please enter a location to search.');
                return;
            }

            var url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchInput)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var location = data[0];
                        var lat = parseFloat(location.lat);
                        var lon = parseFloat(location.lon);
                        map.setView([lat, lon], 15);
                        marker.setLatLng([lat, lon]);
                        updateLocation(lat, lon);
                        calculateDeliveryFee(lat, lon);
                    } else {
                        alert('Location not found. Please try a different query.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching location:', error);
                    alert('An error occurred while searching for the location.');
                });
        };

        document.getElementById('locateBtn').onclick = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        var lat = position.coords.latitude;
                        var lon = position.coords.longitude;
                        map.setView([lat, lon], 15);
                        marker.setLatLng([lat, lon]);
                        updateLocation(lat, lon);
                        calculateDeliveryFee(lat, lon);
                        document.getElementById('alamat').value = 'Current Location';
                    },
                    function(error) {
                        console.error('Geolocation error:', error);
                        alert('Unable to retrieve your location. Please try again.');
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        };
    </script>
</body>

</html>
