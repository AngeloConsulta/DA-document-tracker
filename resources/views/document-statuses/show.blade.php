<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Document Status Details') }}
            </h2>
            <div class="flex space-x-2">
                 @if(auth()->user()->hasPermission('document_statuses.edit'))
                <a href="{{ route('document-statuses.edit', $documentStatus) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                 @endif
                <a href="{{ route('document-statuses.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Back to List') }}
                </a>
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
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentStatus->name }}</div>
                        </div>

                        <div>
                            <x-input-label for="code" :value="__('Code')" />
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentStatus->code }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $documentStatus->description ?? 'N/A' }}</div>
                        </div>

                         <div>
                             <x-input-label for="color" :value="__('Color')" />
                              <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $documentStatus->color }}; color: #ffffff;">
                                     {{ $documentStatus->color }}
                                 </span>
                             </div>
                         </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                             <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $documentStatus->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $documentStatus->is_active ? 'Active' : 'Inactive' }}
                                </span>
                             </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="timestamps" :value="__('Timestamps')" />
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Created At: {{ $documentStatus->created_at->format('Y-m-d H:i:s') }}
                        </div>
                         <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Last Updated At: {{ $documentStatus->updated_at->format('Y-m-d H:i:s') }}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</x-app-layout> 