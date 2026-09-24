<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;

class ModuleApiController extends Controller
{
    public function index()
    {
        $modules = Module::with('category')
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(fn ($module) => [
                'id' => $module->id,
                'code' => $module->code,
                'name' => $module->name,
                'category' => $module->category->name ?? null,
                'version' => $module->current_version,
                'description' => $module->description,
            ]);

        return response()->json(['data' => $modules]);
    }
}
