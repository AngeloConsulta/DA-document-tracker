<x-modal name="create-document-type-modal" :show="$errors->isNotEmpty()" focusable>
    <form method="POST" action="{{ route('document-types.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
            {{ __('Create Document Type') }}
        </h2>

        @if ($errors->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $errors->first('error') }}</span>
            </div>
        @endif

        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="mb-4">
            <x-input-label for="code" :value="__('Code')" />
            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" required />
            <x-input-error class="mt-2" :messages="$errors->get('code')" />
        </div>

        <div class="mb-4">
            <x-input-label for="description" :value="__('Description')" />
            <x-textarea-input id="description" name="description" class="mt-1 block w-full" rows="3">{{ old('description') }}</x-textarea-input>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div class="mb-4">
            <label for="requires_approval" class="inline-flex items-center">
                <input type="hidden" name="requires_approval" value="0">
                <input id="requires_approval" name="requires_approval" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('requires_approval') ? 'checked' : '' }} value="1">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Requires Approval') }}</span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('requires_approval')" />
        </div>

        <div class="mb-6">
            <label for="is_active" class="inline-flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active" name="is_active" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('is_active', true) ? 'checked' : '' }} value="1">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Active') }}</span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
        </div>

        <div class="flex justify-end space-x-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'create-document-type-modal')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button>
                {{ __('Create Document Type') }}
            </x-primary-button>
        </div>
    </form>
</x-modal> 