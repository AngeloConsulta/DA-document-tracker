<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Document QR Code') }} - {{ $document->tracking_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- QR Code Display -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Document QR Code
                            </h3>
                            
                            <div class="bg-white p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 inline-block">
                                <img src="{{ $qrCodeUrl }}" 
                                     alt="Document QR Code" 
                                     class="w-64 h-64 object-contain">
                            </div>
                            
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Tracking Number:</strong> {{ $document->tracking_number }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Title:</strong> {{ $document->title }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Type:</strong> {{ $document->documentType->name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Department:</strong> {{ $document->department->name }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                QR Code Actions
                            </h3>
                            
                            <div class="space-y-3">
                                <a href="{{ route('qr-codes.download', $document) }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download QR Code
                                </a>

                                <button onclick="generatePrintQR()" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    Generate Print QR Code
                                </button>

                                <a href="{{ route('documents.show', $document) }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Document Details
                                </a>
                            </div>

                            <!-- Print QR Code Preview -->
                            <div id="print-qr-preview" class="hidden space-y-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100">
                                    Print QR Code (Larger Size)
                                </h4>
                                <div id="print-qr-image" class="bg-white p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 inline-block">
                                    <!-- Print QR code will be inserted here -->
                                </div>
                                <a id="print-qr-download" 
                                   href="#" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download Print QR Code
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function generatePrintQR() {
            const button = event.target;
            const originalText = button.innerHTML;
            
            button.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generating...';
            button.disabled = true;

            fetch('{{ route("qr-codes.print", $document) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const preview = document.getElementById('print-qr-preview');
                    const image = document.getElementById('print-qr-image');
                    const downloadLink = document.getElementById('print-qr-download');
                    
                    image.innerHTML = `<img src="${data.download_url}" alt="Print QR Code" class="w-80 h-80 object-contain">`;
                    downloadLink.href = data.download_url;
                    downloadLink.download = `document-{{ $document->tracking_number }}-print-qr.png`;
                    
                    preview.classList.remove('hidden');
                    preview.scrollIntoView({ behavior: 'smooth' });
                    
                    showSuccess('Print QR code generated successfully!');
                } else {
                    showError(data.message || 'Failed to generate print QR code');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Failed to generate print QR code');
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }

        function showSuccess(message) {
            // You can implement your own success notification here
            alert(message);
        }

        function showError(message) {
            // You can implement your own error notification here
            alert('Error: ' + message);
        }
    </script>
    @endpush
</x-app-layout> 