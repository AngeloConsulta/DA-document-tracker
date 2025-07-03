<x-modal name="edit-document-status-modal" :show="$errors->isNotEmpty()" focusable>
    <form method="POST" action="{{ route('document-statuses.update', $documentStatus->id) }}" class="p-6">
        @csrf
        @method('PUT')
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
            {{ __('Edit Document Status') }}
        </h2>
        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $documentStatus->name)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div class="mb-4">
            <x-input-label for="document_sub_type_id" :value="__('Document Sub-Type')" />
            <select id="document_sub_type_id" name="document_sub_type_id" class="mt-1 block w-full" required>
                @foreach($documentSubTypes as $subType)
                    <option value="{{ $subType->id }}" {{ old('document_sub_type_id', $documentStatus->document_sub_type_id) == $subType->id ? 'selected' : '' }}>{{ $subType->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('document_sub_type_id')" />
        </div>
        <div class="flex justify-end space-x-3 mt-6">
            <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'edit-document-status-modal')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button type="submit">
                {{ __('Update Status') }}
            </x-primary-button>
        </div>
    </form>
</x-modal> 