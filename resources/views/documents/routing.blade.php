<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Document Routing') }} - {{ $document->tracking_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Document Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Document Information
                            </h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Tracking Number</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $document->tracking_number }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $document->title }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Department</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $document->department->name }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Assignee</label>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $document->assignee ? $document->assignee->name : 'Unassigned' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $document->status->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Routing Actions -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Route to Department -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Route to Department
                            </h3>
                            
                            <form id="routeToDepartmentForm" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="to_department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target Department</label>
                                        <select id="to_department_id" name="to_department_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="to_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign to User (Optional)</label>
                                        <select id="to_user_id" name="to_user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="route_remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                                    <textarea id="route_remarks" name="remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter routing remarks..."></textarea>
                                </div>
                                
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Route Document
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Forward to User -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Forward to User
                            </h3>
                            
                            <form id="forwardToUserForm" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="forward_to_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Forward to User</label>
                                    <select id="forward_to_user_id" name="to_user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="forward_priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <select id="forward_priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="forward_remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                                    <textarea id="forward_remarks" name="remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter forwarding remarks..."></textarea>
                                </div>
                                
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Forward Document
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Routing History -->
            <div class="mt-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Routing History
                        </h3>
                        
                        <div id="routingHistory" class="space-y-3">
                            <!-- Routing history will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadRoutingHistory();
            
            // Route to Department Form
            document.getElementById('routeToDepartmentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                routeToDepartment();
            });
            
            // Forward to User Form
            document.getElementById('forwardToUserForm').addEventListener('submit', function(e) {
                e.preventDefault();
                forwardToUser();
            });
        });

        function routeToDepartment() {
            const form = document.getElementById('routeToDepartmentForm');
            const formData = new FormData(form);
            
            fetch('{{ route("documents.routing.to-department", $document) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    form.reset();
                    loadRoutingHistory();
                    // Update document info
                    updateDocumentInfo(data.document);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Failed to route document');
            });
        }

        function forwardToUser() {
            const form = document.getElementById('forwardToUserForm');
            const formData = new FormData(form);
            
            fetch('{{ route("documents.routing.to-user", $document) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    form.reset();
                    loadRoutingHistory();
                    // Update document info
                    updateDocumentInfo(data.document);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Failed to forward document');
            });
        }

        function loadRoutingHistory() {
            fetch('{{ route("documents.routing.history", $document) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayRoutingHistory(data.history);
                }
            })
            .catch(error => {
                console.error('Error loading routing history:', error);
            });
        }

        function displayRoutingHistory(history) {
            const container = document.getElementById('routingHistory');
            
            if (history.length === 0) {
                container.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No routing history available.</p>';
                return;
            }
            
            const historyHtml = history.map(item => {
                const date = new Date(item.created_at).toLocaleString();
                const action = item.action_type === 'routing' ? 'Routed' : 
                             item.action_type === 'forwarding' ? 'Forwarded' : 'Updated';
                
                let details = '';
                if (item.from_department && item.to_department) {
                    details = `from ${item.from_department.name} to ${item.to_department.name}`;
                } else if (item.from_user && item.to_user) {
                    details = `from ${item.from_user.name} to ${item.to_user.name}`;
                }
                
                return `
                    <div class="border-l-4 border-indigo-500 pl-4 py-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    ${action} ${details}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    ${item.remarks}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    by ${item.user.name}
                                </p>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                ${date}
                            </span>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = historyHtml;
        }

        function updateDocumentInfo(document) {
            // Update the document info display if needed
            // This could refresh the page or update specific elements
            location.reload();
        }

        function showSuccess(message) {
            // You can implement your own success notification here
            alert(message);
        }

        function showError(message) {
            // You can implement your own error notification here
            alert('Error: ' + message);
        }
    </script>
    @endpush
</x-app-layout> 