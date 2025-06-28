<x-modal name="create-document-modal" :show="$errors->isNotEmpty()" focusable maxWidth="4xl">
    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
            {{ __('Create Document') }}
        </h2>

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
            <x-primary-button>
                {{ __('Create Document') }}
            </x-primary-button>
        </div>
    </form>
</x-modal> 