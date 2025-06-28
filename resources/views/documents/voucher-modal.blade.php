<style>
    @media print {
        body, html {
            background: #fff !important;
        }
        .print\:qr {
            width: 180px !important;
            height: 180px !important;
            display: block !important;
            margin: 0 0 0 2rem !important;
        }
        .voucher-container {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
    }
</style>
<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow-none print:p-0 print:shadow-none print:bg-white voucher-container" style="margin-top:0;padding-top:0;">
    <div class="flex items-start mb-8" style="align-items: flex-start; margin-top:0;">
        <div class="flex-1">
            <h1 class="text-3xl font-bold mb-1">Document Voucher</h1>
            <div class="text-gray-600 text-base mb-4">Tracking #: <span class="font-semibold">{{ $document->tracking_number }}</span></div>
            <div class="mb-2"><span class="font-semibold">Title:</span> {{ $document->title }}</div>
            <div class="mb-2"><span class="font-semibold">Type:</span> {{ $document->documentType->name }}</div>
            <div class="mb-2"><span class="font-semibold">Department:</span> {{ $document->department->name }}</div>
        </div>
        @if($printQrUrl)
            <div class="print:border-0 print:bg-white ml-8">
                <img src="{{ $printQrUrl }}" alt="Print QR Code" class="print:qr" style="width:180px;height:180px;">
            </div>
        @endif
    </div>
</div> 