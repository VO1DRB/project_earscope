<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-teal-50 rounded-xl text-teal-600">
                <svg class="w-6 h-6 animate-heartbeat" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Daftar Permintaan Konsultasi') }}
                </h2>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Kelola janji temu dan diagnosa keluhan telinga pasien</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 animate-fade-in-up">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="p-6">                    
                    <!-- Status Filter Tabs -->
                    <div class="mb-6 flex flex-wrap gap-2 border-b border-slate-100 pb-5">
                        <a href="{{ route('doctor.consultations', ['status' => 'all']) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $status === 'all' ? 'bg-teal-50 border border-teal-200/50 text-teal-700' : 'bg-slate-50 border border-slate-100 text-slate-500 hover:bg-slate-100/70 hover:text-slate-700' }}">
                            Semua
                        </a>
                        <a href="{{ route('doctor.consultations', ['status' => 'pending']) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $status === 'pending' ? 'bg-amber-50 border border-amber-200/50 text-amber-700' : 'bg-slate-50 border border-slate-100 text-slate-500 hover:bg-slate-100/70 hover:text-slate-700' }}">
                            Menunggu
                        </a>
                        <a href="{{ route('doctor.consultations', ['status' => 'approved']) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $status === 'approved' ? 'bg-emerald-50 border border-emerald-200/50 text-emerald-700' : 'bg-slate-50 border border-slate-100 text-slate-500 hover:bg-slate-100/70 hover:text-slate-700' }}">
                            Disetujui
                        </a>
                        <a href="{{ route('doctor.consultations', ['status' => 'rejected']) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $status === 'rejected' ? 'bg-rose-50 border border-rose-200/50 text-rose-700' : 'bg-slate-50 border border-slate-100 text-slate-500 hover:bg-slate-100/70 hover:text-slate-700' }}">
                            Ditolak
                        </a>
                        <a href="{{ route('doctor.consultations', ['status' => 'done']) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition {{ $status === 'done' ? 'bg-sky-50 border border-sky-200/50 text-sky-700' : 'bg-slate-50 border border-slate-100 text-slate-500 hover:bg-slate-100/70 hover:text-slate-700' }}">
                            Selesai
                        </a>
                    </div>
                    
                    @if($consultations->isEmpty())
                        <div class="text-center py-16 px-6">
                            <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3 animate-float">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-slate-700">Tidak Ada Permintaan</h4>
                            <p class="text-xs text-slate-400 mt-1">Belum ada pasien yang mengajukan sesi konsultasi untuk status ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th scope="col" class="px-6 py-4">Nama Pasien</th>
                                        <th scope="col" class="px-6 py-4">Kontak / HP</th>
                                        <th scope="col" class="px-6 py-4">Keluhan Diagnosis</th>
                                        <th scope="col" class="px-6 py-4">Status Layanan</th>
                                        <th scope="col" class="px-6 py-4">Jadwal Sesi</th>
                                        <th scope="col" class="px-6 py-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($consultations as $consultation)
                                        <tr id="row-{{ $consultation->id }}" class="hover:bg-slate-50/40 transition-colors duration-250">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-9 w-9 rounded-xl bg-teal-50 flex items-center justify-center shrink-0 border border-teal-100/30">
                                                        <span class="text-teal-700 text-xs font-bold">
                                                            {{ strtoupper(substr($consultation->patient->name ?? 'P', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-slate-800">
                                                            {{ $consultation->patient->name ?? 'N/A' }}
                                                        </span>
                                                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Umur: {{ $consultation->patient->age ?? '-' }} Tahun</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 font-medium whitespace-nowrap">
                                                {{ $consultation->patient->contact ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 font-medium max-w-xs truncate">
                                                {{ Str::limit($consultation->complaint, 45) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusMap = [
                                                        'pending'  => ['label' => 'Menunggu', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/50'],
                                                        'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50'],
                                                        'rejected' => ['label' => 'Ditolak',   'class' => 'bg-rose-50 text-rose-700 border-rose-200/50'],
                                                        'done'     => ['label' => 'Selesai',   'class' => 'bg-sky-50 text-sky-700 border-sky-200/50'],
                                                    ];
                                                    $s = $statusMap[$consultation->status] ?? ['label' => ucfirst($consultation->status), 'class' => 'bg-slate-50 text-slate-700 border-slate-200/50'];
                                                    $hasDiagnosis = $consultation->diagnosis !== null;
                                                    $aiReady = $hasDiagnosis && !$consultation->diagnosis->is_verified && $consultation->status !== 'done';
                                                @endphp
                                                <div class="flex flex-col gap-1">
                                                    <span id="status-{{ $consultation->id }}" class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $s['class'] }}">
                                                        {{ $s['label'] }}
                                                    </span>
                                                    @if($aiReady)
                                                        <span class="px-2 py-0.5 inline-flex items-center gap-1 text-[10px] font-bold rounded-md border bg-violet-50 border-violet-200/60 text-violet-700 animate-pulse">
                                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                                            AI Hasil Ready
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($consultation->scheduled_date)
                                                    <div class="flex flex-col">
                                                        <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('d M Y') }}</span>
                                                        <span class="text-[11px] text-slate-400 font-medium">{{ $consultation->scheduled_time }} WIB</span>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400 font-medium italic">Belum dijadwalkan</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold uppercase tracking-wider space-x-2.5">
                                                <button type="button" onclick="openDetailModal('{{ $consultation->id }}')" class="text-teal-600 hover:text-teal-700 transition">
                                                    Detail
                                                </button>

                                                @if($consultation->status === 'pending')
                                                    <button type="button"
                                                        onclick="openScheduleModal('{{ $consultation->id }}', true)"
                                                        class="text-emerald-600 hover:text-emerald-700 transition">
                                                        Setujui
                                                    </button>
                                                    <button type="button" onclick="rejectConsultation('{{ $consultation->id }}')" class="text-rose-600 hover:text-rose-700 transition">
                                                        Tolak
                                                    </button>
                                                @endif

                                                @if($consultation->status === 'approved')
                                                    <button type="button" onclick="openScheduleModal('{{ $consultation->id }}', false)" class="text-amber-500 hover:text-amber-600 transition">
                                                        Jadwalkan Ulang
                                                    </button>
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
    </div>

    <!-- Include Modals -->
    @include('doctor.modals.consultation-detail-modal')
    @include('doctor.modals.schedule-modal')

    <script>
        // Approve -> delegate ke schedule modal
        function approveConsultation(consultationId) {
            openScheduleModal(consultationId, true);
        }

        // Reject Consultation
        function rejectConsultation(consultationId) {
            if (confirm('Apakah Anda yakin ingin menolak konsultasi ini?')) {
                $.ajax({
                    url: '/doctor/consultation/' + consultationId + '/reject',
                    type: 'POST',
                    data: {
                        _token: $('[name="_token"]').val()
                    },
                    success: function(response) {
                        // Update status badge
                        $('#status-' + consultationId)
                            .removeClass('bg-amber-50 text-amber-700 border-amber-200/50')
                            .addClass('bg-rose-50 text-rose-700 border-rose-200/50')
                            .text('Ditolak');
                        
                        // Remove action buttons
                        let row = $('#row-' + consultationId);
                        row.find('button').each(function() {
                            if ($.trim($(this).text()) !== 'Detail') {
                                $(this).remove();
                            }
                        });
                        
                        showNotification('Konsultasi berhasil ditolak', 'success');
                    },
                    error: function(xhr) {
                        showNotification('Gagal menolak konsultasi', 'error');
                    }
                });
            }
        }

        // Show Notification
        function showNotification(message, type) {
            let bgColor = type === 'success' ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200';
            let textColor = type === 'success' ? 'text-emerald-800' : 'text-rose-800';
            
            let notification = `
                <div class="fixed top-4 right-4 rounded-xl border ${bgColor} p-4 shadow-lg z-50 animate-fade-in-up">
                    <p class="text-xs font-semibold ${textColor}">${message}</p>
                </div>
            `;
            
            $('body').append(notification);
            
            setTimeout(function() {
                $('body').find('.fixed.top-4').remove();
            }, 3000);
        }

        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
</x-app-layout>