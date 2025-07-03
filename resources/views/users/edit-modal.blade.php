<form id="editUserForm" method="POST" action="{{ route('users.update', $user) }}" class="p-6 space-y-6" x-data="{}" @submit.prevent="
    const form = $el;
    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $dispatch('close-modal', 'edit-user-modal');
            Swal.fire({
                title: 'Success!',
                text: 'User has been updated successfully.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to update user.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred while updating the user.',
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    })">
    @csrf
    @method('PUT')

    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
        {{ __('Edit User') }}
    </h2>

    <!-- Name -->
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Email Address -->
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Role -->
    <div>
        <x-input-label for="role_id" :value="__('Role')" />
        <select id="role_id" name="role_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
            <option value="">Select a role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id) == $role->id) ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
    </div>

    <!-- Department -->
    <div>
        <x-input-label for="department_id" :value="__('Department')" />
        <select id="department_id" name="department_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            <option value="">Select a department</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ (old('department_id', $user->department_id) == $department->id) ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
    </div>

    <!-- Active Status -->
    <div class="flex items-center">
        <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-900" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
        <x-input-label for="is_active" :value="__('Active')" class="ml-2" />
    </div>

    <div class="flex justify-end space-x-3 mt-6">
        <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'edit-user-modal')">
            {{ __('Cancel') }}
        </x-secondary-button>
        <x-primary-button type="submit">
            {{ __('Update User') }}
        </x-primary-button>
    </div>
</form> 