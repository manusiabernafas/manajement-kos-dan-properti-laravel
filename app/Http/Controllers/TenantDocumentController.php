<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantDocument;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantDocumentController extends Controller
{
    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->authorize('update', $tenant);

        $request->validate([
            'document_type' => ['required', 'in:ktp,kk,agreement,other'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $file = $request->file('file');
        $path = $file->store('tenant-documents/'.$tenant->id, 'public');

        $document = TenantDocument::create([
            'tenant_id' => $tenant->id,
            'document_type' => $request->document_type,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        AuditLogService::log('create', 'tenant-documents', $document->id, null, $document->toArray());

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function destroy(Tenant $tenant, TenantDocument $document): RedirectResponse
    {
        $this->authorize('update', $tenant);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        AuditLogService::log('delete', 'tenant-documents', $document->id, $document->toArray(), null);

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
