<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $statePath = $getStatePath();
        $countryKey = $getCountryStatePath();
        $numberKey = $getNumberStatePath();
        $includePlus = $shouldIncludeLeadingPlus();
        $countries = \App\Support\CountryZoneHelper::getAll();
    @endphp

    <div 
        x-data="{
            countryIso: $wire.entangle('data.{{ $countryKey }}'),
            localNumber: $wire.entangle('data.{{ $numberKey }}'),
            fullPhone: $wire.entangle('data.{{ $statePath }}'),
            includePlus: {{ $includePlus ? 'true' : 'false' }},
            countries: @js($countries),
            get currentDialCode() {
                const found = this.countries.find(c => c.iso === this.countryIso);
                return found ? found.code : '+62';
            },
            sync() {
                const cleanDial = this.currentDialCode.replace(/\D/g, '');
                let num = (this.localNumber || '').toString().replace(/\D/g, '');
                if (num.startsWith('0')) {
                    num = num.replace(/^0+/, '');
                    this.localNumber = num;
                }
                if (!num) {
                    this.fullPhone = '';
                    return;
                }
                this.fullPhone = this.includePlus ? ('+' + cleanDial + num) : (cleanDial + num);
            }
        }"
        x-init="sync(); $watch('countryIso', () => sync()); $watch('localNumber', () => sync())"
        class="w-full space-y-1.5"
    >
        {{-- ALL-IN-ONE CONTIGUOUS INPUT BAR (SATU AREA TERPADU) --}}
        <div class="flex flex-wrap sm:flex-nowrap items-stretch border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded-none shadow-xs overflow-hidden focus-within:ring-1 focus-within:ring-emerald-500 focus-within:border-emerald-500 transition">
            
            {{-- 1. Country Zone Selector (Left) --}}
            <div class="relative shrink-0 border-b sm:border-b-0 border-r border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 flex items-center w-full sm:w-auto min-w-[200px] max-w-full sm:max-w-[240px]">
                <select 
                    x-model="countryIso"
                    @change="sync()"
                    class="w-full h-full py-2.5 pl-3 pr-7 text-xs font-mono bg-transparent border-0 text-zinc-900 dark:text-zinc-100 focus:ring-0 cursor-pointer appearance-none outline-none"
                    style="background: transparent;"
                >
                    <template x-for="c in countries" :key="c.iso">
                        <option :value="c.iso" x-text="c.emoji + ' ' + c.name + ' (' + c.code + ')'" :selected="c.iso === countryIso" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"></option>
                    </template>
                </select>
                <div class="pointer-events-none absolute right-2 text-zinc-400 text-[10px]">
                    ▼
                </div>
            </div>

            {{-- 2. Dial Code Indicator --}}
            <div class="px-2.5 py-2.5 flex items-center font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-zinc-100/70 dark:bg-zinc-900/70 border-r border-zinc-200 dark:border-zinc-800 select-none shrink-0" x-text="currentDialCode">
                +62
            </div>

            {{-- 3. Local Number Text Input (Center) --}}
            <input 
                type="text"
                x-model="localNumber"
                @input="sync()"
                placeholder="81380448253"
                class="flex-1 min-w-[140px] py-2.5 px-3 text-xs font-mono bg-transparent border-0 text-zinc-900 dark:text-zinc-100 focus:ring-0 placeholder:text-zinc-400 outline-none"
            />

            {{-- 4. Integrated E.164 Final Result Badge (Right Edge) --}}
            <div class="px-3 py-2 sm:py-0 flex items-center justify-between sm:justify-end gap-2 bg-zinc-50 dark:bg-zinc-950 border-t sm:border-t-0 border-l border-zinc-200 dark:border-zinc-800 shrink-0 select-none w-full sm:w-auto">
                <span class="text-[9px] font-mono text-zinc-500 dark:text-zinc-400 uppercase tracking-wider font-semibold">E.164:</span>
                <span 
                    class="px-2 py-0.5 font-mono text-xs font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 rounded-none tracking-tight"
                    x-text="fullPhone || '—'"
                >
                </span>
            </div>
        </div>
    </div>
</x-dynamic-component>
