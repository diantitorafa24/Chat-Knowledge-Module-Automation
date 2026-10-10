import os

path = r"C:\xampp\htdocs\cika\web-admin\app\Http\Controllers\ModuleVersionController.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add images activation
act_old = """        Chunk::where('module_id', $module->id)
            ->where('document_id', $document->id)
            ->update(['is_active' => true]);"""
act_new = """        Chunk::where('module_id', $module->id)
            ->where('document_id', $document->id)
            ->update(['is_active' => true]);
            
        \App\Models\DocumentImage::where('module_id', $module->id)
            ->where('document_id', $document->id)
            ->update(['is_active' => true]);"""
content = content.replace(act_old, act_new)

# Add images deactivation
deact_old = """        Chunk::where('module_id', $module->id)
            ->where('document_id', $document->id)
            ->update(['is_active' => false]);"""
deact_new = """        Chunk::where('module_id', $module->id)
            ->where('document_id', $document->id)
            ->update(['is_active' => false]);
            
        \App\Models\DocumentImage::where('module_id', $module->id)
            ->where('document_id', $document->id)
            ->update(['is_active' => false]);"""
content = content.replace(deact_old, deact_new)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("ModuleVersionController updated.")
