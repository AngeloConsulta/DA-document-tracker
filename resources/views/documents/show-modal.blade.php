<div>
    <div class="mb-4">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $document->title }}</h3>
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Tracking #: <span class="font-mono">{{ $document->tracking_number }}</span></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <div class="mb-2"><span class="font-semibold">Type:</span> {{ $document->documentType->name ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Department:</span> {{ $document->department->name ?? '-' }}</div>
            <div class="mb-2"><span class="font-semibold">Status:</span> <span class="px-2 py-1 text-xs rounded-full text-white" style="background-color: {{ $document->status->color ?? '#888' }}">{{ $document->status->name ?? '-' }}</span></div>
            <div class="mb-2"><span class="font-semibold">Assignee:</span> {{ $document->assignee->name ?? '-' }}</div>
        </div>
        <div>
            <div class="mb-2"><span class="font-semibold">Created By:</span> {{ $document->creator->name ?? '-' }}</div>
            @if($document->file_path)
                <div class="mb-2"><span class="font-semibold">File:</span> <a href="{{ Storage::url($document->file_path) }}" class="text-blue-600 hover:underline" target="_blank">Download</a></div>
            @endif
        </div>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Description:</span>
        <div class="mt-1 text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $document->description }}</div>
    </div>
</div> 