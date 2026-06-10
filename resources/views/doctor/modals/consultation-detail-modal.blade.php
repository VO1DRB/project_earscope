{{-- Consultation Detail Modal --}}
<div id="consultationDetailModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl text-left overflow-hidden w-full max-w-2xl animate-fade-in-up border border-slate-100">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-teal-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-teal-100 rounded-xl text-teal-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Detail Rekam Konsultasi</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Data pasien & hasil pemeriksaan otoskop AI</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Content (populated via AJAX) --}}
            <div id="modalContent" class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                    <svg class="w-8 h-8 animate-spin mb-3 text-teal-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-xs font-medium">Memuat data...</p>
                </div>
            </div>

            {{-- Footer --}}
            <div id="modalFooter" class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentConsultationId = null;

    function openDetailModal(consultationId) {
        currentConsultationId = consultationId;

        // Reset content
        $('#modalContent').html(`
            <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                <svg class="w-8 h-8 animate-spin mb-3 text-teal-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-xs font-medium">Memuat data...</p>
            </div>
        `);
        $('#modalFooter').html(`
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                Tutup
            </button>
        `);
        $('#consultationDetailModal').removeClass('hidden');

        $.ajax({
            url: '/doctor/consultation/' + consultationId + '/details',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                renderModalContent(data);
            },
            error: function() {
                $('#modalContent').html('<p class="text-rose-500 text-sm text-center py-8">Gagal memuat data. Silakan coba lagi.</p>');
            }
        });
    }

    function renderModalContent(data) {
        const statusMap = {
            'pending': ['bg-amber-50 text-amber-700 border-amber-200', 'Menunggu'],
            'approved': ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Disetujui'],
            'rejected': ['bg-rose-50 text-rose-700 border-rose-200', 'Ditolak'],
            'done': ['bg-sky-50 text-sky-700 border-sky-200', 'Selesai'],
        };
        const [statusClass, statusLabel] = statusMap[data.status] || ['bg-slate-50 text-slate-700 border-slate-200', data.status];
        const gender = data.patient?.gender === 'male' ? 'Laki-laki' : 'Perempuan';

        // ── AI Screening Section ──────────────────────────────────
        let aiSection = '';
        if (data.diagnosis) {
            const d = data.diagnosis;
            const verifiedBadge = d.is_verified
                ? `<span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-sky-50 border border-sky-200 text-sky-700">✓ Terverifikasi Dokter</span>`
                : `<span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-amber-50 border border-amber-200 text-amber-700 animate-pulse">⏳ Menunggu Verifikasi</span>`;

            // Images
            let imagesHtml = '';
            if (d.images && d.images.length > 0) {
                imagesHtml = d.images.map((img, idx) => {
                    let aiMeta = '';
                    if (img.ai_screening_result) {
                        const ai = img.ai_screening_result;
                        const confidence = ai.confidence ? (ai.confidence * 100).toFixed(1) + '%' : '-';
                        aiMeta = `<p class="text-[10px] text-slate-500 mt-1 font-medium">
                            Kelas: <span class="font-bold text-teal-700">${ai.class || '-'}</span> &bull;
                            Kepercayaan: <span class="font-bold text-teal-700">${confidence}</span>
                        </p>`;
                    }
                    return `
                        <div class="shrink-0 w-48">
                            <img src="${img.image_url}" alt="Otoskop ${idx+1}"
                                 class="w-full h-36 object-cover rounded-xl border border-slate-100 cursor-pointer hover:opacity-90 transition"
                                 onclick="window.open('${img.image_url}','_blank')" />
                            ${aiMeta}
                        </div>`;
                }).join('');
                imagesHtml = `<div class="flex gap-3 overflow-x-auto pb-2 mt-3">${imagesHtml}</div>`;
            } else {
                imagesHtml = `<p class="text-xs text-slate-400 italic mt-2">Belum ada gambar otoskop diunggah.</p>`;
            }

            const noteVal = d.notes ? d.notes : '';

            aiSection = `
                <div class="mt-5 pt-5 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Hasil Pemeriksaan AI Jetson
                        </h4>
                        ${verifiedBadge}
                    </div>

                    <div class="bg-teal-50/50 border border-teal-100 rounded-xl p-4">
                        <p class="text-[10px] text-teal-600 font-bold uppercase tracking-wider mb-1">Diagnosis Awal AI</p>
                        <p class="text-sm font-bold text-slate-800">${d.diagnosis_result}</p>
                    </div>

                    <div class="mt-3">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Foto Otoskop Telinga</p>
                        ${imagesHtml}
                    </div>
                    ${!d.is_verified && data.status !== 'done' ? `
                    <div class="mt-4 pt-4 border-t border-dashed border-slate-200">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan Akhir & Verifikasi Dokter</p>
                        <form id="verifyForm" onsubmit="submitVerification(event, ${data.id})">
                            <textarea id="doctorNotes" name="notes" rows="3" placeholder="Tuliskan catatan klinis atau resep untuk pasien..."
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent resize-none"></textarea>
                        </form>
                    </div>` : ''}
                    ${d.notes && d.is_verified ? `
                    <div class="mt-3 bg-sky-50/60 border border-sky-100 rounded-xl p-4">
                        <p class="text-[10px] text-sky-600 font-bold uppercase tracking-wider mb-1">Catatan Akhir Dokter</p>
                        <p class="text-sm text-slate-700">${d.notes || '-'}</p>
                    </div>` : ''}
                </div>`;
        } else if (data.status === 'approved') {
            aiSection = `
                <div class="mt-5 pt-5 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hasil Pemeriksaan AI Jetson</p>
                    </div>
                    <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-6 text-center">
                        <p class="text-sm text-slate-400 font-medium">Menunggu perangkat Jetson mengunggah hasil pemeriksaan otoskop...</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-dashed border-slate-200">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Selesaikan Konsultasi Secara Manual</p>
                        <form id="verifyForm" onsubmit="submitVerification(event, ${data.id})">
                            <textarea id="doctorNotes" name="notes" rows="3" placeholder="Tuliskan catatan diagnosis manual jika pemeriksaan dilakukan secara langsung..."
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent resize-none"></textarea>
                        </form>
                    </div>
                </div>`;
        }

        const content = `
            <div class="space-y-5">
                {{-- Patient Info --}}
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Informasi Pasien</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Nama</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">${data.patient?.name ?? '-'}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Usia</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">${data.patient?.age ?? '-'} Tahun</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Kontak</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">${data.patient?.contact ?? '-'}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Jenis Kelamin</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">${gender}</p>
                        </div>
                        <div class="col-span-2 bg-slate-50 rounded-xl p-3">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Alamat</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">${data.patient?.address ?? '-'}</p>
                        </div>
                    </div>
                </div>

                {{-- Complaint & Status --}}
                <div class="pt-5 border-t border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Detail Konsultasi</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 bg-slate-50 rounded-xl p-3">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Keluhan Pasien</p>
                            <p class="text-sm text-slate-700 mt-1 leading-relaxed">${data.complaint}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Status</p>
                            <span class="mt-1 inline-flex px-2 py-0.5 text-xs font-bold rounded-full border ${statusClass}">${statusLabel}</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Jadwal</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">
                                ${data.scheduled_date ? data.scheduled_date + ' · ' + data.scheduled_time : 'Belum dijadwalkan'}
                            </p>
                        </div>
                    </div>
                </div>

                ${aiSection}
            </div>`;

        $('#modalContent').html(content);

        // Show verify button in footer if applicable
        const showVerify = data.diagnosis && !data.diagnosis.is_verified && data.status !== 'done';
        const showManualVerify = !data.diagnosis && data.status === 'approved';
        if (showVerify || showManualVerify) {
            $('#modalFooter').html(`
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-xs font-semibold uppercase tracking-wider rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition mr-2">
                    Tutup
                </button>
                <button type="button" onclick="$('#verifyForm').submit()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white transition shadow-md shadow-teal-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Verifikasi & Selesaikan
                </button>`);
        }
    }

    function submitVerification(event, consultationId) {
        event.preventDefault();
        const notes = $('#doctorNotes').val();
        const btn = $('#modalFooter button:last-child');
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: '/doctor/consultation/' + consultationId + '/verify',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                notes: notes
            },
            success: function() {
                closeDetailModal();
                showNotification('Konsultasi berhasil diverifikasi & diselesaikan', 'success');
                // Update the badge in the table
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(`
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg> Verifikasi & Selesaikan`);
                showNotification('Gagal menyimpan verifikasi', 'error');
            }
        });
    }

    function closeDetailModal() {
        $('#consultationDetailModal').addClass('hidden');
        currentConsultationId = null;
    }

    // Close on overlay click handled inline
</script>
