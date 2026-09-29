<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleVersion;
use Illuminate\Http\Request;

class VersionListController extends Controller
{
    public function index(Request $request)
    {
        $modules = Module::orderBy('name')->get(['id', 'code', 'name']);

        $versions = ModuleVersion::with(['module', 'document'])
            ->when($request->filled('module'), fn ($q) => $q->where('module_id', $request->input('module')))
            ->orderBy('module_id')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('versions.index', compact('versions', 'modules'));
    }
}
