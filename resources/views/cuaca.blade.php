<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cuaca Real-Time</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --background-color: #ecf0f1;
            --card-background: #ffffff;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header i {
            margin-right: 10px;
            animation: spin 10s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        .card {
            background: var(--card-background);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
            border: none;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem;
            font-weight: 600;
        }

        .temperature-box {
            background: linear-gradient(135deg, #f6d365, #fda085);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            color: white;
            text-align: center;
        }

        .temperature-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }

        .data-label {
            color: var(--primary-color);
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .data-value {
            color: var(--secondary-color);
            font-weight: 500;
        }

        .record-list {
            list-style: none;
            padding: 0;
        }

        .record-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--secondary-color);
        }

        .record-item p {
            margin: 0.5rem 0;
        }

        .month-year-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0;
            list-style: none;
        }

        .month-year-item {
            background: var(--secondary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .loading {
            text-align: center;
            padding: 2rem;
        }

        .loading i {
            color: var(--secondary-color);
            font-size: 2rem;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="header">
        <i class="fas fa-cloud-sun"></i>
        Dashboard Cuaca Real-Time
    </div>
    
    <div class="container">
        <div class="row">
            <!-- Summary Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-thermometer-half me-2"></i>
                        Ringkasan Suhu
                    </div>
                    <div class="card-body">
                        <div class="temperature-box">
                            <p class="temperature-value"><span id="suhuMax">-</span>°C</p>
                            <p class="mb-0">Suhu Maksimum</p>
                        </div>
                        <div class="mt-3">
                            <p><i class="fas fa-temperature-low me-2"></i><span class="data-label">Suhu Minimum:</span> 
                                <span class="data-value" id="suhuMin">-</span>°C</p>
                            <p><i class="fas fa-equals me-2"></i><span class="data-label">Suhu Rata-rata:</span> 
                                <span class="data-value" id="suhuRata">-</span>°C</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Detail Card -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i>
                        Data Suhu & Kelembaban Maksimum
                    </div>
                    <div class="card-body">
                        <div class="record-list" id="nilaiSuhuHumidMax">
                            <div class="loading">
                                <i class="fas fa-spinner"></i>
                                <p>Memuat data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Rekor Bulanan
                    </div>
                    <div class="card-body">
                        <div class="month-year-list" id="monthYearMax"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatTimestamp(timestamp) {
            return new Date(timestamp).toLocaleString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        fetch('http://127.0.0.1:8000/apicuaca')
            .then(response => response.json())
            .then(data => {
                document.getElementById('suhuMax').textContent = data.suhumax || '-';
                document.getElementById('suhuMin').textContent = data.suhumin || '-';
                document.getElementById('suhuRata').textContent = data.suhurata || '-';

                const nilaiSuhuHumidMax = document.getElementById('nilaiSuhuHumidMax');
                nilaiSuhuHumidMax.innerHTML = data.nilai_suhu_max_humid_max.map(item => `
                    <div class="record-item">
                        <div class="row">
                            <div class="col-md-6">
                                <p><i class="fas fa-fingerprint me-2"></i><span class="data-label">ID:</span> 
                                    <span class="data-value">${item.idx || '-'}</span></p>
                                <p><i class="fas fa-thermometer-full me-2"></i><span class="data-label">Suhu:</span> 
                                    <span class="data-value">${item.suhun || '-'}°C</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><i class="fas fa-tint me-2"></i><span class="data-label">Kelembaban:</span> 
                                    <span class="data-value">${item.humid || '-'}%</span></p>
                                <p><i class="fas fa-sun me-2"></i><span class="data-label">Kecerahan:</span> 
                                    <span class="data-value">${item.kecerahan || '-'} lux</span></p>
                            </div>
                        </div>
                        <p class="mt-2 mb-0">
                            <i class="fas fa-clock me-2"></i><span class="data-label">Waktu:</span> 
                            <span class="data-value">${formatTimestamp(item.timestamp)}</span>
                        </p>
                    </div>
                `).join('');

                const monthYearMax = document.getElementById('monthYearMax');
                monthYearMax.innerHTML = data.month_year_max.map(item => `
                    <li class="month-year-item">
                        <i class="fas fa-calendar-day me-2"></i>${item.month_year}
                    </li>
                `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('nilaiSuhuHumidMax').innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Gagal memuat data. Silakan coba lagi nanti.
                    </div>
                `;
            });
    </script>
</body>
</html>