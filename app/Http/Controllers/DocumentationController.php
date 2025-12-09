<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DocumentationController extends Controller
{
    /**
     * Display the API documentation page
     */
    public function index()
    {
        return view('documentation.index');
    }

    /**
     * Display quick start guide
     */
    public function quickStart()
    {
        $content = File::get(base_path('PAYVIBE_QUICK_START.md'));
        return view('documentation.show', [
            'title' => 'Quick Start Guide',
            'content' => $this->parseMarkdown($content)
        ]);
    }

    /**
     * Display integration guide
     */
    public function integrationGuide()
    {
        $content = File::get(base_path('PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md'));
        return view('documentation.show', [
            'title' => 'Integration Guide',
            'content' => $this->parseMarkdown($content)
        ]);
    }

    /**
     * Display API documentation
     */
    public function apiDocs()
    {
        $content = File::get(base_path('PAYVIBE_API_DOCUMENTATION.md'));
        return view('documentation.show', [
            'title' => 'API Documentation',
            'content' => $this->parseMarkdown($content)
        ]);
    }

    /**
     * Display fee calculator
     */
    public function feeCalculator()
    {
        $content = File::get(base_path('FEE_CALCULATOR.md'));
        return view('documentation.show', [
            'title' => 'Fee Calculator',
            'content' => $this->parseMarkdown($content)
        ]);
    }

    /**
     * Parse markdown to HTML (improved parser)
     */
    private function parseMarkdown($markdown)
    {
        // Remove PayVibe references from content
        $markdown = str_replace('PAYVIBE_', 'XTRAPAY_', $markdown);
        
        // Convert code blocks first (before other processing)
        $markdown = preg_replace_callback('/```(\w+)?\n(.*?)```/s', function($matches) {
            $code = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');
            return '<pre><code>' . $code . '</code></pre>';
        }, $markdown);
        
        // Convert inline code (but not inside code blocks)
        $markdown = preg_replace('/`([^`\n]+)`/', '<code>$1</code>', $markdown);
        
        // Convert headers
        $markdown = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $markdown);
        $markdown = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $markdown);
        $markdown = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $markdown);
        $markdown = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $markdown);
        $markdown = preg_replace('/^##### (.+)$/m', '<h5>$1</h5>', $markdown);
        
        // Convert bold
        $markdown = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $markdown);
        
        // Convert italic
        $markdown = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $markdown);
        
        // Convert links
        $markdown = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank">$1</a>', $markdown);
        
        // Convert tables (basic)
        $markdown = preg_replace_callback('/\|(.+)\|\n\|[-\s\|]+\|\n((?:\|.+\|\n?)+)/', function($matches) {
            $headers = explode('|', trim($matches[1]));
            $rows = explode("\n", trim($matches[2]));
            
            $html = '<table class="table table-bordered"><thead><tr>';
            foreach ($headers as $header) {
                $html .= '<th>' . trim($header) . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            
            foreach ($rows as $row) {
                if (trim($row)) {
                    $cells = explode('|', trim($row));
                    $html .= '<tr>';
                    foreach ($cells as $cell) {
                        $html .= '<td>' . trim($cell) . '</td>';
                    }
                    $html .= '</tr>';
                }
            }
            $html .= '</tbody></table>';
            return $html;
        }, $markdown);
        
        // Convert lists (ordered and unordered)
        $lines = explode("\n", $markdown);
        $inList = false;
        $listType = '';
        $result = [];
        
        foreach ($lines as $line) {
            if (preg_match('/^(\d+)\.\s+(.+)$/', $line, $matches)) {
                if (!$inList || $listType !== 'ol') {
                    if ($inList) $result[] = '</' . $listType . '>';
                    $result[] = '<ol>';
                    $inList = true;
                    $listType = 'ol';
                }
                $result[] = '<li>' . $matches[2] . '</li>';
            } elseif (preg_match('/^[-*]\s+(.+)$/', $line, $matches)) {
                if (!$inList || $listType !== 'ul') {
                    if ($inList) $result[] = '</' . $listType . '>';
                    $result[] = '<ul>';
                    $inList = true;
                    $listType = 'ul';
                }
                $result[] = '<li>' . $matches[1] . '</li>';
            } else {
                if ($inList) {
                    $result[] = '</' . $listType . '>';
                    $inList = false;
                }
                $result[] = $line;
            }
        }
        
        if ($inList) {
            $result[] = '</' . $listType . '>';
        }
        
        $markdown = implode("\n", $result);
        
        // Convert line breaks (but preserve existing HTML)
        $markdown = preg_replace('/(?<!>)\n(?!<)/', '<br>', $markdown);
        
        // Clean up multiple br tags
        $markdown = preg_replace('/(<br>\s*){3,}/', '<br><br>', $markdown);
        
        return $markdown;
    }
}

