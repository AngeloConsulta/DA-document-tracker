<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Department Information</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $department->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Code</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $department->code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Department Head</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $department->head_name ?? 'Not assigned' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
                @if($department->description)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $department->description }}</dd>
                </div>
                @endif
            </dl>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Statistics</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Documents</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $department->documents_count }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $department->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $department->updated_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div> 