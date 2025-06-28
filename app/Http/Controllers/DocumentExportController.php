<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class DocumentExportController extends Controller
{
    /**
     * Export documents to CSV
     */
    public function exportToCsv(Request $request): Response
    {
        $documents = $this->getFilteredDocuments($request);
        
        $filename = 'documents_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'Tracking Number',
            'Title',
            'Description',
            'Document Type',
            'Department',
            'Status',
            'Source/Recipient',
            'Date Received',
            'Due Date',
            'Created By',
            'Current Assignee',
            'Created At',
            'Updated At'
        ];
        
        $sheet->fromArray([$headers], null, 'A1');
        
        // Add data
        $row = 2;
        foreach ($documents as $document) {
            $sheet->setCellValue('A' . $row, $document->tracking_number);
            $sheet->setCellValue('B' . $row, $document->title);
            $sheet->setCellValue('C' . $row, $document->description);
            $sheet->setCellValue('D' . $row, $document->documentType->name);
            $sheet->setCellValue('E' . $row, $document->department->name);
            $sheet->setCellValue('F' . $row, $document->status->name);
            $sheet->setCellValue('G' . $row, $document->source);
            $sheet->setCellValue('H' . $row, $document->date_received->format('Y-m-d'));
            $sheet->setCellValue('I' . $row, $document->due_date ? $document->due_date->format('Y-m-d') : '');
            $sheet->setCellValue('J' . $row, $document->creator->name);
            $sheet->setCellValue('K' . $row, $document->assignee ? $document->assignee->name : '');
            $sheet->setCellValue('L' . $row, $document->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('M' . $row, $document->updated_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new Csv($spreadsheet);
        $writer->save($filepath);
        
        return response()->download($filepath, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend();
    }

    /**
     * Export documents to Excel
     */
    public function exportToExcel(Request $request): Response
    {
        $documents = $this->getFilteredDocuments($request);
        
        $filename = 'documents_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Document Tracking Report');
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Set headers
        $headers = [
            'Tracking Number',
            'Title',
            'Description',
            'Document Type',
            'Department',
            'Status',
            'Source/Recipient',
            'Date Received',
            'Due Date',
            'Created By',
            'Current Assignee',
            'Created At',
            'Updated At'
        ];
        
        $sheet->fromArray([$headers], null, 'A3');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]
        ];
        $sheet->getStyle('A3:M3')->applyFromArray($headerStyle);
        
        // Add data
        $row = 4;
        foreach ($documents as $document) {
            $sheet->setCellValue('A' . $row, $document->tracking_number);
            $sheet->setCellValue('B' . $row, $document->title);
            $sheet->setCellValue('C' . $row, $document->description);
            $sheet->setCellValue('D' . $row, $document->documentType->name);
            $sheet->setCellValue('E' . $row, $document->department->name);
            $sheet->setCellValue('F' . $row, $document->status->name);
            $sheet->setCellValue('G' . $row, $document->source);
            $sheet->setCellValue('H' . $row, $document->date_received->format('Y-m-d'));
            $sheet->setCellValue('I' . $row, $document->due_date ? $document->due_date->format('Y-m-d') : '');
            $sheet->setCellValue('J' . $row, $document->creator->name);
            $sheet->setCellValue('K' . $row, $document->assignee ? $document->assignee->name : '');
            $sheet->setCellValue('L' . $row, $document->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('M' . $row, $document->updated_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Add borders
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A3:M' . ($row - 1))->applyFromArray($borderStyle);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
        
        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    /**
     * Export document history to Excel
     */
    public function exportHistoryToExcel(Request $request, Document $document): Response
    {
        $document->load(['histories.user', 'histories.fromStatus', 'histories.toStatus', 'histories.fromDepartment', 'histories.toDepartment', 'histories.fromUser', 'histories.toUser']);
        
        $filename = 'document_history_' . $document->tracking_number . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);
        
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Document History - ' . $document->tracking_number);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Set headers
        $headers = [
            'Date/Time',
            'Action',
            'User',
            'From',
            'To',
            'Remarks'
        ];
        
        $sheet->fromArray([$headers], null, 'A3');
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        
        // Add data
        $row = 4;
        foreach ($document->histories as $history) {
            $sheet->setCellValue('A' . $row, $history->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('B' . $row, $history->action_type ?? 'Status Update');
            $sheet->setCellValue('C' . $row, $history->user->name);
            
            // From/To information
            $from = [];
            $to = [];
            
            if ($history->fromStatus) $from[] = 'Status: ' . $history->fromStatus->name;
            if ($history->fromDepartment) $from[] = 'Dept: ' . $history->fromDepartment->name;
            if ($history->fromUser) $from[] = 'User: ' . $history->fromUser->name;
            
            if ($history->toStatus) $to[] = 'Status: ' . $history->toStatus->name;
            if ($history->toDepartment) $to[] = 'Dept: ' . $history->toDepartment->name;
            if ($history->toUser) $to[] = 'User: ' . $history->toUser->name;
            
            $sheet->setCellValue('D' . $row, implode(', ', $from));
            $sheet->setCellValue('E' . $row, implode(', ', $to));
            $sheet->setCellValue('F' . $row, $history->remarks);
            
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
        
        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    /**
     * Get filtered documents based on request parameters
     */
    private function getFilteredDocuments(Request $request)
    {
        $query = Document::with(['documentType', 'department', 'status', 'creator', 'assignee']);

        // Apply filters
        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date_received', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date_received', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Determine document type (incoming/outgoing)
        if ($request->filled('document_direction')) {
            if ($request->document_direction === 'incoming') {
                $query->whereNull('sent_at');
            } elseif ($request->document_direction === 'outgoing') {
                $query->whereNotNull('sent_at');
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
} 