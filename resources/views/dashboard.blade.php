<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
            @if(auth()->user()->department)
                - {{ auth()->user()->department->name }}
            @endif
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Documents Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Total Documents
                                </p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $stats['total_documents'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Incoming Documents Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Incoming Documents
                                </p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $stats['incoming_documents'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outgoing Documents Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Outgoing Documents
                                </p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $stats['outgoing_documents'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Documents Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Pending Documents
                                </p>
                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $stats['pending_documents'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="font-bold text-gray-900 dark:text-white">Assigned to Me</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['my_assigned_documents'] ?? 0 }}</div>
                </div>
                @if(auth()->user()->isSuperadmin() || auth()->user()->isAdmin())
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="font-bold text-gray-900 dark:text-white">Total Departments</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['departments'] ?? 0 }}</div>
                </div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">Recent Documents</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Tracking #</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Type</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($recentDocuments as $document)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $document->tracking_number }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $document->title }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full text-white" style="background-color: {{ $document->status->color }}">{{ $document->status->name }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $document->department->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $document->documentType->name }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-gray-500 dark:text-gray-400 py-8">No recent documents.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

    <link rel="stylesheet" href="https://unpkg.com/flowbite@latest/dist/flowbite.min.css" />
