@props(['size' => 36])
{{-- Icône BePack : boîte isométrique avec ruban coral montant (envoi & croissance) --}}
<svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="BePack">
    <!-- face gauche -->
    <path d="M6 16 L24 26 L24 45 L6 35 Z" fill="#945F2C"/>
    <!-- face droite -->
    <path d="M24 26 L42 16 L42 35 L24 45 Z" fill="#B5793A"/>
    <!-- face dessus -->
    <path d="M24 4 L42 14 L24 24 L6 14 Z" fill="#D9A066"/>
    <!-- ruban coral (dessus) -->
    <path d="M24 4 L31 8 L24 12 L17 8 Z" fill="#FF5A3C"/>
    <!-- ruban coral vertical (avant) -->
    <path d="M21.5 19 L26.5 19 L26.5 42 L24 43.5 L21.5 42 Z" fill="#FF5A3C"/>
    <!-- flèche montante (croissance) sur la face droite -->
    <path d="M30 30 L35 27 L35 33 Z" fill="#FFE2DB" opacity="0.9"/>
</svg>
