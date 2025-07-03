<form id="editDocumentForm" method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" x-data="{}" @submit.prevent="
    const form = $el;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $dispatch('close-modal', 'edit-document-modal');
            Swal.fire({
                title: 'Success!',
                text: 'Document has been updated successfully.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to update document.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred while updating the document.',
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    })">
    @csrf
    @method('PUT')
    
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Left Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $document->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror" required>
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Document Type</label>
                <select name="document_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->id }}" @if($document->document_type_id == $type->id) selected @endif>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Department</label>
                <select name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @if($document->department_id == $dept->id) selected @endif>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Status</label>
                <select name="status_id" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" @if($document->status_id == $status->id) selected @endif>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Assignee</label>
                <input type="text" name="current_assignee" value="{{ old('current_assignee', $document->current_assignee) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">File</label>
                <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                @if($document->file_path)
                    <a href="{{ Storage::url($document->file_path) }}" class="text-blue-600 hover:underline mt-2 inline-block" target="_blank">Current File</a>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4">
        <label class="block text-gray-600 dark:text-gray-300 text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $document->description) }}</textarea>
    </div>

    <div class="flex justify-end space-x-3 mt-6">
        <x-secondary-button type="button" @click="$dispatch('close-modal', 'edit-document-modal')">
            {{ __('Cancel') }}
        </x-secondary-button>
        <x-primary-button type="submit">
            {{ __('Update Document') }}
        </x-primary-button>
    </div>
</form> 