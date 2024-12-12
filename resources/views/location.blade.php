<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Info | معلومات الموقع</title>
    <meta name="description" content="معلومات شاملة عن موقعك: الطقس، مواقيت الصلاة، التوقيت المحلي">
    <meta name="keywords" content="موقع, طقس, مواقيت الصلاة, توقيت محلي, location, weather, prayer times">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#3498db">
    <meta name="msapplication-TileColor" content="#3498db">
    <meta name="theme-color" content="#3498db">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Location Info | معلومات الموقع">
    <meta property="og:description" content="معلومات شاملة عن موقعك: الطقس، مواقيت الصلاة، التوقيت المحلي">
    <meta property="og:image" content="{{ asset('og-image.png') }}">
    
    <!-- تحميل مسبق -->
    <link rel="preload" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" as="script">
    <link rel="preload" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" as="style">
    
    <!-- الملفات الأساسية -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #000000;
            --accent-color: #3498db;
            --text-color: #000000;
            --card-bg: rgba(255, 255, 255, 0.8);
            --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }

        body {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            transition: background-image 0.5s ease-in-out;
            min-height: 100vh;
            position: relative;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            background: linear-gradient(135deg, #1c92d2, #f2fcfe);
            z-index: 1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        @media (min-width: 576px) {
            .container {
                padding: 0 20px;
            }
        }

        .location-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }

        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.25);
        }

        .card-body {
            padding: 20px;
        }

        @media (min-width: 768px) {
            .card-body {
                padding: 25px;
            }
        }

        .card-title {
            color: var(--primary-color);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 8px;
            display: inline-block;
        }

        @media (min-width: 768px) {
            .card-title {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
                padding-bottom: 10px;
            }
        }

        .h5 {
            color: #000000;
            font-weight: 600;
            font-size: 1.2rem;
            margin-top: 0.5rem;
        }

        @media (min-width: 768px) {
            .h5 {
                font-size: 1.8rem;
            }
        }

        .text-muted {
            font-size: 0.875rem;
        }

        @media (min-width: 768px) {
            .text-muted {
                font-size: 1rem;
            }
        }

        .border-end {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        @media (max-width: 767px) {
            .border-end {
                border-right: none !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
                margin-bottom: 15px;
                padding-bottom: 15px;
            }

            .col-md-4 {
                margin-bottom: 20px;
            }

            .col-md-4:last-child {
                margin-bottom: 0;
            }
        }

        #map {
            height: 300px;
            border-radius: 15px;
            margin-top: 10px;
        }

        @media (min-width: 768px) {
            #map {
                height: 400px;
            }
        }

        .forecast-scroll {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-color) rgba(255, 255, 255, 0.1);
            gap: 10px;
            padding: 15px 5px;
            margin: 0 -10px;
        }

        .forecast-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .forecast-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .forecast-scroll::-webkit-scrollbar-thumb {
            background-color: var(--accent-color);
            border-radius: 4px;
        }

        .forecast-scroll::-webkit-scrollbar-thumb:hover {
            background-color: var(--primary-color);
        }

        .forecast-hour {
            flex: 0 0 auto;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(8px);
            padding: 15px;
            border-radius: 12px;
            min-width: 120px;
            text-align: center;
            transition: transform 0.2s;
        }

        .forecast-hour:hover {
            transform: translateY(-5px);
        }

        .forecast-icon {
            width: 40px;
            height: 40px;
            margin: 8px auto;
        }

        @media (min-width: 768px) {
            .forecast-icon {
                width: 50px;
                height: 50px;
                margin: 10px auto;
            }
        }

        .prayer-time {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        @media (min-width: 768px) {
            .prayer-time {
                font-size: 1.3rem;
                margin-bottom: 1rem;
            }
        }

        .loading, .error {
            padding: 20px;
            border-radius: 15px;
            margin: 15px 0;
            font-size: 1rem;
        }

        @media (min-width: 768px) {
            .loading, .error {
                padding: 30px;
                font-size: 1.2rem;
            }
        }

        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            z-index: -2;
        }

        .background-overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                to bottom,
                rgba(0, 0, 0, 0.5),
                rgba(0, 0, 0, 0.3)
            );
            z-index: -1;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div id="loading" class="loading">
            جاري تحميل المعلومات...
        </div>
        <div id="error" class="error"></div>
        <div id="content" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-md-12 mb-4">
                    <div class="card location-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center border-end">
                                    <i class="fas fa-map-marker-alt mb-2" style="font-size: 2rem; color: var(--accent-color);"></i>
                                    <div class="mt-3">
                                        <div id="cityDisplay" class="h5 mb-2"></div>
                                        <div class="text-muted">المدينة</div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center border-end">
                                    <i class="fas fa-flag mb-2" style="font-size: 2rem; color: var(--accent-color);"></i>
                                    <div class="mt-3">
                                        <div id="countryDisplay" class="h5 mb-2"></div>
                                        <div id="regionDisplay" class="text-muted mb-1"></div>
                                        <div class="text-muted">الدولة والمنطقة</div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-globe mb-2" style="font-size: 2rem; color: var(--accent-color);"></i>
                                    <div class="mt-3">
                                        <div id="coordinates" class="h5 mb-2"></div>
                                        <div id="ip" class="text-muted mb-1"></div>
                                        <div class="text-muted">الموقع الجغرافي</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 mb-4">
                    <div class="card location-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center border-end">
                                    <i class="fas fa-calendar-alt mb-2" style="font-size: 2rem; color: var(--accent-color);"></i>
                                    <div class="mt-3">
                                        <div id="weekDay" class="text-muted mb-1" style="font-size: 1.1rem;"></div>
                                        <div id="dateDisplay" class="h5 mb-2"></div>
                                        <div class="text-muted">التاريخ الميلادي</div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center border-end">
                                    <i class="fas fa-clock mb-2" style="font-size: 2rem; color: var(--accent-color);"></i>
                                    <div class="mt-3">
                                        <div id="timeDisplay" class="h5 mb-2"></div>
                                        <div class="text-muted">الوقت المحلي</div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-moon mb-2" style="font-size: 2rem; color: var(--accent-color);"></i>
                                    <div class="mt-3">
                                        <div id="hijriDate" class="h5 mb-2"></div>
                                        <div class="text-muted">التاريخ الهجري</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 mb-4">
                    <div class="card location-card">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-4">مواقيت الصلاة</h5>
                            <div class="row text-center">
                                <div class="col-md-2 col-6 mb-3">
                                    <div class="prayer-time">
                                        <strong class="d-block mb-2">الفجر</strong>
                                        <span id="fajr" class="h5 text-primary">-</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-3">
                                    <div class="prayer-time">
                                        <strong class="d-block mb-2">الشروق</strong>
                                        <span id="sunrise" class="h5 text-primary">-</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-3">
                                    <div class="prayer-time">
                                        <strong class="d-block mb-2">الظهر</strong>
                                        <span id="dhuhr" class="h5 text-primary">-</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-3">
                                    <div class="prayer-time">
                                        <strong class="d-block mb-2">العصر</strong>
                                        <span id="asr" class="h5 text-primary">-</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-3">
                                    <div class="prayer-time">
                                        <strong class="d-block mb-2">المغرب</strong>
                                        <span id="maghrib" class="h5 text-primary">-</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-3">
                                    <div class="prayer-time">
                                        <strong class="d-block mb-2">العشاء</strong>
                                        <span id="isha" class="h5 text-primary">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small id="prayerSource" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 mb-4">
                    <div class="card location-card">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-4">حالة الطقس</h5>
                            <div class="row align-items-center">
                                <div class="col-md-6 text-center mb-3 mb-md-0">
                                    <img id="weatherIcon" src="" alt="حالة الطقس" style="width: 100px; height: 100px; display: none;">
                                    <h3 class="mt-2 mb-0">
                                        <span id="temperature" class="h2 text-primary">-</span>
                                        <span class="h4">°م</span>
                                    </h3>
                                    <p id="weatherDescription" class="text-muted mb-0">-</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="weather-detail">
                                                <strong class="d-block mb-2">الرطوبة</strong>
                                                <span id="humidity" class="h5 text-primary">-</span>
                                                <span class="small">%</span>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="weather-detail">
                                                <strong class="d-block mb-2">سرعة الرياح</strong>
                                                <span id="windSpeed" class="h5 text-primary">-</span>
                                                <span class="small">كم/س</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="weather-detail">
                                                <strong class="d-block mb-2">الضغط الجوي</strong>
                                                <span id="pressure" class="h5 text-primary">-</span>
                                                <span class="small">هـ.ب</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="weather-detail">
                                                <strong class="d-block mb-2">الرؤية</strong>
                                                <span id="visibility" class="h5 text-primary">-</span>
                                                <span class="small">كم</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <small id="weatherSource" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 mb-4">
                    <div class="card location-card">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-4">درجات الحرارة خلال 24 ساعة القادمة</h5>
                            <div class="forecast-scroll" style="overflow-x: auto; white-space: nowrap;">
                                <div id="hourlyForecast" class="d-flex" style="min-width: 100%;">
                                    <!-- سيتم ملء هذا القسم بالبيانات عبر JavaScript -->
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <small id="forecastSource" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card location-card">
                        <div class="card-body">
                            <h5 class="card-title text-primary">الخريطة</h5>
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map = null;
        let globalCurrentTime = new Date('2024-12-12T01:01:53+02:00');

        function updateDateTime() {
            // تحديث الوقت العالمي
            globalCurrentTime = new Date(globalCurrentTime.getTime() + 1000);
            
            // تحديث التاريخ الميلادي
            const dateOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('dateDisplay').textContent = new Intl.DateTimeFormat('ar-EG', dateOptions).format(globalCurrentTime);
            
            // تحديث يوم الأسبوع
            const weekDayOptions = {
                weekday: 'long'
            };
            document.getElementById('weekDay').textContent = new Intl.DateTimeFormat('ar-EG', weekDayOptions).format(globalCurrentTime);
            
            // تحديث الوقت
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            document.getElementById('timeDisplay').textContent = new Intl.DateTimeFormat('ar-EG', timeOptions).format(globalCurrentTime);
            
            // تحديث التاريخ الهجري
            const hijriOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                calendar: 'islamic'
            };
            document.getElementById('hijriDate').textContent = new Intl.DateTimeFormat('ar-SA-u-ca-islamic', hijriOptions).format(globalCurrentTime);
        }

        // تحديث الوقت كل ثانية
        updateDateTime(); // تحديث فوري عند التحميل
        const timeInterval = setInterval(updateDateTime, 1000);

        function loadMap(lat, lon) {
            console.log('Loading map for coordinates:', lat, lon);
            
            if (map) {
                map.remove();
            }
            
            try {
                map = L.map('map').setView([lat, lon], 13);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                
                L.marker([lat, lon]).addTo(map);
                
                // تحديث حجم الخريطة بعد التحميل
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
            } catch (error) {
                console.error('Error loading map:', error);
                document.getElementById('map').innerHTML = '<div class="alert alert-warning">عذراً، حدث خطأ في تحميل الخريطة</div>';
            }
        }

        // دالة تنسيق الوقت
        function formatTime(timeStr) {
            try {
                const date = new Date(`2024-01-01 ${timeStr}`);
                return date.toLocaleTimeString('ar-EG', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            } catch (e) {
                return timeStr;
            }
        }

        // دالة جلب مواقيت الصلاة من Aladhan API
        async function fetchPrayerTimesAladhan(latitude, longitude) {
            try {
                const response = await fetch(`https://api.aladhan.com/v1/timings/1701766800?latitude=${latitude}&longitude=${longitude}&method=5`);
                const data = await response.json();
                if (data.code === 200 && data.data && data.data.timings) {
                    document.getElementById('prayerSource').textContent = 'المصدر: Aladhan';
                    return data.data.timings;
                }
                throw new Error('Invalid data from Aladhan API');
            } catch (error) {
                console.error('Aladhan API error:', error);
                return null;
            }
        }

        // دالة تحديث مواقيت الصلاة في الواجهة
        function updatePrayerTimes(times) {
            const prayerElements = {
                'fajr': ['Fajr', 'Fajr'],
                'sunrise': ['Sunrise', 'Shorooq'],
                'dhuhr': ['Dhuhr', 'Zuhr'],
                'asr': ['Asr', 'Asr'],
                'maghrib': ['Maghrib', 'Maghrib'],
                'isha': ['Isha', 'Isha']
            };

            for (const [elementId, possibilities] of Object.entries(prayerElements)) {
                let time = null;
                for (const key of possibilities) {
                    if (times[key]) {
                        time = times[key];
                        break;
                    }
                }
                if (time) {
                    document.getElementById(elementId).textContent = formatTime(time);
                }
            }
        }

        // دالة جلب مواقيت الصلاة مع النسخ الاحتياطي
        async function fetchPrayerTimesWithFallback(latitude, longitude) {
            try {
                let prayerTimes = await fetchPrayerTimesAladhan(latitude, longitude);
                if (prayerTimes) return prayerTimes;

                throw new Error('Failed to fetch prayer times from all sources');
            } catch (error) {
                console.error('Prayer times fetch error:', error);
                return null;
            }
        }

        // دالة جلب بيانات الطقس من WeatherAPI
        async function fetchWeatherFromWeatherAPI(latitude, longitude) {
            try {
                const response = await fetch(`https://api.weatherapi.com/v1/current.json?key=76725eed85744f18903180939240912&q=${latitude},${longitude}&aqi=no`);
                const data = await response.json();
                if (data.current) {
                    document.getElementById('weatherSource').textContent = 'المصدر: WeatherAPI';
                    return {
                        temperature: data.current.temp_c,
                        description: data.current.condition.text,
                        humidity: data.current.humidity,
                        windSpeed: Math.round(data.current.wind_kph),
                        pressure: data.current.pressure_mb,
                        visibility: data.current.vis_km,
                        icon: data.current.condition.icon
                    };
                }
                throw new Error('Invalid data from WeatherAPI');
            } catch (error) {
                console.error('WeatherAPI error:', error);
                return null;
            }
        }

        // دالة تحديث بيانات الطقس في الواجهة
        function updateWeatherUI(weather) {
            document.getElementById('temperature').textContent = Math.round(weather.temperature);
            document.getElementById('weatherDescription').textContent = weather.description;
            document.getElementById('humidity').textContent = weather.humidity;
            document.getElementById('windSpeed').textContent = weather.windSpeed;
            document.getElementById('pressure').textContent = weather.pressure;
            document.getElementById('visibility').textContent = weather.visibility;
            
            const weatherIcon = document.getElementById('weatherIcon');
            weatherIcon.src = weather.icon;
            weatherIcon.style.display = 'inline-block';
        }

        // دالة جلب بيانات الطقس مع النسخ الاحتياطي
        async function fetchWeatherWithFallback(latitude, longitude) {
            try {
                let weatherData = await fetchWeatherFromWeatherAPI(latitude, longitude);
                if (weatherData) return weatherData;

                throw new Error('Failed to fetch weather data from all sources');
            } catch (error) {
                console.error('Weather fetch error:', error);
                return null;
            }
        }

        // دالة جلب توقعات الطقس من WeatherAPI
        async function fetchHourlyForecast(latitude, longitude) {
            try {
                const response = await fetch(`https://api.weatherapi.com/v1/forecast.json?key=76725eed85744f18903180939240912&q=${latitude},${longitude}&days=2&hours=24&aqi=no`);
                const data = await response.json();
                
                if (data.forecast && data.forecast.forecastday) {
                    // جمع ساعات اليوم الحالي والغد
                    let hourlyData = [];
                    data.forecast.forecastday.forEach(day => {
                        if (day.hour) {
                            hourlyData = hourlyData.concat(day.hour);
                        }
                    });
                    updateHourlyForecast(hourlyData);
                }
            } catch (error) {
                console.error('Error fetching hourly forecast:', error);
            }
        }

        // دالة تحديث توقعات درجات الحرارة في الواجهة
        function updateHourlyForecast(hourlyData) {
            const forecastContainer = document.getElementById('hourlyForecast');
            forecastContainer.innerHTML = '';

            // الحصول على الساعة الحالية
            const currentDate = new Date('2024-12-12T01:04:16+02:00');
            const currentHour = currentDate.getHours();
            
            // تحديد الساعة التالية
            const nextHour = (currentHour + 1) % 24;
            
            // تصفية وترتيب البيانات للـ 24 ساعة القادمة
            const next24Hours = hourlyData.filter(hour => {
                const hourDate = new Date(hour.time);
                const hourTime = hourDate.getHours();
                const isAfterCurrent = hourDate > currentDate;
                return isAfterCurrent;
            }).slice(0, 24);

            // إنشاء كروت الساعات
            next24Hours.forEach(hour => {
                const hourTime = new Date(hour.time);
                const timeString = new Intl.DateTimeFormat('ar-EG', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }).format(hourTime);

                const forecastHour = document.createElement('div');
                forecastHour.className = 'forecast-hour';
                forecastHour.innerHTML = `
                    <div class="text-muted">${timeString}</div>
                    <img src="${hour.condition.icon}" alt="${hour.condition.text}" class="forecast-icon">
                    <div class="forecast-temp">${Math.round(hour.temp_c)}°م</div>
                    <div class="text-muted small">${hour.condition.text}</div>
                `;
                forecastContainer.appendChild(forecastHour);
            });

            // تحديث تنسيق شريط التمرير
            const scrollContainer = document.querySelector('.forecast-scroll');
            if (scrollContainer) {
                scrollContainer.style.overflowX = 'auto';
                scrollContainer.style.display = 'flex';
                scrollContainer.style.gap = '10px';
                scrollContainer.style.padding = '15px 5px';
                scrollContainer.style.marginBottom = '20px';
            }
        }

        // دالة تحديث خلفية الصفحة
        async function updateBackground(cityName) {
            try {
                // تحديد ما إذا كان الوقت نهاراً أو ليلاً
                const currentHour = globalCurrentTime.getHours();
                const isDaytime = currentHour >= 6 && currentHour < 18;
                const timeOfDay = isDaytime ? 'نهارا' : 'ليلا';
                const searchQuery = `${cityName} ${timeOfDay}`;

                const headers = {
                    'Authorization': 'Bh4nXzIxjddxai34Xps1uzgo5dXANgfRNd1OY6Uc63o5JjZllhY1Vxjx'
                };
                const response = await fetch(
                    `https://api.pexels.com/v1/search?query=${searchQuery}+city+landscape&orientation=landscape&per_page=15`,
                    { headers }
                );
                const data = await response.json();
                
                if (data.photos && data.photos.length > 0) {
                    const randomIndex = Math.floor(Math.random() * Math.min(15, data.photos.length));
                    const imageUrl = data.photos[randomIndex].src.large2x;
                    
                    let backgroundOverlay = document.querySelector('.background-overlay');
                    if (!backgroundOverlay) {
                        backgroundOverlay = document.createElement('div');
                        backgroundOverlay.className = 'background-overlay';
                        document.body.insertBefore(backgroundOverlay, document.body.firstChild);
                    }

                    const img = new Image();
                    img.onload = function() {
                        backgroundOverlay.style.backgroundImage = `url(${imageUrl})`;
                        backgroundOverlay.style.opacity = '1';
                    };
                    img.onerror = function() {
                        console.error('Failed to load image from Pexels, trying Pixabay...');
                        tryPixabayBackground(cityName, isDaytime);
                    };
                    img.src = imageUrl;
                } else {
                    tryPixabayBackground(cityName, isDaytime);
                }
            } catch (error) {
                console.error('Error with Pexels API:', error);
                tryPixabayBackground(cityName, isDaytime);
            }
        }

        async function tryPixabayBackground(cityName, isDaytime) {
            try {
                const timeOfDay = isDaytime ? 'نهارا' : 'ليلا';
                const searchQuery = `${cityName} ${timeOfDay}`;
                
                const response = await fetch(
                    `https://pixabay.com/api/?key=47549478-7394855896b9f3e4bb41186b0&q=${searchQuery}+city+landscape&image_type=photo&orientation=horizontal&per_page=15`
                );
                const data = await response.json();
                
                if (data.hits && data.hits.length > 0) {
                    const randomIndex = Math.floor(Math.random() * Math.min(15, data.hits.length));
                    const imageUrl = data.hits[randomIndex].largeImageURL;
                    
                    let backgroundOverlay = document.querySelector('.background-overlay');
                    if (!backgroundOverlay) {
                        backgroundOverlay = document.createElement('div');
                        backgroundOverlay.className = 'background-overlay';
                        document.body.insertBefore(backgroundOverlay, document.body.firstChild);
                    }

                    const img = new Image();
                    img.onload = function() {
                        backgroundOverlay.style.backgroundImage = `url(${imageUrl})`;
                        backgroundOverlay.style.opacity = '1';
                    };
                    img.src = imageUrl;
                }
            } catch (error) {
                console.error('Error with Pixabay API:', error);
            }
        }

        // دالة رئيسية لتحميل البيانات
        async function init() {
            try {
                document.getElementById('loading').style.display = 'block';
                document.getElementById('content').style.display = 'none';
                document.getElementById('error').style.display = 'none';

                const response = await fetch('/get-location');
                const data = await response.json();

                if (data.city) {
                    updateBackground(data.city);
                }

                document.getElementById('cityDisplay').textContent = data.city || 'غير معروف';
                document.getElementById('countryDisplay').textContent = data.country || 'غير معروف';
                document.getElementById('regionDisplay').textContent = data.region || 'غير معروف';
                document.getElementById('coordinates').textContent = `${data.latitude || 'غير معروف'}, ${data.longitude || 'غير معروف'}`;
                document.getElementById('ip').textContent = data.ip || 'غير معروف';

                if (data.latitude && data.longitude) {
                    // تحميل مواقيت الصلاة
                    fetchPrayerTimesWithFallback(data.latitude, data.longitude)
                        .then(prayerTimes => {
                            if (prayerTimes) {
                                updatePrayerTimes(prayerTimes);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching prayer times:', error);
                        });

                    // تحميل بيانات الطقس
                    fetchWeatherWithFallback(data.latitude, data.longitude)
                        .then(weatherData => {
                            if (weatherData) {
                                updateWeatherUI(weatherData);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching weather:', error);
                        });

                    // تحميل توقعات الطقس للساعات القادمة
                    fetchHourlyForecast(data.latitude, data.longitude);

                    // تحميل الخريطة
                    loadMap(data.latitude, data.longitude);
                }

                document.getElementById('loading').style.display = 'none';
                document.getElementById('content').style.display = 'block';
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('error').textContent = 'حدث خطأ في تحميل البيانات';
                document.getElementById('error').style.display = 'block';
                document.getElementById('loading').style.display = 'none';
            }
        }

        // تشغيل الدالة الرئيسية عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
