<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Document Scanner') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-[600px] flex items-center justify-center">
                <div class="w-full">
                    <div class="flex flex-col items-center justify-center h-full" x-data="qrScanner()" x-init="initScanner">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 text-center">
                            Scan Document QR Code
                        </h3>
                        
                        <!-- Scanner Container -->
                        <div class="relative w-full max-w-xs aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg mx-auto overflow-hidden scanner-container">
                            <div id="qr-reader" class="w-full h-full"></div>
                            
                            <!-- Scanning Overlay -->
                            <div class="absolute inset-0 pointer-events-none">
                                <!-- Corner Indicators -->
                                <div class="absolute top-4 left-4 w-8 h-8 border-l-4 border-t-4 border-green-500 corner-indicator"></div>
                                <div class="absolute top-4 right-4 w-8 h-8 border-r-4 border-t-4 border-green-500 corner-indicator"></div>
                                <div class="absolute bottom-4 left-4 w-8 h-8 border-l-4 border-b-4 border-green-500 corner-indicator"></div>
                                <div class="absolute bottom-4 right-4 w-8 h-8 border-r-4 border-b-4 border-green-500 corner-indicator"></div>
                                
                                <!-- Scanning Line Animation -->
                                <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-green-500 scan-line"></div>
                                
                                <!-- Center Target -->
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 border-2 border-green-500 rounded-lg opacity-50 target-frame"></div>
                            </div>
                            
                            <!-- Status Messages -->
                            <div x-show="scanningStatus" x-text="scanningStatus" 
                                 class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-75 text-white px-3 py-1 rounded text-sm camera-permission">
                            </div>
                        </div>
                        
                        <!-- Camera Controls -->
                        <div class="mt-4 flex gap-2">
                            <button @click="switchCamera" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                Switch Camera
                            </button>
                            <button @click="restartScanner" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                                Restart Scanner
                            </button>
                        </div>
                        
                        <!-- Error Messages -->
                        <div x-show="errorMessage" x-text="errorMessage" 
                             class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('qrScanner', () => ({
                scanner: null,
                currentDocument: null,
                showDetails: false,
                scanningStatus: 'Initializing camera...',
                errorMessage: '',
                isScanning: false,
                currentCamera: 'environment',
                formData: {
                    status_id: '',
                    remarks: ''
                },

                async initScanner() {
                    try {
                        this.scanningStatus = 'Initializing camera...';
                        this.errorMessage = '';
                        
                        this.scanner = new Html5Qrcode("qr-reader");
                        
                        // Enhanced configuration for automatic scanning
                        const config = {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0,
                            disableFlip: false,
                            experimentalFeatures: {
                                useBarCodeDetectorIfSupported: true
                            }
                        };

                        // Try to start with environment camera first
                        await this.startCamera(this.currentCamera, config);
                        
                    } catch (error) {
                        console.error('Scanner initialization error:', error);
                        this.handleCameraError(error);
                    }
                },

                async startCamera(facingMode, config) {
                    try {
                        this.scanningStatus = 'Starting camera...';
                        
                        await this.scanner.start(
                            { facingMode: facingMode },
                            config,
                            this.handleScanSuccess.bind(this),
                            this.handleScanError.bind(this)
                        );
                        
                        this.isScanning = true;
                        this.scanningStatus = 'Scanning for QR codes...';
                        
                    } catch (error) {
                        console.error('Camera start error:', error);
                        this.handleCameraError(error);
                    }
                },

                async handleScanSuccess(decodedText, decodedResult) {
                    if (this.isScanning) {
                        this.isScanning = false;
                        this.scanningStatus = 'QR Code detected! Processing...';
                        
                        try {
                            // Add a small delay to show the success state
                            await new Promise(resolve => setTimeout(resolve, 500));
                            
                            const response = await fetch('{{ route("scanner.scan") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ qr_code: decodedText })
                            });

                            const data = await response.json();
                            
                            if (data.success) {
                                this.currentDocument = data.document;
                                this.showDetails = true;
                                this.scanningStatus = 'Document found!';
                                
                                // Show success feedback
                                this.showSuccessFeedback();
                                
                            } else {
                                this.scanningStatus = 'Document not found';
                                this.errorMessage = 'Document not found in the system';
                                this.restartScannerAfterDelay(2000);
                            }
                        } catch (error) {
                            console.error('Error processing scan:', error);
                            this.errorMessage = 'Error processing document';
                            this.restartScannerAfterDelay(2000);
                        }
                    }
                },

                handleScanError(error) {
                    // Only log errors, don't stop scanning for common errors
                    if (error.name !== 'NotFoundException') {
                        console.warn(`QR Code scan error: ${error}`);
                    }
                },

                handleCameraError(error) {
                    console.error('Camera error:', error);
                    
                    if (error.name === 'NotAllowedError') {
                        this.errorMessage = 'Camera access denied. Please allow camera permissions.';
                    } else if (error.name === 'NotFoundError') {
                        this.errorMessage = 'No camera found on this device.';
                    } else if (error.name === 'NotSupportedError') {
                        this.errorMessage = 'Camera not supported on this device.';
                    } else {
                        this.errorMessage = 'Camera initialization failed. Please try again.';
                    }
                    
                    this.scanningStatus = 'Camera error';
                },

                async switchCamera() {
                    if (this.scanner && this.isScanning) {
                        await this.scanner.stop();
                        this.currentCamera = this.currentCamera === 'environment' ? 'user' : 'environment';
                        await this.initScanner();
                    }
                },

                async restartScanner() {
                    if (this.scanner) {
                        await this.scanner.stop();
                        this.isScanning = false;
                        this.errorMessage = '';
                        this.scanningStatus = 'Restarting scanner...';
                        await this.initScanner();
                    }
                },

                async restartScannerAfterDelay(delay) {
                    setTimeout(() => {
                        this.restartScanner();
                    }, delay);
                },

                showSuccessFeedback() {
                    // Add visual feedback for successful scan
                    const scannerElement = document.getElementById('qr-reader');
                    if (scannerElement) {
                        scannerElement.classList.add('success-flash');
                        setTimeout(() => {
                            scannerElement.classList.remove('success-flash');
                        }, 500);
                    }
                },

                async updateStatus() {
                    if (!this.currentDocument) return;

                    try {
                        const response = await fetch(`/documents/${this.currentDocument.id}/status`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            showSuccess('Status updated successfully');
                            this.formData = { status_id: '', remarks: '' };
                            this.showDetails = false;
                            this.currentDocument = null;
                            this.restartScanner();
                        } else {
                            showError('Error updating status');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showError('Error updating status');
                    }
                }
            }))
        });
    </script>
    @endpush
</x-app-layout> 