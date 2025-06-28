<x-modal name="create-department-modal" :show="$errors->isNotEmpty()" focusable>
    <form method="POST" action="{{ route('departments.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
            {{ __('Create Department') }}
        </h2>

        <!-- Name -->
        <div class="mb-4">
            <x-input-label for="name" :value="__('Department Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Code -->
        <div class="mb-4">
            <x-input-label for="code" :value="__('Department Code')" />
            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" required />
            <x-input-error class="mt-2" :messages="$errors->get('code')" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A unique code to identify the department (e.g., HR, IT, FIN)</p>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <x-input-label for="description" :value="__('Description')" />
            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <!-- Head Name -->
        <div class="mb-4">
            <x-input-label for="head_name" :value="__('Department Head')" />
            <x-text-input id="head_name" name="head_name" type="text" class="mt-1 block w-full" :value="old('head_name')" />
            <x-input-error class="mt-2" :messages="$errors->get('head_name')" />
        </div>

        <!-- Active Status -->
        <div class="flex items-center mb-6">
            <input id="is_active" name="is_active" type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-900" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
            <x-input-label for="is_active" :value="__('Active')" class="ml-2" />
        </div>

        <div class="flex justify-end space-x-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'create-department-modal')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button>
                {{ __('Create Department') }}
            </x-primary-button>
        </div>
    </form>
</x-modal> 