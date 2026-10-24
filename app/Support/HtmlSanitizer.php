<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Strips dangerous markup from rich-editor HTML before it is rendered.
 */
class HtmlSanitizer
{
    /**
     * Tags that are removed together with their content.
     *
     * @var list<string>
     */
    private const array FORBIDDEN_TAGS = ['script', 'style', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'meta', 'link', 'base'];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $document = new DOMDocument;
        $previousSetting = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8"?><div id="sanitizer-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousSetting);

        $xpath = new DOMXPath($document);

        foreach (self::FORBIDDEN_TAGS as $tag) {
            foreach (iterator_to_array($document->getElementsByTagName($tag)) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        foreach (iterator_to_array($document->getElementsByTagName('iframe')) as $iframe) {
            /** @var DOMElement $iframe */
            if (! preg_match('~^https://(www\.)?(youtube\.com|youtube-nocookie\.com|player\.vimeo\.com)/~', $iframe->getAttribute('src'))) {
                $iframe->parentNode?->removeChild($iframe);
            }
        }

        foreach (iterator_to_array($xpath->query('//@*') ?: []) as $attribute) {
            $name = strtolower($attribute->nodeName);
            $value = strtolower(trim($attribute->nodeValue ?? ''));

            if (str_starts_with($name, 'on') || (in_array($name, ['href', 'src'], true) && preg_match('~^(javascript|vbscript|data:text)~', $value))) {
                $attribute->ownerElement?->removeAttributeNode($attribute);
            }
        }

        $root = $document->getElementById('sanitizer-root');
        $output = '';

        foreach ($root?->childNodes ?? [] as $child) {
            $output .= $document->saveHTML($child);
        }

        return $output;
    }
}
