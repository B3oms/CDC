<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Generate a consistent color for an item based on its name and ID
     */
    public static function generateItemColor($itemName, $itemId)
    {
        // Create a seed from the item name and ID for consistency
        $seed = crc32($itemName . $itemId);
        
        // Define a palette of professional colors
        $colors = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#8B5CF6', // Violet
            '#EC4899', // Pink
            '#14B8A6', // Teal
            '#F97316', // Orange
            '#6366F1', // Indigo
            '#84CC16', // Lime
            '#06B6D4', // Cyan
            '#A855F7', // Purple
            '#DC2626', // Dark Red
            '#059669', // Dark Green
            '#7C3AED', // Purple
            '#DB2777', // Pink
            '#0891B2', // Cyan
            '#EA580C', // Orange
            '#4F46E5', // Indigo
            '#BE185D', // Pink
            '#047857', // Teal
        ];
        
        // Use the seed to select a consistent color
        $index = abs($seed) % count($colors);
        return $colors[$index];
    }
    
    /**
     * Generate a color based on string hash (alternative method)
     */
    public static function generateColorFromString($string)
    {
        $hash = md5($string);
        $r = hexdec(substr($hash, 0, 2));
        $g = hexdec(substr($hash, 2, 2));
        $b = hexdec(substr($hash, 4, 2));
        
        // Ensure colors are not too light or too dark
        $r = max(50, min(200, $r));
        $g = max(50, min(200, $g));
        $b = max(50, min(200, $b));
        
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
