<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentChunk;
use App\Models\Module;
use App\Models\ModuleVersion;
use App\Services\QdrantService;
use Illuminate\Support\Facades\DB;

class ModuleVersionController extends Controller
{
    public function __construct(private QdrantService $qdrant)
    {
    }

    public function activate(Module $module, ModuleVersion $version)
    {
        abort_unless($version->module_id === $module->id, 404);

        $document = $version->document;

        if (! $document) {
            return back()->with('error', 'Versi ini tidak punya dokumen.');
        }

        if ($version->status === 'active') {
            return back()->with('success', 'Versi ' . $version->version . ' sudah aktif.');
        }

        if ($document->processing_status !== 'indexed') {
            return back()->with('error', 'Dokumen harus berstatus indexed sebelum diaktifkan (sekarang: ' . $document->processing_status . ').');
        }

        try {
            $expected = DocumentChunk::where('document_id', $document->id)->count();
            $indexed = $this->qdrant->count(['must' => [['key' => 'document_id', 'match' => ['value' => $document->id]]]]);

            if ($expected === 0 || $indexed !== $expected) {
                return back()->with('error', "Indeks belum lengkap ($indexed dari $expected chunk). Proses ulang dokumen dulu.");
            }

            $this->qdrant->setActiveForModule($module->id, false);
            $this->qdrant->setActiveForDocument($document->id, true);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengubah indeks: ' . $e->getMessage());
        }

        DB::transaction(function () use ($module, $version, $document) {
            foreach ($module->versions()->where('status', 'active')->get() as $old) {
                $old->update(['status' => 'inactive']);

                Document::where('id', $old->document_id)
                    ->where('processing_status', 'active')
                    ->update(['processing_status' => 'indexed', 'progress' => 90]);
                \App\Models\DocumentImage::where('document_id', $old->document_id)->update(['is_active' => false]);
            }

            $version->update(['status' => 'active']);
            $document->update(['processing_status' => 'active', 'progress' => 100]);
            $module->update(['status' => 'active', 'current_version' => $version->version]);
            \App\Models\DocumentImage::where('document_id', $document->id)->update(['is_active' => true]);
        });

        return back()->with('success', 'Versi ' . $version->version . ' diaktifkan. Bot sekarang memakai versi ini.');
    }

    public function deactivate(Module $module, ModuleVersion $version)
    {
        abort_unless($version->module_id === $module->id, 404);

        if ($version->status !== 'active') {
            return back()->with('error', 'Versi ini tidak sedang aktif.');
        }

        try {
            if ($version->document) {
                $this->qdrant->setActiveForDocument($version->document->id, false);
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengubah indeks: ' . $e->getMessage());
        }

        DB::transaction(function () use ($module, $version) {
            $version->update(['status' => 'inactive']);

            if ($version->document) {
                $version->document->update(['processing_status' => 'indexed', 'progress' => 90]);
                \App\Models\DocumentImage::where('document_id', $version->document->id)->update(['is_active' => false]);
            }

            $module->update(['status' => 'inactive', 'current_version' => null]);
        });

        return back()->with('success', 'Versi ' . $version->version . ' dinonaktifkan.');
    }
}
