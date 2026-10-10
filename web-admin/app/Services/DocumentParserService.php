<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use RuntimeException;
use ZipArchive;

class DocumentParserService
{
    private const NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    /**
     * Baca file DOCX dan kembalikan daftar blok (heading, paragraph, list_item, table) beserta statistik.
     *
     * @return array{blocks: array<int, array<string, mixed>>, stats: array<string, int>}
     */
    public function parse(string $path): array
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException('File DOCX tidak bisa dibuka.');
        }

        $documentXml = $zip->getFromName('word/document.xml');
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
        
        $rels = $this->readRels($relsXml);

        if ($documentXml === false) {
            throw new RuntimeException('File DOCX tidak memiliki isi dokumen.');
        }

        $styles = $this->readStyles($stylesXml === false ? null : $stylesXml);

        $dom = new DOMDocument();

        if (! $dom->loadXML($documentXml, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            throw new RuntimeException('Isi dokumen DOCX rusak dan tidak bisa dibaca.');
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', self::NS);

        $body = $xpath->query('/w:document/w:body')->item(0);

        if (! $body instanceof DOMElement) {
            throw new RuntimeException('Struktur DOCX tidak dikenali.');
        }

        $blocks = [];
        $this->collect($body, $xpath, $styles, $blocks, $rels, $imagesData);

        if ($blocks === []) {
            throw new RuntimeException('Dokumen tidak berisi teks yang bisa dibaca.');
        }

        return ['blocks' => $blocks, 'stats' => $this->stats($blocks)];
    }

    private function collect(DOMElement $container, DOMXPath $xpath, array $styles, array &$blocks, array $rels = [], array $imagesData = []): void
    {
        foreach ($container->childNodes as $node) {
            if (! $node instanceof DOMElement || $node->namespaceURI !== self::NS) {
                continue;
            }

            if ($node->localName === 'p') {
                $block = $this->parseParagraph($node, $xpath, $styles);
                // Extract drawing/images inside paragraph
                $this->extractImages($node, $xpath, $rels, $imagesData, $blocks);
            } elseif ($node->localName === 'tbl') {
                $block = $this->parseTable($node, $xpath);
            } elseif ($node->localName === 'sdt') {
                $content = $xpath->query('w:sdtContent', $node)->item(0);

                if ($content instanceof DOMElement) {
                    $this->collect($content, $xpath, $styles, $blocks, $rels, $imagesData);
                }

                continue;
            } else {
                continue;
            }

            if ($block !== null) {
                $blocks[] = $block;
            }
        }
    }

    private function parseParagraph(DOMElement $p, DOMXPath $xpath, array $styles): ?array
    {
        $text = $this->paragraphText($p, $xpath);

        if ($text === '') {
            return null;
        }

        $styleId = $this->attr($xpath, 'w:pPr/w:pStyle', $p);
        $outline = $this->attr($xpath, 'w:pPr/w:outlineLvl', $p);
        $styleName = $styles[$styleId]['name'] ?? strtolower((string) $styleId);

        // Daftar isi (TOC) dilewati supaya tidak menggandakan judul.
        if (str_starts_with($styleName, 'toc')) {
            return null;
        }

        $level = $this->headingLevel($styleId, $outline === null ? null : (int) $outline, $styles);

        if ($level !== null) {
            return ['type' => 'heading', 'level' => $level, 'text' => $text];
        }

        $numId = $this->attr($xpath, 'w:pPr/w:numPr/w:numId', $p);
        $isList = ($numId !== null && $numId !== '0') || str_starts_with($styleName, 'list');

        if ($isList) {
            $ilvl = $this->attr($xpath, 'w:pPr/w:numPr/w:ilvl', $p);

            return ['type' => 'list_item', 'level' => (int) ($ilvl ?? 0), 'text' => $text];
        }

        return ['type' => 'paragraph', 'text' => $text];
    }

    private function parseTable(DOMElement $tbl, DOMXPath $xpath): ?array
    {
        $rows = [];

        foreach ($xpath->query('w:tr', $tbl) as $tr) {
            $cells = [];

            foreach ($xpath->query('w:tc', $tr) as $tc) {
                $parts = [];

                foreach ($xpath->query('w:p', $tc) as $p) {
                    $text = $this->paragraphText($p, $xpath);

                    if ($text !== '') {
                        $parts[] = $text;
                    }
                }

                $cells[] = implode(' ', $parts);
            }

            if (array_filter($cells, fn ($cell) => $cell !== '') !== []) {
                $rows[] = $cells;
            }
        }

        return $rows === [] ? null : ['type' => 'table', 'rows' => $rows];
    }

    
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

    private function paragraphText(DOMElement $p, DOMXPath $xpath): string
    {
        $text = '';

        foreach ($xpath->query('.//w:r/w:t | .//w:r/w:tab | .//w:r/w:br | .//w:r/w:noBreakHyphen', $p) as $node) {
            $text .= match ($node->localName) {
                't' => $node->textContent,
                'tab' => ' ',
                'br' => "\n",
                default => '-',
            };
        }

        return trim((string) preg_replace('/[ \t]+/u', ' ', $text));
    }

    private function attr(DOMXPath $xpath, string $query, DOMElement $context): ?string
    {
        $node = $xpath->query($query, $context)->item(0);

        if (! $node instanceof DOMElement) {
            return null;
        }

        $value = $node->getAttributeNS(self::NS, 'val');

        return $value === '' ? null : $value;
    }

    private function readStyles(?string $xml): array
    {
        $styles = [];

        if ($xml === null) {
            return $styles;
        }

        $dom = new DOMDocument();

        if (! $dom->loadXML($xml, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            return $styles;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', self::NS);

        foreach ($xpath->query('//w:style[@w:type="paragraph"]') as $style) {
            $id = $style->getAttributeNS(self::NS, 'styleId');

            if ($id === '') {
                continue;
            }

            $styles[$id] = [
                'name' => strtolower((string) $this->attr($xpath, 'w:name', $style)),
                'based_on' => $this->attr($xpath, 'w:basedOn', $style),
                'outline' => ($outline = $this->attr($xpath, 'w:pPr/w:outlineLvl', $style)) === null ? null : (int) $outline,
            ];
        }

        return $styles;
    }

    /**
     * Tentukan tingkat heading: 0 = Title, 1-9 = Heading 1-9, null = bukan heading.
     */
    private function headingLevel(?string $styleId, ?int $paragraphOutline, array $styles): ?int
    {
        if ($paragraphOutline !== null && $paragraphOutline >= 0 && $paragraphOutline <= 8) {
            return $paragraphOutline + 1;
        }

        $seen = [];
        $current = $styleId;

        while ($current !== null && $current !== '' && ! isset($seen[$current])) {
            $seen[$current] = true;
            $name = isset($styles[$current]) ? $styles[$current]['name'] : strtolower($current);

            if (preg_match('/^heading\s*([1-9])$/', $name, $m)) {
                return (int) $m[1];
            }

            if ($name === 'title') {
                return 0;
            }

            $outline = $styles[$current]['outline'] ?? null;

            if ($outline !== null && $outline >= 0 && $outline <= 8) {
                return $outline + 1;
            }

            $current = $styles[$current]['based_on'] ?? null;
        }

        return null;
    }

    private function stats(array $blocks): array
    {
        $stats = ['headings' => 0, 'paragraphs' => 0, 'list_items' => 0, 'tables' => 0, 'characters' => 0];

        foreach ($blocks as $block) {
            switch ($block['type']) {
                case 'heading':
                    $stats['headings']++;
                    break;
                case 'paragraph':
                    $stats['paragraphs']++;
                    break;
                case 'list_item':
                    $stats['list_items']++;
                    break;
                case 'table':
                    $stats['tables']++;
                    break;
                case 'image':
                    $stats['images'] = ($stats['images'] ?? 0) + 1;
                    break;
            }

            if ($block['type'] === 'image') {
                continue;
            }

            $stats['characters'] += $block['type'] === 'table'
                ? array_sum(array_map(fn ($row) => array_sum(array_map('mb_strlen', $row)), $block['rows']))
                : mb_strlen($block['text']);
        }

        return $stats;
    }
}
