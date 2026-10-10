<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'ai_system_prompt' => 'required|string',
            'ai_chat_model' => 'required|string|max:100',
            'rag_min_score' => 'required|numeric|min:0|max:1',
            'rag_max_results' => 'required|integer|min:1|max:10',
        ]);

        Setting::set('ai_system_prompt', $data['ai_system_prompt']);
        Setting::set('ai_chat_model', $data['ai_chat_model']);
        Setting::set('rag_min_score', $data['rag_min_score']);
        Setting::set('rag_max_results', $data['rag_max_results']);

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
