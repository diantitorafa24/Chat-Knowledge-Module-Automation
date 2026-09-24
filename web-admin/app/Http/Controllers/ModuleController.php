<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
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
        ]);

        $nextId = (Module::max('id') ?? 0) + 1;
        $data['code'] = 'MOD-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $data['status'] = 'inactive';

        $module = Module::create($data);

        return redirect()->route('modules.show', $module)->with('success', 'Modul berhasil dibuat.');
    }

    public function show(Module $module)
    {
        $module->load('category');

        return view('modules.show', compact('module'));
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
        $module->delete();

        return redirect()->route('modules.index')->with('success', 'Modul berhasil dihapus.');
    }
}
