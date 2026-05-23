<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patient Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash success --}}
            @if(session('success'))
                <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-300 text-green-800 px-5 py-3 rounded-lg">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Header tabel --}}
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">{{ __('My Consultations') }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Riwayat permintaan konsultasi Anda</p>
                        </div>
                        <!-- <a href="{{ route('patient.create-consultation') }}"
                            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Request Consultation
                        </a> -->
                    </div>

                    @if($consultations->isEmpty())
                        {{-- Empty state --}}
                        <div class="text-center py-14">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-3 text-sm text-gray-500">Belum ada konsultasi yang diajukan.</p>
                            <a href="{{ route('patient.create-consultation') }}"
                                class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                                Ajukan Konsultasi Sekarang
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keluhan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($consultations as $i => $consultation)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $i + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                                        <span class="text-indigo-700 text-xs font-bold">
                                                            {{ strtoupper(substr($consultation->doctor->name ?? 'D', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900">
                                                        dr. {{ $consultation->doctor->name ?? '-' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                                                {{ Str::limit($consultation->complaint, 60) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($consultation->scheduled_date)
                                                    <div class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('d M Y') }}</div>
                                                    <div class="text-xs text-gray-400">{{ $consultation->scheduled_time ?? '' }}</div>
                                                @else
                                                    <span class="text-xs text-yellow-600 italic">Menunggu persetujuan</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusMap = [
                                                        'pending'  => ['label' => 'Pending',   'class' => 'bg-yellow-100 text-yellow-800'],
                                                        'approved' => ['label' => 'Disetujui', 'class' => 'bg-green-100 text-green-800'],
                                                        'rejected' => ['label' => 'Ditolak',   'class' => 'bg-red-100 text-red-800'],
                                                        'done'     => ['label' => 'Selesai',   'class' => 'bg-blue-100 text-blue-800'],
                                                    ];
                                                    $s = $statusMap[$consultation->status] ?? ['label' => ucfirst($consultation->status), 'class' => 'bg-gray-100 text-gray-800'];
                                                @endphp
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $s['class'] }}">
                                                    {{ $s['label'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
