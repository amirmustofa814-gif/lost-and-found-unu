<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Lost & Found') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Permanent+Marker&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            /* Font Utama Website */
            body { font-family: 'Poppins', sans-serif; }
            
            /* Font Khusus Judul Logo */
            .font-chalk { font-family: 'Permanent Marker', cursive; }
        </style>
    </head>
    
    {{-- Background Gradasi Halus & Text Abu Gelap --}}
    <body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-screen text-gray-800">
        
        <div class="min-h-screen">
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white/60 backdrop-blur-sm border-b border-gray-200/50 shadow-sm sticky top-16 z-40">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Cek jika ada Session SUCCESS
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#f0fdf4', // Hijau muda soft
                    iconColor: '#16a34a'   // Hijau tua
                });
            @endif

            // Cek jika ada Session ERROR
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Ups, Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444',
                    background: '#fef2f2', // Merah muda soft
                });
            @endif
        </script>
    </body>
</html>