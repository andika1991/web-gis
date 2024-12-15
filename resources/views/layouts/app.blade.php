<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Geografis Tempat Ibadah di Kecamatan Labuhan Ratu')</title>
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Poppins:wght@300;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Leaflet & Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <style>
        /* Global Style */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f9f9f9;
        }

        /* Header Styling */
        header {
            background-color: #4a4a4a;
            color: white;
            padding: 30px 20px;
            text-align: center;
            animation: fadeInDown 1s ease-out;
        }

        header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        header p {
            margin: 10px 0 0;
            font-size: 1.2rem;
            font-weight: 300;
        }

        /* Add subtle animation */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Main Content Styling */
        main {
            flex: 1;
            padding: 20px;
            animation: fadeIn 1s ease-in;
        }

        main h1 {
            font-size: 1.8rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 20px;
        }

        #map {
            width: 100%;
            height: 500px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Footer Styling */
        footer {
            background-color: #4a4a4a;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 0.9rem;
        }

        /* Animasi untuk elemen muncul */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Subtle glow effect for places of worship */
        .place-of-worship {
            animation: subtleGlow 3s ease-out infinite;
        }

        /* Soft, subtle glow effect */
        @keyframes subtleGlow {
            0% {
                box-shadow: 0 0 5px rgba(255, 200, 0, 0.8);
            }
            50% {
                box-shadow: 0 0 15px rgba(255, 200, 0, 1);
            }
            100% {
                box-shadow: 0 0 5px rgba(255, 200, 0, 0.8);
            }
        }

        /* Custom Map Markers Style for Places of Worship */
        .map-icon {
            font-size: 24px;
            color: #ffb800;
            transition: transform 0.3s ease-in-out;
        }

        .map-icon:hover {
            transform: scale(1.3);
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }

            header p {
                font-size: 1rem;
            }

            main h1 {
                font-size: 1.5rem;
            }

            #map {
                height: 400px;
            }

            footer {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            header h1 {
                font-size: 1.8rem;
            }

            header p {
                font-size: 0.9rem;
            }

            main h1 {
                font-size: 1.3rem;
            }

            #map {
                height: 350px;
            }

            footer {
                font-size: 0.75rem;
            }
        }
        /* Global Style */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background-color: #f9f9f9;
}

/* Responsive Table */
table {
    width: 100%;
    overflow-x: auto;
    margin-bottom: 20px;
}

/* Make table headers and rows adjust for small screens */
@media (max-width: 768px) {
    table thead th {
        font-size: 0.8rem;
    }
    table tbody td {
        font-size: 0.8rem;
    }
}

/* Responsive form and buttons */
.input-group {
    width: 100%;
    margin-bottom: 20px;
}

.input-group input {
    width: 70%; /* Takes up most of the space */
    margin-right: 10px;
}

.input-group button {
    width: 30%; /* Adjust the button size */
}

/* Mobile-First Adjustment for Buttons */
@media (max-width: 576px) {
    .input-group input {
        width: 60%; /* Input field takes up most of the screen */
    }

    .input-group button {
        width: 35%; /* Adjust button size for small screens */
    }

    .btn {
        width: 100%; /* Ensure buttons take full width on small screens */
        margin-top: 10px;
    }

    .table td, .table th {
        font-size: 0.9rem;
        padding: 8px;
    }
}

/* Responsive Map */
#map {
    width: 100%;
    height: 500px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    #map {
        height: 300px; /* Reduce map height on small screens */
    }
}

/* Footer Styling */
footer {
    background-color: #4a4a4a;
    color: white;
    text-align: center;
    padding: 15px 0;
    font-size: 0.9rem;
}

/* Header Styling */
header {
    background-color: #4a4a4a;
    color: white;
    padding: 30px 20px;
    text-align: center;
}

header h1 {
    font-size: 2.5rem;
    font-weight: 600;
    letter-spacing: 1px;
}

header p {
    margin-top: 10px;
    font-size: 1.2rem;
    font-weight: 300;
}

/* Media Queries for Header */
@media (max-width: 576px) {
    header h1 {
        font-size: 1.8rem;
    }

    header p {
        font-size: 1rem;
    }
}

    </style>
</head>
<body>
    <!-- Professional Header -->
    <header>
        <h1>Sistem Informasi Geografis Tempat Ibadah di Kecamatan Labuhan Ratu</h1>
        <p>Visualisasi dan Pengelolaan Data Tempat Ibadah dengan Efisien</p>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Sistem Informasi Geografis Tempat Ibadah di Kecamatan Labuhan Ratu. All rights reserved.</p>
    </footer>

</body>
</html>
