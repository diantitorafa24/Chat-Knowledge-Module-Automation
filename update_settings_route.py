import os

routes_path = r"C:\xampp\htdocs\cika\web-admin\routes\web.php"
with open(routes_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace old settings route
old_setting = "Route::get('/settings', function () { return view('settings.index'); })->name('settings.index');"
new_setting = "Route::get('/settings', [App\\Http\\Controllers\\SettingController::class, 'index'])->name('settings.index');\n    Route::put('/settings', [App\\Http\\Controllers\\SettingController::class, 'update'])->name('settings.update');"
content = content.replace(old_setting, new_setting)

with open(routes_path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Settings route updated")
