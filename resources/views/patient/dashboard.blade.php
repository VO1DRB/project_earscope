<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-teal-50 rounded-xl text-teal-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">{{ __('Dashboard Pasien') }}</h2>
                    <p class="text-xs font-medium text-slate-400 mt-0.5">Kelola janji temu dan hasil pemeriksaan medis telinga Anda</p>
                </div>
            </div>
            <div>
                <a href="{{ route('patient.create-consultation') }}"
                    class="relative inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-semibold py-2.5 px-5 rounded-xl text-sm transition-all duration-300 shadow-md shadow-teal-500/20 hover:shadow-teal-500/35 transform hover:-translate-y-0.5 active:translate-y-0 animate-pulse-ring">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Konsultasi Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 animate-fade-in-up">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash success --}}
            @if(session('success'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm animate-bounce">
                    <div class="p-1 bg-emerald-500 text-white rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- PROFILE SUMMARY WIDGET -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-500 flex items-center justify-center text-white font-bold text-2xl shadow-md shadow-teal-500/10">
                        {{ strtoupper(substr(Auth::user()->username, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-teal-600 uppercase tracking-wider">Pasien Terdaftar</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ Auth::user()->username }}</h3>
                        <p class="text-xs text-slate-400 font-medium">Melindungi kesehatan telinga Anda sejak {{ Auth::user()->created_at->format('M Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-6 divide-x divide-slate-100 w-full md:w-auto">
                    <div class="px-4 text-center md:text-left flex-1 md:flex-none">
                        <p class="text-xs text-slate-400 font-medium">Total Konsultasi</p>
                        <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $consultations->count() }}</p>
                    </div>
                    <div class="px-6 text-center md:text-left flex-1 md:flex-none">
                        <p class="text-xs text-slate-400 font-medium">Disetujui</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $consultations->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="px-6 text-center md:text-left flex-1 md:flex-none">
                        <p class="text-xs text-slate-400 font-medium">Selesai</p>
                        <p class="text-2xl font-bold text-sky-600 mt-0.5">{{ $consultations->where('status', 'done')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- TABLE CARD -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-lg text-slate-800">{{ __('Riwayat Konsultasi Medis') }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Pantau status persetujuan, jadwal, dan hasil pemeriksaan dokter Anda</p>
                </div>

                @if($consultations->isEmpty())
                    <div class="text-center py-20 px-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-4 animate-float">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-700">Belum Ada Pengajuan Konsultasi</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Anda belum mengajukan sesi pemeriksaan telinga ke dokter manapun.</p>
                        <a href="{{ route('patient.create-consultation') }}"
                            class="mt-5 inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold py-2.5 px-5 rounded-xl transition shadow-sm">
                            Buat Pengajuan Pertama
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-4">No</th>
                                    <th class="px-6 py-4">Dokter Spesialis</th>
                                    <th class="px-6 py-4">Keluhan Utama</th>
                                    <th class="px-6 py-4">Jadwal Sesi</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Hasil Diagnosis</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($consultations as $i => $consultation)
                                    <tr class="hover:bg-slate-50/40 transition-colors duration-250">
                                        <td class="px-6 py-4 text-slate-400 font-medium">{{ $i + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-9 w-9 rounded-xl bg-teal-50 flex items-center justify-center shrink-0 border border-teal-100/30">
                                                    <span class="text-teal-700 text-xs font-bold">
                                                        {{ strtoupper(substr($consultation->doctor->name ?? 'D', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-800">dr. {{ $consultation->doctor->name ?? '-' }}</span>
                                                    <p class="text-[10px] text-teal-600 font-semibold mt-0.5">{{ $consultation->doctor->specialization ?? 'Umum' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600 font-medium max-w-xs truncate">
                                            {{ Str::limit($consultation->complaint, 50) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($consultation->scheduled_date)
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('d M Y') }}</span>
                                                    <span class="text-[11px] text-slate-400 font-medium">{{ $consultation->scheduled_time ?? '' }} WIB</span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-[11px] font-semibold text-amber-600 border border-amber-100/50 italic">
                                                    <svg class="w-3.5 h-3.5 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Menunggu Jadwal
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusMap = [
                                                    'pending'  => ['label' => 'Menunggu', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/50'],
                                                    'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50'],
                                                    'rejected' => ['label' => 'Ditolak',   'class' => 'bg-rose-50 text-rose-700 border-rose-200/50'],
                                                    'done'     => ['label' => 'Selesai',   'class' => 'bg-sky-50 text-sky-700 border-sky-200/50'],
                                                ];
                                                $s = $statusMap[$consultation->status] ?? ['label' => ucfirst($consultation->status), 'class' => 'bg-slate-50 text-slate-700 border-slate-200/50'];
                                            @endphp
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $s['class'] }}">
                                                {{ $s['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($consultation->status === 'done' && $consultation->diagnosis)
                                                <button type="button"
                                                    onclick="openPatientDiagnosisModal({{ $consultation->id }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 text-white text-[11px] font-bold uppercase tracking-wider shadow-sm hover:shadow-teal-500/20 hover:-translate-y-0.5 transition-all duration-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Lihat Hasil
                                                </button>
                                            @elseif($consultation->status === 'approved' && $consultation->diagnosis)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-bold rounded-md border bg-violet-50 border-violet-200/60 text-violet-700 animate-pulse">
                                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                                    Menunggu Verifikasi
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-300 font-medium italic">—</span>
                                            @endif
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

    {{-- ── Patient Diagnosis Result Modal ──────────────────────────────────────── --}}
    <div id="patientDiagnosisModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePatientDiagnosisModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl border border-slate-100 animate-fade-in-up overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-teal-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-teal-100 rounded-xl text-teal-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Hasil Diagnosis Pemeriksaan</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Laporan resmi dari dokter & sistem AI EarScope Jetson</p>
                        </div>
                    </div>
                    <button onclick="closePatientDiagnosisModal()" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="patientDiagnosisContent" class="px-6 py-5 max-h-[70vh] overflow-y-auto space-y-5"></div>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                    <button onclick="closePatientDiagnosisModal()" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        @php
            $consultationData = $consultations->map(function($c) {
                $d = $c->diagnosis;
                $images = [];
                if ($d) {
                    foreach ($d->images as $img) {
                        $images[] = [
                            'image_url'           => asset('storage/' . $img->image_path),
                            'ai_screening_result' => $img->ai_screening_result,
                        ];
                    }
                }
                return [
                    'id'          => $c->id,
                    'complaint'   => $c->complaint,
                    'status'      => $c->status,
                    'doctor_name' => optional($c->doctor)->name,
                    'diagnosis'   => $d ? [
                        'diagnosis_result' => $d->diagnosis_result,
                        'notes'            => $d->notes,
                        'is_verified'      => $d->is_verified,
                        'images'           => $images,
                    ] : null,
                ];
            })->values()->all();
        @endphp
        const patientConsultations = {!! json_encode($consultationData) !!};

        function openPatientDiagnosisModal(consultationId) {
            const c = patientConsultations.find(x => x.id == consultationId);
            if (!c || !c.diagnosis) return;
            const d = c.diagnosis;

            let imagesHtml = '';
            if (d.images && d.images.length > 0) {
                imagesHtml = d.images.map((img, idx) => {
                    let aiMeta = '';
                    if (img.ai_screening_result) {
                        const ai = img.ai_screening_result;
                        const confidence = ai.confidence ? (ai.confidence * 100).toFixed(1) + '%' : '-';
                        aiMeta = `<p class="text-[10px] text-slate-500 mt-1.5 font-medium">
                            Kelas AI: <strong class="text-teal-700">${ai.class || '-'}</strong>
                            &bull; Kepercayaan: <strong class="text-teal-700">${confidence}</strong></p>`;
                    }
                    return `<div class="shrink-0 w-44">
                        <img src="${img.image_url}" alt="Otoskop ${idx+1}"
                             class="w-full h-32 object-cover rounded-xl border border-slate-100 cursor-pointer hover:opacity-90 transition"
                             onclick="window.open('${img.image_url}','_blank')" />
                        ${aiMeta}
                    </div>`;
                }).join('');
                imagesHtml = `<div class="flex gap-3 overflow-x-auto pb-1">${imagesHtml}</div>`;
            } else {
                imagesHtml = `<p class="text-xs text-slate-400 italic">Tidak ada foto otoskop tersedia.</p>`;
            }

            const verifiedBadge = d.is_verified
                ? `<span class="px-2 py-0.5 text-[10px] font-bold rounded-md border bg-sky-50 border-sky-200 text-sky-700">✓ Terverifikasi Dokter</span>`
                : `<span class="px-2 py-0.5 text-[10px] font-bold rounded-md border bg-amber-50 border-amber-200 text-amber-700">Menunggu Verifikasi</span>`;

            document.getElementById('patientDiagnosisContent').innerHTML = `
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dokter Pemeriksa</p>
                    <p class="text-sm font-bold text-slate-800">dr. ${c.doctor_name || '-'}</p>
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Keluhan Awal</p>
                    <p class="text-sm text-slate-700 leading-relaxed">${c.complaint}</p>
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hasil Diagnosis AI EarScope</p>
                        ${verifiedBadge}
                    </div>
                    <div class="bg-teal-50/60 border border-teal-100 rounded-xl p-4 mb-3">
                        <p class="text-[10px] text-teal-600 font-bold uppercase tracking-wider mb-1">Klasifikasi Kondisi Telinga</p>
                        <p class="text-base font-bold text-slate-800">${d.diagnosis_result}</p>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Foto Otoskop Telinga</p>
                    ${imagesHtml}
                </div>
                ${d.notes ? `<div class="pt-4 border-t border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Catatan & Rekomendasi Dokter</p>
                    <div class="bg-sky-50/60 border border-sky-100 rounded-xl p-4">
                        <p class="text-sm text-slate-700 leading-relaxed">${d.notes}</p>
                    </div>
                </div>` : ''}
            `;
            document.getElementById('patientDiagnosisModal').classList.remove('hidden');
        }

        function closePatientDiagnosisModal() {
            document.getElementById('patientDiagnosisModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
