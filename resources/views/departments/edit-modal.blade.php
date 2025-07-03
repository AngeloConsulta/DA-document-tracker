<form method="POST" action="{{ route('departments.update', $department) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Name -->
    <div>
        <x-input-label for="name" :value="__('Department Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $department->name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <!-- Code -->
    <div>
        <x-input-label for="code" :value="__('Department Code')" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $department->code)" required />
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A unique code to identify the department (e.g., HR, IT, FIN)</p>
    </div>

    <!-- Description -->
    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $department->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <!-- Head Name -->
    <div>
        <x-input-label for="head_name" :value="__('Department Head')" />
        <x-text-input id="head_name" name="head_name" type="text" class="mt-1 block w-full" :value="old('head_name', $department->head_name)" />
        <x-input-error class="mt-2" :messages="$errors->get('head_name')" />
    </div>

    <!-- Active Status -->
    <div class="flex items-center">
        <input id="is_active" name="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
        <x-input-label for="is_active" :value="__('Active')" class="ml-2" />
    </div>

    <div class="flex items-center justify-end mt-4 space-x-4">
        <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'edit-department-modal')">
            Cancel
        </x-secondary-button>
        <x-primary-button>
            {{ __('Update Department') }}
        </x-primary-button>
    </div>
</form> 