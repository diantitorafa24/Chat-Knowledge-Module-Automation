<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Module;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    public function __construct(private DocumentUploadService $uploads)
    {
    }

    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $modules = Module::with('category')
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->input('category')))
            ->latest()
            ->get();

        return view('modules.index', compact('modules', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('modules.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('status', 'active')],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => DocumentUploadService::fileRules(),
        ], DocumentUploadService::messages());

        $module = DB::transaction(function () use ($data, $request) {
            $module = Module::create([
                'name' => $data['name'],
                'category_id' => $data['category_id'] ?? null,
                'description' => $data['description'] ?? null,
                'code' => 'MOD-' . str_pad((Module::max('id') ?? 0) + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'inactive',
            ]);

            $this->uploads->upload($module, $request->file('file'), 'v1.0');

            return $module;
        });

        return redirect()->route('modules.show', $module)
            ->with('success', 'Modul dan dokumen v1.0 berhasil diunggah.');
    }

    public function show(Module $module)
    {
        $module->load('category');
        $versions = $module->versions()->with('document')->orderByDesc('id')->get();
        $suggestedVersion = $this->uploads->suggestVersion($module);

        return view('modules.show', compact('module', 'versions', 'suggestedVersion'));
    }

    public function edit(Module $module)
    {
        $categories = Category::orderBy('name')->get();

        return view('modules.edit', compact('module', 'categories'));
    }

    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $module->update($data);

        return redirect()->route('modules.show', $module)->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(Module $module)
    {
        try {
            app(\App\Services\QdrantService::class)->deleteByModule($module->id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal menghapus titik Qdrant', ['module_id' => $module->id, 'error' => $e->getMessage()]);
        }
        Storage::deleteDirectory('modules/' . $module->id);
        $module->delete();

        return redirect()->route('modules.index')->with('success', 'Modul berhasil dihapus.');
    }
}
