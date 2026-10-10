import os

path = r"C:\xampp\htdocs\cika\web-admin\app\Services\DocumentParserService.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add relationships reading in parse()
parse_old = """        $documentXml = $zip->getFromName('word/document.xml');
        $stylesXml = $zip->getFromName('word/styles.xml');
        $zip->close();"""

parse_new = """        $documentXml = $zip->getFromName('word/document.xml');
        $stylesXml = $zip->getFromName('word/styles.xml');
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
        
        // Extract all images to a temp structure
        $imagesData = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_starts_with($name, 'word/media/')) {
                $imagesData[$name] = $zip->getFromIndex($i);
            }
        }
        $zip->close();
        
        $rels = $this->readRels($relsXml);"""
content = content.replace(parse_old, parse_new)

# Add collect images in collect()
collect_old = """            if ($node->localName === 'p') {
                $block = $this->parseParagraph($node, $xpath, $styles);
            } elseif ($node->localName === 'tbl') {"""
collect_new = """            if ($node->localName === 'p') {
                $block = $this->parseParagraph($node, $xpath, $styles);
                // Extract drawing/images inside paragraph
                $this->extractImages($node, $xpath, $rels, $imagesData, $blocks);
            } elseif ($node->localName === 'tbl') {"""
content = content.replace(collect_old, collect_new)

# Add extractImages and readRels methods
new_methods = """
    private function readRels(?string $xml): array
    {
        $rels = [];
        if (!$xml) return $rels;
        $dom = new DOMDocument();
        if (@$dom->loadXML($xml)) {
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');
            foreach ($xpath->query('//rel:Relationship') as $rel) {
                $rels[$rel->getAttribute('Id')] = 'word/' . $rel->getAttribute('Target');
            }
        }
        return $rels;
    }

    private function extractImages(DOMElement $p, DOMXPath $xpath, array $rels, array $imagesData, array &$blocks): void
    {
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        foreach ($xpath->query('.//w:drawing//a:blip', $p) as $blip) {
            $rId = $blip->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed');
            if ($rId && isset($rels[$rId])) {
                $target = $rels[$rId];
                if (isset($imagesData[$target])) {
                    $blocks[] = [
                        'type' => 'image',
                        'mime' => $this->getMimeType($target),
                        'data' => base64_encode($imagesData[$target]),
                    ];
                }
            }
        }
    }

    private function getMimeType(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };
    }
"""

content = content.replace("private function paragraphText", new_methods + "\n    private function paragraphText")

# Update collect() signature
content = content.replace("private function collect(DOMElement $container, DOMXPath $xpath, array $styles, array &$blocks): void", "private function collect(DOMElement $container, DOMXPath $xpath, array $styles, array &$blocks, array $rels = [], array $imagesData = []): void")

# Update collect() call in parse()
content = content.replace("$this->collect($body, $xpath, $styles, $blocks);", "$this->collect($body, $xpath, $styles, $blocks, $rels, $imagesData);")

# Update recursive collect() call inside collect()
content = content.replace("$this->collect($content, $xpath, $styles, $blocks);", "$this->collect($content, $xpath, $styles, $blocks, $rels, $imagesData);")

# Update stats function
stats_old = "                case 'table':\n                    $stats['tables']++;\n                    break;\n            }"
stats_new = "                case 'table':\n                    $stats['tables']++;\n                    break;\n                case 'image':\n                    $stats['images'] = ($stats['images'] ?? 0) + 1;\n                    break;\n            }"
content = content.replace(stats_old, stats_new)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("DocumentParserService updated.")
