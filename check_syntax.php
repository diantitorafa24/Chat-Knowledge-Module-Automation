<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('C:\xampp\htdocs\cika\web-admin\app'));
$errors = [];
$total = 0;
foreach ($files as $file) {
    if ($file->getExtension() === 'php') {
        $total++;
        $cmd = 'php -l "' . $file->getPathname() . '" 2>&1';
        $out = shell_exec($cmd);
        if (strpos($out, 'No syntax errors detected') === false) {
            $errors[] = $file->getPathname() . ": " . $out;
        }
    }
}
echo "Checked $total PHP files in app/.\n";
if (empty($errors)) {
    echo "All PHP files have valid syntax!\n";
} else {
    echo "Errors found:\n" . implode("\n", $errors) . "\n";
}
