<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 text-black leading-tight">
            {{ __('Documents') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Filter Tabs -->
                    <div class="mb-6 flex gap-2">
                        <a href="{{ route('documents.index', ['filter' => 'all']) }}"
                           class="px-4 py-2 rounded-md text-sm font-semibold {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                            All Documents
                        </a>
                        <a href="{{ route('documents.index', ['filter' => 'incoming']) }}"
                           class="px-4 py-2 rounded-md text-sm font-semibold {{ $filter === 'incoming' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                            Incoming
                        </a>
                        <a href="{{ route('documents.index', ['filter' => 'outgoing']) }}"
                           class="px-4 py-2 rounded-md text-sm font-semibold {{ $filter === 'outgoing' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                            Outgoing
                        </a>
                    </div>
                    <div x-data="{}">
                        <!-- Remove the Create New Document button from the All Documents page -->
                    </div>
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Tracking #</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Title</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr class="text-gray-900 dark:text-gray-300">
                                    <td class="px-4 py-2">{{ $document->tracking_number }}</td>
                                    <td class="px-4 py-2">
                                        <button type="button"
                                                class="text-blue-600 dark:text-blue-400 hover:underline focus:outline-none">
                                            {{ $document->title }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full text-gray-900 dark:text-white" style="background-color: {{ $document->status->color }}">
                                            {{ $document->status->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">{{ $document->department->name }}</td>
                                    <td class="px-4 py-2">{{ $document->documentType->name }}</td>
                                    <td class="space-x-2">
                                        <button type="button" onclick="openViewModal('{{ $document->id }}')" class="inline-block p-2 text-blue-600 hover:text-blue-800" title="View"><i class="fas fa-eye"></i></button>
                                        <button type="button" onclick="openPrintVoucherModal('{{ $document->id }}')" class="inline-block p-2 text-purple-500 hover:text-purple-600" title="Print Voucher"><i class="fas fa-print"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // The openForwardModal, closeForwardModal, and form submit event listener are in documents/forward-modal.blade.php
        });

        function openEditModal(documentId) {
            fetch(`/documents/${documentId}/edit`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Raw response for edit modal:', response);
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                    });
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('edit-document-modal-content').innerHTML = html;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-document-modal' }));
            })
            .catch(error => {
                console.error('Error loading edit modal:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load edit form: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        }

        function openPrintVoucherModal(documentId) {
            // Step 1: Generate the print QR code first
            fetch(`/qr-codes/documents/${documentId}/print`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(() => {
                // Step 2: Fetch the voucher modal content
                return fetch(`/documents/${documentId}/voucher?modal=1`);
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('print-voucher-content').innerHTML = html;
                document.getElementById('print-voucher-modal').style.display = 'flex';
                setTimeout(() => {
                    window.print();
                    setTimeout(() => {
                        document.getElementById('print-voucher-modal').style.display = 'none';
                        document.getElementById('print-voucher-content').innerHTML = '';
                    }, 500);
                }, 200);
            });
        }

        function openViewModal(documentId) {
            fetch(`/documents/${documentId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text); });
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('show-document-modal-content').innerHTML = html;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'show-document-modal' }));
            })
            .catch(error => {
                alert('Failed to load document details: ' + error.message);
            });
        }
    </script>
    @endpush

    @include('documents.forward-modal')
    @include('documents.create-modal')

    <x-modal name="edit-document-modal" :show="false" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Edit Document
            </h2>
            <div id="edit-document-modal-content" class="mt-6">
                <!-- Edit form will be loaded here via AJAX -->
            </div>
        </div>
    </x-modal>

    <div id="print-voucher-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div id="print-voucher-content" style="background:white; padding:2rem; border-radius:8px; max-width:700px; margin:auto;"></div>
    </div>

    <x-modal name="show-document-modal" :show="false" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Document Details
            </h2>
            <div id="show-document-modal-content" class="mt-6">
                <!-- Document details will be loaded here via AJAX -->
            </div>
        </div>
    </x-modal>
</x-app-layout> 