<x-modal name="create-document-modal" :show="$errors->isNotEmpty()" focusable maxWidth="4xl">
    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="p-6" id="create-document-form">
        @csrf
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
            {{ __('Create Document') }}
        </h2>

        <div id="create-document-errors" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded hidden">
            <ul class="list-disc list-inside" id="create-document-errors-list">
            </ul>
        </div>

        <div id="create-document-success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded hidden">
            <span id="create-document-success-message"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Document Type</label>
                    <select name="document_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Department</label>
                    <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" @if($userDepartment) disabled @endif required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (old('department_id', $userDepartment) == $dept->id) ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @if($userDepartment)
                        <input type="hidden" name="department_id" value="{{ $userDepartment }}">
                    @endif
                </div>

                <div>
                    <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Status</label>
                    <select name="status_id" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('status_id') border-red-500 @enderror" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $statuses->where('code', 'DRAFT')->first()?->id ?? $statuses->first()?->id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('status_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Assignee</label>
                    <select name="current_assignee" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('current_assignee') border-red-500 @enderror">
                        <option value="">Select Assignee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('current_assignee') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('current_assignee')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Date Created</label>
                    <input type="date" name="date_received" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                <div>
                    <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Due Date</label>
                    <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">File</label>
                    <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'create-document-modal')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button type="submit" id="create-document-submit">
                {{ __('Create Document') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('create-document-form');
            const submitBtn = document.getElementById('create-document-submit');
            const errorsDiv = document.getElementById('create-document-errors');
            const errorsList = document.getElementById('create-document-errors-list');
            const successDiv = document.getElementById('create-document-success');
            const successMessage = document.getElementById('create-document-success-message');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset messages
                errorsDiv.classList.add('hidden');
                successDiv.classList.add('hidden');
                errorsList.innerHTML = '';
                
                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creating...';

                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        successMessage.textContent = data.message;
                        successDiv.classList.remove('hidden');
                        
                        // Reset form
                        form.reset();
                        
                        // Close modal after 2 seconds and stay on current page
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-document-modal' }));
                            // Insert new row into the table if on outgoing page
                            if (data.document) {
                                const tbody = document.querySelector('table tbody');
                                if (tbody) {
                                    const tr = document.createElement('tr');
                                    tr.className = 'text-gray-900 dark:text-gray-300';
                                    tr.innerHTML = `
                                        <td class="px-4 py-2">${data.document.tracking_number}</td>
                                        <td class="px-4 py-2">
                                            <button type="button"
                                                    onclick="openViewModal('${data.document.id}')"
                                                    class="text-blue-600 dark:text-blue-400 hover:underline focus:outline-none">
                                                ${data.document.title}
                                            </button>
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full text-gray-900 dark:text-white" style="background-color: ${data.document.status.color}">
                                                ${data.document.status.name}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">${data.document.department.name}</td>
                                        <td class="px-4 py-2">${data.document.documentType.name}</td>
                                        <td class="space-x-2">
                                            <button type="button"
                                                    onclick="openEditModal('${data.document.id}')"
                                                    class="inline-block p-2 text-yellow-500 hover:text-yellow-600 transition-colors" title="Edit Document">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" onclick="openPrintVoucherModal('${data.document.id}')" class="inline-block p-2 text-purple-500 hover:text-purple-600" title="Print Voucher"><i class="fas fa-print"></i></button>
                                            <button type="button" onclick="openForwardModal('${data.document.id}', '${data.document.title}', '${data.document.tracking_number}')" class="inline-block p-2 text-blue-500 hover:text-blue-600" title="Forward"><i class="fas fa-share-alt"></i></button>
                                            <form action="/documents/${data.document.id}" method="POST" class="inline" onsubmit="return false;">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').getAttribute('content')}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="button" onclick="showConfirm({ title: 'Delete Document: ${data.document.title}', text: 'Are you sure you want to delete this document? This action cannot be undone.', confirmButtonText: 'Yes, delete it!' }).then((result) => { if (result.isConfirmed) { this.form.submit(); } });" class="inline-block p-2 text-red-500 hover:text-red-600" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    `;
                                    tbody.prepend(tr);
                                }
                            }
                            // Show success message on the page
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        }, 2000);
                    } else {
                        // Show error message
                        if (data.message) {
                            const li = document.createElement('li');
                            li.textContent = data.message;
                            errorsList.appendChild(li);
                        }
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                data.errors[field].forEach(error => {
                                    const li = document.createElement('li');
                                    li.textContent = error;
                                    errorsList.appendChild(li);
                                });
                            });
                        }
                        errorsDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const li = document.createElement('li');
                    li.textContent = 'An unexpected error occurred. Please try again.';
                    errorsList.appendChild(li);
                    errorsDiv.classList.remove('hidden');
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Document';
                });
            });
        });
    </script>
</x-modal> 