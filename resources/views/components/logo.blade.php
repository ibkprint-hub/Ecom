@props(['size' => 36, 'dark' => false])
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <x-brand-mark :size="$size" />
    <span class="font-display font-extrabold leading-none {{ $dark ? 'text-white' : 'text-ink' }}" style="font-size: {{ round($size*0.62) }}px">Be<span style="color: var(--bp-accent)">Pack</span></span>
</span>
