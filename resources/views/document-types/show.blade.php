<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Document Type Details') }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->hasPermission('document_types.edit'))
                <a href="{{ route('document-types.edit', $documentType) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                @endif
                <x-secondary-button href="{{ route('document-types.index') }}">
                    {{ __('Back to List') }}
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentType->name }}</div>
                        </div>

                        <div>
                            <x-input-label for="code" :value="__('Code')" />
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentType->code }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentType->description ?? 'N/A' }}</div>
                        </div>

                         <div>
                            <x-input-label for="requires_approval" :value="__('Requires Approval')" />
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $documentType->requires_approval ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $documentType->requires_approval ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                             <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $documentType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $documentType->is_active ? 'Active' : 'Inactive' }}
                                </span>
                             </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="timestamps" :value="__('Timestamps')" />
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Created At: {{ $documentType->created_at->format('Y-m-d H:i:s') }}
                        </div>
                         <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Last Updated At: {{ $documentType->updated_at->format('Y-m-d H:i:s') }}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
