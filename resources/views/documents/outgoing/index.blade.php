<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Outgoing Documents') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-blue-500 text-white rounded-lg p-6 shadow-lg border border-blue-400 flex items-center gap-4">
                    <i class="fas fa-file-alt text-3xl"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $outgoingCount }}</div>
                        <div class="text-blue-100">Total Outgoing</div>
                    </div>
                </div>
                <div class="bg-yellow-500 text-white rounded-lg p-6 shadow-lg border border-yellow-400 flex items-center gap-4">
                    <i class="fas fa-hourglass-half text-3xl"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $pendingCount }}</div>
                        <div class="text-yellow-100">Pending</div>
                    </div>
                </div>
                <div class="bg-green-500 text-white rounded-lg p-6 shadow-lg border border-green-400 flex items-center gap-4">
                    <i class="fas fa-paper-plane text-3xl"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $sentCount }}</div>
                        <div class="text-green-100">Sent</div>
                    </div>
                </div>
                <div class="bg-red-500 text-white rounded-lg p-6 shadow-lg border border-red-400 flex items-center gap-4">
                    <i class="fas fa-times-circle text-3xl"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $endedCount }}</div>
                        <div class="text-red-100">Ended</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-end mb-4">
                        <div x-data="{}">
                            <button type="button" x-on:click="$dispatch('open-modal', 'create-document-modal')" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-plus mr-2"></i>Create Document
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Tracking #</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($documents as $document)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $document->tracking_number }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <button type="button"
                                                    onclick="openViewModal('{{ $document->id }}')"
                                                    class="text-blue-600 dark:text-blue-400 hover:underline focus:outline-none">
                                                {{ $document->title }}
                                            </button>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full text-white" style="background-color: {{ $document->status->color }}">
                                                {{ $document->status->name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $document->department->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $document->documentType->name }}</td>
                                        <td class="px-4 py-3 text-sm space-x-2">
                                            <button type="button"
                                                    onclick="openEditModal('{{ $document->id }}')"
                                                    class="inline-block p-2 text-yellow-500 hover:text-yellow-600 transition-colors" title="Edit Document">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" onclick="openPrintVoucherModal('{{ $document->id }}')" class="inline-block p-2 text-purple-500 hover:text-purple-600 transition-colors" title="Print Voucher"><i class="fas fa-print"></i></button>
                                            <button type="button" onclick="openForwardModal('{{ $document->id }}', '{{ $document->title }}', '{{ $document->tracking_number }}')" class="inline-block p-2 text-blue-500 hover:text-blue-600 transition-colors" title="Forward"><i class="fas fa-share-alt"></i></button>
                                            <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline" onsubmit="return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="showConfirm({ title: 'Delete Document: {{ $document->title }}', text: 'Are you sure you want to delete this document? This action cannot be undone.', confirmButtonText: 'Yes, delete it!' }).then((result) => { if (result.isConfirmed) { this.form.submit(); } });" class="inline-block p-2 text-red-500 hover:text-red-600 transition-colors" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('documents.forward-modal')
    @include('documents.create-modal')
    <div id="print-voucher-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div id="print-voucher-content" style="background:white; padding:2rem; border-radius:8px; max-width:700px; margin:auto;"></div>
    </div>
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

        function openEditModal(documentId) {
            fetch(`/documents/${documentId}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text); });
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('edit-document-modal-content').innerHTML = html;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-document-modal' }));
            })
            .catch(error => {
                alert('Failed to load edit form: ' + error.message);
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
</x-app-layout> 