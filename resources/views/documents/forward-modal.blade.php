<!-- Forward Document Modal -->
<div id="forwardModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Forward Document</h3>
                <button type="button" onclick="closeForwardModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="forwardForm" class="space-y-4">
                @csrf
                <input type="hidden" id="documentId" name="document_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document</label>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <div><strong>Tracking #:</strong> <span id="trackingNumber"></span></div>
                        <div><strong>Title:</strong> <span id="documentTitle"></span></div>
                    </div>
                </div>
                
                <div>
                    <label for="next_department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Forward to Department</label>
                    <select id="next_department_id" name="next_department_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select Department</option>
                        @foreach(\App\Models\Department::where('is_active', true)->get() as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="next_assignee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign to User (Optional)</label>
                    <select id="next_assignee_id" name="next_assignee_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select User</option>
                        @foreach(\App\Models\User::where('is_active', true)->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter forwarding remarks..." required></textarea>
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
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeForwardModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Forward Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openForwardModal(documentId, documentTitle, trackingNumber) {
        if (!documentId) {
            Swal.fire({
                title: 'Error!',
                text: 'Document ID is missing',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
        }
        document.getElementById('documentId').value = documentId;
        document.getElementById('documentTitle').textContent = documentTitle;
        document.getElementById('trackingNumber').textContent = trackingNumber;
        document.getElementById('forwardModal').classList.remove('hidden');
    }

    function closeForwardModal() {
        document.getElementById('forwardModal').classList.add('hidden');
        document.getElementById('forwardForm').reset();
    }

    document.getElementById('forwardForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const documentId = formData.get('document_id');
        
        if (!documentId) {
            Swal.fire({
                title: 'Error!',
                text: 'Document ID is missing',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Convert FormData to JSON
        const data = {
            next_department_id: formData.get('next_department_id'),
            next_assignee_id: formData.get('next_assignee_id') || null,
            remarks: formData.get('remarks'),
            priority: formData.get('priority')
        };
        
        fetch(`/documents/${documentId}/forward`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeForwardModal();
                Swal.fire({
                    title: 'Success!',
                    text: 'Document forwarded successfully!',
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
                    text: data.message || 'Failed to forward document',
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
                text: 'An error occurred while forwarding the document',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        });
    });
</script>
@endpush 