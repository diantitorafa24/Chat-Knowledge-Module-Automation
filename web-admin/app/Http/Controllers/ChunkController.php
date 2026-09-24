<?php

namespace App\Http\Controllers;

use App\Models\DocumentChunk;
use App\Models\Module;
use Illuminate\Http\Request;

class ChunkController extends Controller
{
    public function index(Request $request)
    {
        $modules = Module::orderBy('name')->get(['id', 'code', 'name']);

        $chunks = DocumentChunk::with(['module', 'document'])
            ->when($request->filled('module'), fn ($q) => $q->where('module_id', $request->input('module')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = addcslashes((string) $request->input('q'), '%_\\');

                $q->where('content', 'like', '%' . $keyword . '%');
            })
            ->orderBy('document_id')
            ->orderBy('chunk_index')
            ->paginate(20)
            ->withQueryString();

        return view('chunks.index', compact('chunks', 'modules'));
    }
}
