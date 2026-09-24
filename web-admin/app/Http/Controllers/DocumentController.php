<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function __construct(private DocumentUploadService $uploads)
    {
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
}
