<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Incoming Documents') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-500 text-white rounded-lg p-4 shadow flex items-center gap-4">
                    <i class="fas fa-inbox text-3xl"></i>
                    <div>
                        <div class="text-lg font-bold">{{ $incomingCount }}</div>
                        <div>Total Incoming</div>
                    </div>
                </div>
                <div class="bg-yellow-500 text-white rounded-lg p-4 shadow flex items-center gap-4">
                    <i class="fas fa-hourglass-half text-3xl"></i>
                    <div>
                        <div class="text-lg font-bold">{{ $pendingCount }}</div>
                        <div>Pending</div>
                    </div>
                </div>
                <div class="bg-green-500 text-white rounded-lg p-4 shadow flex items-center gap-4">
                    <i class="fas fa-check-circle text-3xl"></i>
                    <div>
                        <div class="text-lg font-bold">{{ $receivedCount }}</div>
                        <div>Received</div>
                    </div>
                </div>
                <div class="bg-red-500 text-white rounded-lg p-4 shadow flex items-center gap-4">
                    <i class="fas fa-times-circle text-3xl"></i>
                    <div>
                        <div class="text-lg font-bold">{{ $endedCount }}</div>
                        <div>Ended</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
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
                                                onclick="openViewModal('{{ $document->id }}')"
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
                                        <button type="button" onclick="openPrintVoucherModal('{{ $document->id }}')" class="inline-block p-2 text-purple-500 hover:text-purple-600" title="Print Voucher"><i class="fas fa-print"></i></button>
                                        <button type="button" onclick="openForwardModal('{{ $document->id }}', '{{ $document->title }}', '{{ $document->tracking_number }}')" class="inline-block p-2 text-blue-500 hover:text-blue-600" title="Forward"><i class="fas fa-share-alt"></i></button>
                                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline" onsubmit="return false;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="showConfirm({ title: 'Delete Document: {{ $document->title }}', text: 'Are you sure you want to delete this document? This action cannot be undone.', confirmButtonText: 'Yes, delete it!' }).then((result) => { if (result.isConfirmed) { this.form.submit(); } });" class="inline-block p-2 text-red-500 hover:text-red-600" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
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
    @include('documents.forward-modal')
    <div id="print-voucher-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div id="print-voucher-content" style="background:white; padding:2rem; border-radius:8px; max-width:700px; margin:auto;"></div>
    </div>
    <x-modal name="view-document-modal" :show="false" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Document Details
            </h2>
            <div id="view-document-modal-content" class="mt-6">
                <!-- Document details will be loaded here via AJAX -->
            </div>
        </div>
    </x-modal>
    @push('scripts')
    <script>
        function openPrintVoucherModal(documentId) {
            fetch(`/qr-codes/documents/${documentId}/print`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(() => fetch(`/documents/${documentId}/voucher?modal=1`))
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
                document.getElementById('view-document-modal-content').innerHTML = html;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'view-document-modal' }));
            })
            .catch(error => {
                alert('Failed to load document details: ' + error.message);
            });
        }
    </script>
    @endpush
</x-app-layout> 