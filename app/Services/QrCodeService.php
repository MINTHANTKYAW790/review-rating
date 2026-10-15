<?php

namespace App\Services;

use App\Models\Store;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function templates(): array
    {
        return [
            'classic' => ['name' => 'Classic Clean', 'description' => 'Simple clean border for everyday use.'],
            'neon' => ['name' => 'Neon Glow', 'description' => 'Bright gradient frame that draws attention.'],
            'royal' => ['name' => 'Royal Frame', 'description' => 'Premium layered frame for luxury style.'],
            'dotted' => ['name' => 'Dotted Pop', 'description' => 'Fun dotted corners with playful vibe.'],
            'sunburst' => ['name' => 'Sunburst', 'description' => 'Warm color burst to stand out on posters.'],
            'tech' => ['name' => 'Tech Grid', 'description' => 'Modern angular border for digital branding.'],
        ];
    }

    public function sanitizeTemplate(string $template): string
    {
        $available = array_keys($this->templates());

        return in_array($template, $available, true) ? $template : 'classic';
    }

    public function buildStyledSvg(Store $store, string $template, int $canvasSize): string
    {
        $templateKey = $this->sanitizeTemplate($template);
        $padding = (int) round($canvasSize * 0.16);
        $qrSize = $canvasSize - ($padding * 2);
        $url = url('/public/qr/'.$store->slug);
        $rawQrSvg = QrCode::format('svg')->size($qrSize)->margin(1)->generate($url);
        $qrInnerSvg = $this->extractInnerSvg($rawQrSvg);
        $borderSvg = $this->buildBorderSvg($templateKey, $canvasSize);
        $labelFontSize = $this->scaled($canvasSize, 18);
        $subFontSize = $this->scaled($canvasSize, 12);
        $innerInset = $this->scaled($canvasSize, 22);
        $innerSize = $canvasSize - $this->scaled($canvasSize, 44);
        $innerRadius = $this->scaled($canvasSize, 16);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$canvasSize}" height="{$canvasSize}" viewBox="0 0 {$canvasSize} {$canvasSize}">
    <defs>
        <style>
            .qr-label { font-family: Arial, sans-serif; font-size: {$labelFontSize}px; fill: #2c2c44; font-weight: 600; }
            .qr-sub { font-family: Arial, sans-serif; font-size: {$subFontSize}px; fill: #666; }
        </style>
    </defs>
    {$borderSvg}
    <rect x="{$innerInset}" y="{$innerInset}" width="{$innerSize}" height="{$innerSize}" rx="{$innerRadius}" fill="#ffffff" />
    <g transform="translate({$padding}, {$padding})">
        {$qrInnerSvg}
    </g>
</svg>
SVG;
    }

    private function buildBorderSvg(string $template, int $canvasSize): string
    {
        $inset = $this->scaled($canvasSize, 12);
        $outer = $canvasSize - ($inset * 2);
        $radius = $this->scaled($canvasSize, 26);
        $smallRadius = $this->scaled($canvasSize, 14);
        $stroke = $this->scaled($canvasSize, 6);
        $innerInset = $this->scaled($canvasSize, 28);
        $innerSize = $canvasSize - $this->scaled($canvasSize, 56);
        $innerStroke = $this->scaled($canvasSize, 2);
        $corner = $this->scaled($canvasSize, 42);
        $dotRadius = $this->scaled($canvasSize, 11);
        $lineStart = $this->scaled($canvasSize, 28);
        $lineEnd = $this->scaled($canvasSize, 70);
        $lineYTop = $this->scaled($canvasSize, 60);
        $lineYBottom = $canvasSize - $this->scaled($canvasSize, 60);
        $lineRightStart = $canvasSize - $this->scaled($canvasSize, 70);
        $lineRightEnd = $canvasSize - $this->scaled($canvasSize, 28);
        $lineStroke = $this->scaled($canvasSize, 4);
        $cornerFar = $canvasSize - $corner;

        if ($template === 'neon') {
            return <<<SVG
<defs>
    <linearGradient id="grad-neon" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#5c6cff"/>
        <stop offset="50%" stop-color="#ff5fa2"/>
        <stop offset="100%" stop-color="#ffc857"/>
    </linearGradient>
</defs>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#f8f9ff"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="url(#grad-neon)" stroke-width="{$stroke}"/>
SVG;
        }

        if ($template === 'royal') {
            return <<<SVG
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#f7f3ff"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#5d4ea2" stroke-width="{$stroke}"/>
<rect x="{$innerInset}" y="{$innerInset}" width="{$innerSize}" height="{$innerSize}" rx="{$smallRadius}" fill="none" stroke="#b89be8" stroke-width="{$innerStroke}" stroke-dasharray="8 8"/>
SVG;
        }

        if ($template === 'dotted') {
            return <<<SVG
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#fffaf4"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#ff9f43" stroke-width="{$stroke}" stroke-dasharray="3 10"/>
<circle cx="{$corner}" cy="{$corner}" r="{$dotRadius}" fill="#ffbe76"/>
<circle cx="{$cornerFar}" cy="{$corner}" r="{$dotRadius}" fill="#ffbe76"/>
<circle cx="{$corner}" cy="{$cornerFar}" r="{$dotRadius}" fill="#ffbe76"/>
<circle cx="{$cornerFar}" cy="{$cornerFar}" r="{$dotRadius}" fill="#ffbe76"/>
SVG;
        }

        if ($template === 'sunburst') {
            return <<<SVG
<defs>
    <linearGradient id="grad-sun" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#ffefba"/>
        <stop offset="100%" stop-color="#ff9f43"/>
    </linearGradient>
</defs>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="url(#grad-sun)"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#f0932b" stroke-width="{$stroke}"/>
SVG;
        }

        if ($template === 'tech') {
            return <<<SVG
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#eef5ff"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#2f6fb0" stroke-width="{$stroke}"/>
<path d="M {$lineStart} {$lineYTop} H {$lineEnd}" stroke="#2f6fb0" stroke-width="{$lineStroke}"/>
<path d="M {$lineRightStart} {$lineYTop} H {$lineRightEnd}" stroke="#2f6fb0" stroke-width="{$lineStroke}"/>
<path d="M {$lineStart} {$lineYBottom} H {$lineEnd}" stroke="#2f6fb0" stroke-width="{$lineStroke}"/>
<path d="M {$lineRightStart} {$lineYBottom} H {$lineRightEnd}" stroke="#2f6fb0" stroke-width="{$lineStroke}"/>
SVG;
        }

        return <<<SVG
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="#f6f7fb"/>
<rect x="{$inset}" y="{$inset}" width="{$outer}" height="{$outer}" rx="{$radius}" fill="none" stroke="#6868ac" stroke-width="{$stroke}"/>
SVG;
    }

    private function extractInnerSvg(string $svg): string
    {
        $inner = preg_replace('/^.*?<svg[^>]*>/s', '', $svg);
        $inner = preg_replace('/<\/svg>\s*$/', '', (string) $inner);

        return trim((string) $inner);
    }

    private function scaled(int $size, int $unit): int
    {
        return (int) round(($size / 260) * $unit);
    }
}
