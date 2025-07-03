<form method="POST" action="{{ route('document-statuses.update', $documentStatus) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $documentStatus->name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="code" :value="__('Code')" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $documentStatus->code)" required />
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <x-textarea-input id="description" name="description" class="mt-1 block w-full" rows="3">{{ old('description', $documentStatus->description) }}</x-textarea-input>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label for="color" :value="__('Color')" />
        <x-text-input id="color" name="color" type="color" class="mt-1 block" :value="old('color', $documentStatus->color)" />
        <x-input-error class="mt-2" :messages="$errors->get('color')" />
    </div>

    <div>
        <label for="is_active" class="inline-flex items-center">
            <input id="is_active" name="is_active" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('is_active', $documentStatus->is_active) ? 'checked' : '' }}>
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Active') }}</span>
        </label>
        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Update Document Status') }}</x-primary-button>
        <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'edit-document-status-modal')">
            {{ __('Cancel') }}
        </x-secondary-button>
    </div>
</form> 