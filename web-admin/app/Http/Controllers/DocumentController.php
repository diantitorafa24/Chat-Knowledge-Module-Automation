<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Module;
use App\Models\ModuleVersion;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function __construct(private DocumentUploadService $uploads)
    {
    }

    public function index()
    {
        $documents = Document::with(['module', 'versions'])->latest()->get();

        return view('documents.index', compact('documents'));
    }

    public function processing()
    {
        $documents = Document::with(['module', 'versions'])
            ->where('processing_status', '!=', 'active')
            ->latest()
            ->get();

        return view('documents.processing', compact('documents'));
    }

    public function store(Request $request, Module $module)
    {
        $data = $request->validate([
            'version' => [
                'required',
                'regex:/^v\d+\.\d+$/',
                Rule::unique('module_versions', 'version')->where('module_id', $module->id),
            ],
            'file' => DocumentUploadService::fileRules(),
        ], array_merge(DocumentUploadService::messages(), [
            'version.required' => 'Versi wajib diisi.',
            'version.regex' => 'Format versi harus seperti v1.1 atau v2.0.',
            'version.unique' => 'Versi ini sudah ada pada modul ini.',
        ]));

        $this->uploads->upload($module, $request->file('file'), $data['version']);

        return redirect()->route('modules.show', $module)
            ->with('success', 'Dokumen versi ' . $data['version'] . ' berhasil diunggah.');
    }

    public function download(Document $document)
    {
        abort_unless(Storage::exists($document->file_path), 404, 'File tidak ditemukan di penyimpanan.');

        return Storage::download($document->file_path, $document->original_filename);
    }

    public function destroy(Document $document)
    {
        if ($document->processing_status === 'active') {
            return back()->with('error', 'Dokumen aktif tidak bisa dihapus. Nonaktifkan dulu.');
        }

        Storage::delete($document->file_path);
        ModuleVersion::where('document_id', $document->id)->delete();
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
