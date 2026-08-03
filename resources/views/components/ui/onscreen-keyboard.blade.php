@props([
    'name' => null,
    'mode' => 'numeric',
    'value' => '',
    'placeholder' => '',
    'label' => null,
    'required' => false,
    'maxlength' => null,
    'inputId' => null,
    'class' => '',
])

@php $id = $inputId ?? $name; @endphp

@once
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('onscreenKeyboard', (mode, initialValue, maxlength) => ({
                visible: false,
                inputValue: String(initialValue ?? ''),
                mode: mode || 'numeric',
                maxlength: maxlength || null,
                shifted: true,

                init() {
                    this.$watch('inputValue', () => {
                        this.$nextTick(() => {
                            const hidden = this.$el.querySelector('input[type="hidden"]');
                            if (hidden) hidden.dispatchEvent(new Event('input', { bubbles: true }));
                        });
                    });
                },

                get displayValue() {
                    if (this.mode === 'decimal') {
                        const raw = this.inputValue;
                        if (!raw) return '';
                        const parts = raw.split('.');
                        const intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        return parts.length > 1 ? intPart + ',' + parts[1] : intPart;
                    }
                    return this.inputValue;
                },

                open() { this.visible = true; this.shifted = true; },
                close() { this.visible = false; },

                toggleShift() { this.shifted = !this.shifted; },

                press(char) {
                    if (this.maxlength && this.inputValue.length >= this.maxlength) return;
                    this.inputValue += char;
                },

                backspace() {
                    this.inputValue = this.inputValue.slice(0, -1);
                },

                handleKey(key) {
                    if (key === 'backspace') this.backspace();
                    else this.press(key);
                },
            }));
        });
    </script>
    @endpush
@endonce

<div x-data="onscreenKeyboard('{{ $mode }}', '{{ $value }}', {{ $maxlength ?? 'null' }})" class="space-y-1.5 {{ $class }}">
    @if ($label)
        <label class="block text-sm font-medium text-stone-700">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <button type="button" @click="open()"
        class="input-base flex w-full items-center justify-between gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm shadow-sm"
        :class="inputValue ? 'text-stone-900' : 'text-stone-400'">
        <span x-text="displayValue || '{{ $placeholder }}'" class="truncate"></span>
        <svg class="h-4 w-4 shrink-0 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
        </svg>
    </button>

    <input type="hidden" name="{{ $name }}" id="{{ $id }}" :value="inputValue" @if ($required) required @endif>

    <template x-teleport="body">
        <div>
            <div x-show="visible"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/20 z-[998]" @click="close()">
            </div>

            <div x-show="visible"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="fixed inset-x-0 bottom-0 z-[999] bg-white rounded-t-2xl shadow-2xl px-4 pt-4 pb-8">

                <div class="bg-stone-50 rounded-xl px-4 py-3 mb-5 text-right text-xl font-bold text-stone-900 min-h-[48px]"
                    :class="mode === 'text' && !inputValue ? 'text-stone-400 text-sm' : ''"
                    x-text="displayValue || (mode === 'text' ? '{{ $placeholder }}' : '0')">
                </div>

                <template x-if="mode !== 'text'">
                    <div>
                        <div class="grid grid-cols-3 gap-2 max-w-sm mx-auto">
                            <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="n">
                                <button type="button" @click="press(String(n))"
                                    class="h-14 w-full rounded-xl text-lg font-bold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all"
                                    x-text="n">
                                </button>
                            </template>
                        </div>

                        <div class="grid grid-cols-3 gap-2 max-w-sm mx-auto mt-2">
                            <template x-if="mode === 'phone'">
                                <button type="button" @click="press('+')"
                                    class="h-14 w-full rounded-xl text-lg font-bold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all">+</button>
                            </template>
                            <template x-if="mode === 'numeric'">
                                <button type="button" @click="press('0')"
                                    class="col-span-2 h-14 rounded-xl text-lg font-bold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all">0</button>
                            </template>
                            <template x-if="mode === 'phone'">
                                <button type="button" @click="press('0')"
                                    class="h-14 w-full rounded-xl text-lg font-bold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all">0</button>
                            </template>
                            <template x-if="mode === 'decimal'">
                                <button type="button" @click="press('0')"
                                    class="h-14 w-full rounded-xl text-lg font-bold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all">0</button>
                            </template>
                            <template x-if="mode === 'decimal'">
                                <button type="button" @click="press('.')"
                                    class="h-14 w-full rounded-xl text-lg font-bold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all">,</button>
                            </template>
                            <button type="button" @click="backspace()"
                                class="h-14 w-full rounded-xl text-lg font-bold bg-amber-100 hover:bg-amber-200 active:scale-95 transition-all">⌫</button>
                        </div>
                    </div>
                </template>

                <template x-if="mode === 'text'">
                    <div class="space-y-1.5 max-w-sm mx-auto">
                        <div class="flex justify-center gap-1.5">
                            <template x-for="n in '1234567890'.split('')" :key="n">
                                <button type="button" @click="press(n)"
                                    class="h-11 w-9 rounded-lg text-sm font-semibold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all flex items-center justify-center"
                                    x-text="n"></button>
                            </template>
                        </div>
                        <div class="flex justify-center gap-1.5">
                            <template x-for="l in 'QWERTYUIOP'.split('')" :key="l">
                                <button type="button" @click="press(shifted ? l : l.toLowerCase())"
                                    class="h-11 w-9 rounded-lg text-sm font-semibold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all flex items-center justify-center"
                                    x-text="shifted ? l : l.toLowerCase()"></button>
                            </template>
                        </div>
                        <div class="flex justify-center gap-1.5">
                            <template x-for="l in 'ASDFGHJKL'.split('')" :key="l">
                                <button type="button" @click="press(shifted ? l : l.toLowerCase())"
                                    class="h-11 w-9 rounded-lg text-sm font-semibold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all flex items-center justify-center"
                                    x-text="shifted ? l : l.toLowerCase()"></button>
                            </template>
                        </div>
                        <div class="flex justify-center gap-1.5">
                            <button type="button" @click="toggleShift()"
                                class="h-11 w-11 rounded-lg text-sm font-semibold flex items-center justify-center active:scale-95 transition-all"
                                :class="shifted ? 'bg-theme-primary/15 text-theme-primary' : 'bg-stone-200 text-stone-600 hover:bg-stone-300'">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </button>
                            <template x-for="l in 'ZXCVBNM'.split('')" :key="l">
                                <button type="button" @click="press(shifted ? l : l.toLowerCase())"
                                    class="h-11 w-9 rounded-lg text-sm font-semibold bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all flex items-center justify-center"
                                    x-text="shifted ? l : l.toLowerCase()"></button>
                            </template>
                            <button type="button" @click="backspace()"
                                class="h-11 w-11 rounded-lg text-sm font-semibold bg-amber-100 hover:bg-amber-200 active:scale-95 transition-all flex items-center justify-center">⌫</button>
                        </div>
                        <div class="flex justify-center gap-1.5 pt-1">
                            <button type="button" @click="press(' ')"
                                class="h-11 w-36 rounded-lg text-sm bg-stone-100 hover:bg-stone-200 active:scale-95 transition-all flex items-center justify-center gap-1 text-stone-500">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                Spasi
                            </button>
                            <button type="button" @click="close()"
                                class="h-11 w-16 rounded-lg text-sm font-bold bg-theme-primary text-white active:scale-95 transition-all">↵</button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="close()"
                    class="mt-4 w-full max-w-sm mx-auto block rounded-xl bg-theme-primary py-3.5 text-sm font-bold text-white active:scale-[0.98] transition-all">
                    Selesai
                </button>
            </div>
        </div>
    </template>
</div>
