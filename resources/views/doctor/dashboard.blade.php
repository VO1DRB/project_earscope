<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-teal-50 rounded-xl text-teal-600">
                <svg class="w-6 h-6 animate-heartbeat" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Dashboard Dokter') }}
                </h2>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Ringkasan aktivitas diagnosis pasien dan konsultasi klinis Anda</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 animate-fade-in-up">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- STATS WIDGETS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Stats 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Konsultasi Tertunda</h3>
                        <p class="text-3xl font-extrabold text-slate-800 mt-2">{{ $pendingCount }}</p>
                    </div>
                    <div class="p-4 bg-amber-50 rounded-2xl text-amber-500 group-hover:bg-amber-100/70 transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Stats 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jadwal Hari Ini</h3>
                        <p class="text-3xl font-extrabold text-slate-800 mt-2">{{ $todayScheduleCount }}</p>
                    </div>
                    <div class="p-4 bg-teal-50 rounded-2xl text-teal-600 group-hover:bg-teal-100/70 transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <!-- Stats 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pasien Ditangani</h3>
                        <p class="text-3xl font-extrabold text-slate-800 mt-2">{{ $patientsHandledCount }}</p>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-2xl text-emerald-600 group-hover:bg-emerald-100/70 transition-colors">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

            </div>

            <!-- HISTORY TABLE -->
            <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">Riwayat Pemeriksaan Pasien</h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Daftar lengkap sesi konsultasi yang telah dikelola</p>
                    </div>
                    <a href="{{ route('doctor.consultations') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 hover:text-teal-700 transition">
                        Lihat Permintaan Konsultasi
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if($histories->isEmpty())
                    <div class="text-center py-16 px-6">
                        <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3 animate-float">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-700">Tidak Ada Riwayat Konsultasi</h4>
                        <p class="text-xs text-slate-400 mt-1">Belum ada pasien yang selesai ditangani di klinik Anda.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-4">Pasien</th>
                                    <th class="px-6 py-4">Keluhan Diagnosis</th>
                                    <th class="px-6 py-4">Tanggal Penanganan</th>
                                    <th class="px-6 py-4">Status Layanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($histories as $history)
                                    <tr class="hover:bg-slate-50/40 transition-colors duration-250">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-9 w-9 rounded-xl bg-teal-50 flex items-center justify-center shrink-0 border border-teal-100/30">
                                                    <span class="text-teal-700 text-xs font-bold">
                                                        {{ strtoupper(substr($history->patient->name ?? 'P', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-800">
                                                        {{ $history->patient->name ?? '-' }}
                                                    </span>
                                                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">ID: #C-00{{ $history->id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600 font-medium max-w-xs truncate">
                                            {{ Str::limit($history->complaint, 55) }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 font-medium">
                                            {{ $history->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusMap = [
                                                    'pending'  => ['label' => 'Menunggu', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/50'],
                                                    'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50'],
                                                    'rejected' => ['label' => 'Ditolak',   'class' => 'bg-rose-50 text-rose-700 border-rose-200/50'],
                                                    'done'     => ['label' => 'Selesai',   'class' => 'bg-sky-50 text-sky-700 border-sky-200/50'],
                                                ];
                                                $s = $statusMap[$history->status] ?? ['label' => ucfirst($history->status), 'class' => 'bg-slate-50 text-slate-700 border-slate-200/50'];
                                            @endphp
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $s['class'] }}">
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
</x-app-layout>