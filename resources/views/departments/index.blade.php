<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Departments') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-end mb-4">
                        @if(auth()->user()->hasPermission('departments.create'))
                        <div x-data="{}">
                            <button type="button" x-on:click="$dispatch('open-modal', 'create-department-modal')" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-plus mr-2"></i>Create Department
                            </button>
                        </div>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Code</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Head</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Documents</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($departments as $department)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $department->name }}
                                            </div>
                                            @if($department->description)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($department->description, 50) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $department->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $department->head_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $department->documents_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $department->is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' }}">
                                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if(auth()->user()->hasPermission('departments.view'))
                                                <a href="#" onclick="openShowDepartmentModal({{ $department->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endif

                                                @if(auth()->user()->hasPermission('departments.edit'))
                                                <a href="#" onclick="openEditDepartmentModal({{ $department->id }})" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif

                                                @if(auth()->user()->hasPermission('departments.delete'))
                                                @if($department->documents_count === 0)
                                                <form action="{{ route('departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return false;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            onclick="showConfirm({
                                                                title: 'Delete Department: {{ $department->name }}',
                                                                text: 'Are you sure you want to delete this department? This action cannot be undone.',
                                                                confirmButtonText: 'Yes, delete it!'
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    this.form.submit();
                                                                }
                                                            });"
                                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                            No departments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // The delete button click handling has been moved to delete-confirmation-modal.blade.php
        });

        function openEditDepartmentModal(id) {
            fetch(`/departments/${id}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('edit-department-modal-content').innerHTML = html;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-department-modal' }));
            })
            .catch(error => alert('Failed to load edit form: ' + error.message));
        }

        function openShowDepartmentModal(id) {
            fetch(`/departments/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('show-department-modal-content').innerHTML = html;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'show-department-modal' }));
            })
            .catch(error => alert('Failed to load details: ' + error.message));
        }
    </script>
    @endpush

    @include('departments.create-modal')
    <x-modal name="edit-department-modal" :show="false" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Edit Department
            </h2>
            <div id="edit-department-modal-content" class="mt-6">
                <!-- Edit form will be loaded here via AJAX -->
            </div>
        </div>
    </x-modal>
    <x-modal name="show-department-modal" :show="false" maxWidth="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Department Details
            </h2>
            <div id="show-department-modal-content" class="mt-6">
                <!-- Show content will be loaded here via AJAX -->
            </div>
        </div>
    </x-modal>
</x-app-layout> 