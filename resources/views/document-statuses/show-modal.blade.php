<div class="p-6 text-gray-900 dark:text-gray-100">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentStatus->name }}</div>
        </div>
        <div>
            <x-input-label for="code" :value="__('Code')" />
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentStatus->code }}</div>
        </div>
        <div class="md:col-span-2">
            <x-input-label for="description" :value="__('Description')" />
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentStatus->description ?? 'N/A' }}</div>
        </div>
        <div>
            <x-input-label for="color" :value="__('Color')" />
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $documentStatus->color }}; color: #ffffff;">
                    {{ $documentStatus->color }}
                </span>
            </div>
        </div>
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $documentStatus->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $documentStatus->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>
    <div class="mt-6">
        <x-input-label for="timestamps" :value="__('Timestamps')" />
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Created At: {{ $documentStatus->created_at->format('Y-m-d H:i:s') }}
        </div>
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Last Updated At: {{ $documentStatus->updated_at->format('Y-m-d H:i:s') }}
        </div>
    </div>
</div> 