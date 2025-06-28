<x-modal name="create-document-status-modal" :show="$errors->isNotEmpty()" focusable>
    <form method="POST" action="{{ route('document-statuses.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
            {{ __('Create Document Status') }}
        </h2>

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
            <x-input-label for="color" :value="__('Color')" />
            <x-text-input id="color" name="color" type="color" class="mt-1 block" :value="old('color', '#000000')" />
            <x-input-error class="mt-2" :messages="$errors->get('color')" />
        </div>

        <div class="mb-6">
            <label for="is_active" class="inline-flex items-center">
                <input id="is_active" name="is_active" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('is_active', true) ? 'checked' : '' }}>
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Active') }}</span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
        </div>

        <div class="flex justify-end space-x-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'create-document-status-modal')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button>
                {{ __('Create Document Status') }}
            </x-primary-button>
        </div>
    </form>
</x-modal> 