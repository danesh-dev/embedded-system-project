<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Tracker - {{ $deviceId ?? 'Device Location' }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .search-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }

        .search-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .form-group {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group label {
            position: absolute;
            left: 20px;
            top: 15px;
            color: #666;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-group input:focus + label,
        .form-group input:not(:placeholder-shown) + label {
            top: -10px;
            left: 15px;
            font-size: 12px;
            background: white;
            padding: 0 5px;
            color: #667eea;
        }

        .search-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .location-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }

        .no-location {
            text-align: center;
            color: #666;
            font-size: 1.1rem;
        }

        .location-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0fe 100%);
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .info-item .label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .info-item .value {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
        }

        .map-section {
            margin-top: 30px;
        }

        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            height: 400px;
            position: relative;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .map-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .map-btn {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #667eea;
            color: #667eea;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .map-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .loading {
            text-align: center;
            color: #666;
            font-style: italic;
        }

        .error {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #c33;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .search-form {
                flex-direction: column;
            }

            .form-group {
                min-width: auto;
            }

            .map-container {
                height: 300px;
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-map-marker-alt"></i> Location Tracker</h1>
            <p>Track and visualize device locations in real-time</p>
        </div>

        <div class="search-section fade-in">
            <form class="search-form" method="GET" action="{{ route('location.show') }}">
                <div class="form-group">
                    <input type="text" name="deviceId" id="deviceId" placeholder=" " value="{{ $deviceId }}" required>
                    <label for="deviceId">Device ID</label>
                </div>
                <button type="submit" class="search-btn pulse">
                    <i class="fas fa-search"></i>
                    Track Location
                </button>
            </form>
        </div>

        @if($deviceId)
            <div class="location-card fade-in">
                @if($location)
                    <h2 style="margin-bottom: 20px; color: #333; text-align: center;">
                        <i class="fas fa-mobile-alt"></i> Device: {{ $location->deviceId }}
                    </h2>

                    <div class="location-info">
                        <div class="info-item">
                            <div class="label">Latitude</div>
                            <div class="value">{{ number_format($location->lat, 6) }}°</div>
                        </div>
                        <div class="info-item">
                            <div class="label">Longitude</div>
                            <div class="value">{{ number_format($location->long, 6) }}°</div>
                        </div>
                        <div class="info-item">
                            <div class="label">Last Updated</div>
                            <div class="value">{{ $location->date->format('M j, Y g:i A') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">Time Ago</div>
                            <div class="value">{{ $location->date->diffForHumans() }}</div>
                        </div>
                    </div>

                    <div class="map-section">
                        <div class="map-buttons">
                            <a href="https://www.google.com/maps?q={{ $location->lat }},{{ $location->long }}"
                               target="_blank" class="map-btn">
                                <i class="fab fa-google"></i>
                                Open in Google Maps
                            </a>
                            <a href="https://maps.apple.com/?q={{ $location->lat }},{{ $location->long }}"
                               target="_blank" class="map-btn">
                                <i class="fas fa-map"></i>
                                Open in Apple Maps
                            </a>
                            <a href="https://www.openstreetmap.org/?mlat={{ $location->lat }}&mlon={{ $location->long }}&zoom=15"
                               target="_blank" class="map-btn">
                                <i class="fas fa-globe"></i>
                                OpenStreetMap
                            </a>
                        </div>

                        {{-- <div class="map-container">
                            <iframe id="map"
                                src="https://www.google.com/maps/embed/v1/place?key=YOUR_GOOGLE_MAPS_API_KEY&q={{ $location->lat }},{{ $location->long }}&zoom=15&maptype=roadmap"
                                frameborder="0"
                                style="border:0;"
                                allowfullscreen=""
                                aria-hidden="false"
                                tabindex="0">
                            </iframe>
                        </div> --}}

                        <div style="text-align: center; margin-top: 15px; color: #666; font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i>
                            Coordinates: {{ $location->lat }}, {{ $location->long }}
                        </div>
                    </div>
                @else
                    <div class="no-location">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ffa500; margin-bottom: 20px;"></i>
                        <h3>No Location Found</h3>
                        <p>No location data found for device ID: <strong>{{ $deviceId }}</strong></p>
                        <p style="margin-top: 10px; color: #888;">Make sure the device has sent location data to the API.</p>
                    </div>
                @endif
            </div>
        @else
            <div class="location-card fade-in">
                <div class="no-location">
                    <i class="fas fa-search-location" style="font-size: 3rem; color: #667eea; margin-bottom: 20px;"></i>
                    <h3>Enter Device ID</h3>
                    <p>Enter a device ID above to view its latest location</p>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Auto-focus on device ID input if empty
        document.addEventListener('DOMContentLoaded', function() {
            const deviceIdInput = document.getElementById('deviceId');
            if (!deviceIdInput.value) {
                deviceIdInput.focus();
            }
        });

        // Add smooth scrolling to map section when location is found
        @if($location)
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelector('.map-section').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 500);
        });
        @endif

        // Handle form submission with loading state
        document.querySelector('.search-form').addEventListener('submit', function() {
            const button = this.querySelector('.search-btn');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            button.disabled = true;

            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 3000);
        });
    </script>
</body>
</html>
