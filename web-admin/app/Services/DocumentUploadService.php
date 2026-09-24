<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Module;
use App\Models\ModuleVersion;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DocumentUploadService
{
    public const MAX_KB = 10240;

    public static function fileRules(): array
    {
        return [
            'bail',
            'required',
            'file',
            'extensions:docx',
            'max:' . self::MAX_KB,
            function (string $attribute, mixed $value, Closure $fail) {
                $valid = false;
                $zip = new ZipArchive();

                if ($zip->open($value->getRealPath()) === true) {
                    $valid = $zip->locateName('word/document.xml') !== false;
                    $zip->close();
                }

                if (! $valid) {
                    $fail('File bukan dokumen DOCX yang valid.');
                }
            },
        ];
    }

    public static function messages(): array
    {
        return [
            'file.required' => 'File DOCX wajib dipilih.',
            'file.file' => 'File gagal diunggah.',
            'file.extensions' => 'File harus berformat .docx.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'file.uploaded' => 'File gagal diunggah. Coba lagi.',
        ];
    }

    public function suggestVersion(Module $module): string
    {
        $last = $module->versions()->orderByDesc('id')->value('version');

        if (! $last || ! preg_match('/^v(\d+)\.(\d+)$/', $last, $m)) {
            return 'v1.0';
        }

        return 'v' . $m[1] . '.' . ((int) $m[2] + 1);
    }

    public function upload(Module $module, UploadedFile $file, string $version): Document
    {
        $size = $file->getSize();
        $mime = $file->getMimeType();
        $original = $file->getClientOriginalName();

        $storedName = Str::lower($module->code) . '-' . Str::slug($version, '_') . '-' . now()->format('YmdHis') . '.docx';
        $path = $file->storeAs('modules/' . $module->id, $storedName);

        try {
            return DB::transaction(function () use ($module, $version, $storedName, $original, $path, $size, $mime) {
                $document = Document::create([
                    'module_id' => $module->id,
                    'filename' => $storedName,
                    'original_filename' => $original,
                    'file_path' => $path,
                    'file_size' => $size,
                    'mime_type' => $mime,
                    'processing_status' => 'uploaded',
                    'progress' => 0,
                ]);

                ModuleVersion::create([
                    'module_id' => $module->id,
                    'version' => $version,
                    'document_id' => $document->id,
                    'status' => 'inactive',
                ]);

                return $document;
            });
        } catch (\Throwable $e) {
            Storage::delete($path);

            throw $e;
        }
    }
}
