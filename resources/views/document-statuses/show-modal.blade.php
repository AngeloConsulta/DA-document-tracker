<x-modal name="show-document-status-modal" :show="$errors->isNotEmpty()" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
            {{ __('Document Status Details') }}
        </h2>
        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" />
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentStatus->name }}</div>
        </div>
        <div class="mb-4">
            <x-input-label for="document_sub_type_id" :value="__('Document Sub-Type')" />
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentStatus->subType->name ?? '' }}</div>
        </div>
        <div class="flex justify-end mt-6">
            <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'show-document-status-modal')">
                {{ __('Close') }}
            </x-secondary-button>
        </div>
    </div>
</x-modal> 