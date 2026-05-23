<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Request Consultation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert sukses --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">

                    {{-- Header --}}
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-1">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Form Konsultasi</h3>
                        </div>
                        <p class="text-sm text-gray-500 ml-13">Isi form di bawah ini untuk mengajukan permintaan konsultasi kepada dokter.</p>
                    </div>

                    <hr class="mb-6">

                    {{-- Form --}}
                    <form method="POST" action="{{ route('patient.store-consultation') }}">
                        @csrf

                        {{-- Pilih Dokter --}}
                        <div class="mb-5">
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Dokter <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="doctor_id" name="doctor_id"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm text-gray-900 bg-white
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                           appearance-none cursor-pointer
                                           @error('doctor_id') border-red-500 @enderror">
                                    <option value="" disabled selected>-- Pilih Dokter --</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}"
                                            {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                            dr. {{ $doctor->name }}
                                            @if($doctor->specialization)
                                                — {{ $doctor->specialization }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Custom arrow icon --}}
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('doctor_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Keluhan --}}
                        <div class="mb-6">
                            <label for="complaint" class="block text-sm font-medium text-gray-700 mb-1">
                                Keluhan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="complaint" name="complaint" rows="5"
                                placeholder="Jelaskan keluhan atau gejala yang Anda rasakan secara detail..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                       resize-none @error('complaint') border-red-500 @enderror">{{ old('complaint') }}</textarea>
                            @error('complaint')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Semakin detail keluhan yang Anda tulis, semakin baik dokter dapat membantu Anda.</p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('patient.dashboard') }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700
                                       border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Kembali
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700
                                       text-white text-sm font-semibold rounded-lg transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Kirim Konsultasi
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
