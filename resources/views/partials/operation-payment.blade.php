<section class="rounded-2xl border border-black/5 bg-white shadow-sm">
    <div class="border-b border-black/5 px-6 py-5">
        <h2 class="text-lg font-bold text-zinc-900">Forma de pago</h2>
        <p class="mt-1 text-sm text-zinc-500">
            El registro se realiza en dólares. La tasa es necesaria solo para pagos en bolívares o mixtos.
        </p>
    </div>

    <div class="grid gap-5 p-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="payment_method" class="mb-2 block text-sm font-semibold text-gray-700">
                Forma de pago <span class="text-[#E46F8A]">*</span>
            </label>
            <select id="payment_method" name="payment_method" x-model="paymentMethod"
                x-on:change="changePaymentMethod()"
                class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                required>
                <option value="">Seleccionar forma de pago</option>
                @foreach ([
                    'efectivo_usd' => 'Efectivo USD',
                    'zelle' => 'Zelle',
                    'binance' => 'Binance',
                    'pago_movil' => 'Pago móvil',
                    'transferencia_bs' => 'Transferencia Bs',
                    'mixto' => 'Mixto',
                ] as $value => $label)
                    <option value="{{ $value }}" @selected($selectedPaymentMethod === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <p x-show="!showRate" x-cloak class="md:col-span-2 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-800">
            Puedes guardar esta operación en dólares sin registrar una tasa.
        </p>

        <div x-show="showRate" x-cloak class="md:col-span-2">
            <label for="exchange_rate_choice" class="mb-2 block text-sm font-semibold text-gray-700">
                Tasa registrada para la {{ $operationLabel }}
                <span x-show="requiresRate" class="text-[#E46F8A]">*</span>
            </label>
            <select id="exchange_rate_choice" name="exchange_rate_choice" x-model="exchangeRateChoice"
                :required="requiresRate"
                class="w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10">
                <option value="" x-text="requiresRate ? 'Seleccionar tasa registrada' : 'Sin tasa de cambio'">Sin tasa de cambio</option>
                <template x-for="choice in ratesForSelectedDate" :key="choice.key">
                    <option :value="choice.key" x-text="formatRateChoice(choice)"></option>
                </template>
            </select>

            <div x-show="requiresRate && ratesForSelectedDate.length === 0" x-cloak
                class="mt-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3">
                <p class="text-sm font-semibold text-red-700">No existen tasas para esta fecha.</p>
                <p class="mt-1 text-xs text-red-600">Registra una tasa correspondiente al día de la operación.</p>
                <a href="{{ route('exchange-rates.create') }}" class="mt-2 inline-flex text-xs font-semibold text-red-700 underline">Registrar una tasa</a>
            </div>
            <p x-show="!requiresRate && selectedRateChoice" x-cloak class="mt-2 text-xs text-zinc-500">
                Se conserva la tasa histórica de esta operación. Puedes seleccionar «Sin tasa de cambio» si corresponde.
            </p>
        </div>

        <div x-show="showRate" x-cloak>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Fuente seleccionada</label>
            <input type="text" :value="selectedRateChoice?.source_label || ''" readonly
                class="w-full cursor-not-allowed rounded-xl border border-black/10 bg-zinc-100 px-4 py-3 text-sm text-zinc-600 outline-none">
        </div>
        <div x-show="showRate" x-cloak>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Tasa aplicada</label>
            <input type="text" :value="selectedRateChoice ? formatRate(selectedRateChoice.value) : ''" readonly
                class="w-full cursor-not-allowed rounded-xl border border-black/10 bg-zinc-100 px-4 py-3 text-sm font-semibold text-zinc-700 outline-none">
        </div>
    </div>
</section>
