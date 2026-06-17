@if($product->optionGroups->isNotEmpty())
    <div class="mt-6 space-y-4">
        @foreach($product->optionGroups as $group)
            <div>
                <h3 class="font-medium mb-2">{{ $group->name }}</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($group->options->where('is_active', true) as $opt)
                        <label class="border rounded-lg px-3 py-2 text-sm cursor-pointer {{ ($selectedOptions[$group->id] ?? null) === $opt->id ? 'border-kraft-600 bg-kraft-50' : 'border-gray-200' }}">
                            <input type="radio" class="hidden" wire:model.live="selectedOptions.{{ $group->id }}" value="{{ $opt->id }}">
                            {{ $opt->label }}@if($opt->price_delta > 0) <span class="text-gray-400">+{{ number_format($opt->price_delta,0,',',' ') }}</span>@endif
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif
