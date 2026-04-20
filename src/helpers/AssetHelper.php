<?php
class AssetHelper {
    public static function getAvatar($name, $size = 8) {
        $initial = strtoupper(substr($name, 0, 1));
        $colors = ['bg-primary', 'bg-secondary', 'bg-blue-500', 'bg-purple-500', 'bg-green-500', 'bg-orange-500'];
        $color = $colors[ord($initial) % count($colors)];

        return "<div class=\"h-$size w-$size rounded-lg $color/10 text-$color flex items-center justify-center font-bold text-[10px] shadow-inner\">$initial</div>";
    }
}
