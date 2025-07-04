<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Document Tracker - DARFO5') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/notifications.js'])

        <script>
            // On page load or when changing themes, best to add inline in `head` to avoid FOUC
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark')
            }

            // Add user ID for notifications
            window.userId = {{ auth()->id() }};
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="ml-64">
                <!-- Global Header -->
                <x-header />

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $header }}
                                </div>
                                @isset($pageActions)
                                    <div class="mt-4 md:mt-0 flex items-center space-x-2">
                                        {{ $pageActions }}
                                    </div>
                                @endisset
                            </div>
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="bg-gray-100 dark:bg-gray-900 min-h-screen">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')

        <!-- Flash Messages -->
        @if(session('success'))
            <script>
                window.successMessage = "{{ session('success') }}";
            </script>
        @endif
        
        @if(session('error'))
            <script>
                window.errorMessage = "{{ session('error') }}";
            </script>
        @endif
        
        @if(session('info'))
            <script>
                window.infoMessage = "{{ session('info') }}";
            </script>
        @endif
        
        @if(session('warning'))
            <script>
                window.warningMessage = "{{ session('warning') }}";
            </script>
        @endif
    </body>
</html>
