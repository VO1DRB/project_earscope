<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Diagnoses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- AVAILABLE CONSULTATIONS -->
            <div id="consultationsSection" class="bg-white shadow rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Available Consultations</h3>

                @if($consultations->isEmpty())
                    <p class="text-gray-500">No scheduled consultations available for diagnosis.</p>
                @else
                    <div class="mb-4">
                        <div class="flex items-center gap-3">
                            <input id="searchConsultations" type="text" placeholder="Search patient, complaint, or date..."
                                class="block w-full md:w-96 px-3 py-2 border rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
                            <button type="button" onclick="clearSearch()"
                                class="ml-2 inline-flex items-center px-3 py-2 bg-gray-100 text-sm rounded-md hover:bg-gray-200">Clear</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-2">ID</th>
                                    <th class="px-4 py-2">Patient</th>
                                    <th class="px-4 py-2">Complaint</th>
                                    <th class="px-4 py-2">Scheduled</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($consultations as $consultation)
                                    <tr>
                                        <td class="px-4 py-2 text-sm font-mono font-bold text-indigo-700 bg-indigo-50 rounded">
                                            #{{ $consultation->id }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $consultation->patient->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ Str::limit($consultation->complaint, 50) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('d M Y') }} {{ $consultation->scheduled_time }}</td>
                                        <td class="px-4 py-2 text-sm text-green-800">{{ ucfirst($consultation->status) }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <button type="button" onclick="openDiagnosisForm('{{ $consultation->id }}')" class="text-indigo-600 hover:text-indigo-900 underline font-medium">
                                                Add Diagnosis
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- DIAGNOSIS FORM SECTION -->
            <div id="diagnosisFormSection" class="bg-white shadow rounded-lg p-6" style="display: none;">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-bold text-lg">Submit Diagnosis</h3>
                    <button type="button" onclick="closeDiagnosisForm()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- CONSULTATION DETAILS -->
                <div id="consultationDetails" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Patient Name</p>
                            <p class="mt-1 text-sm font-medium text-gray-900" id="detailPatientName">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Age</p>
                            <p class="mt-1 text-sm font-medium text-gray-900" id="detailPatientAge">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</p>
                            <p class="mt-1 text-sm font-medium text-gray-900" id="detailPatientGender">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</p>
                            <p class="mt-1 text-sm font-medium text-gray-900" id="detailPatientEmail">-</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint</p>
                            <p class="mt-1 text-sm text-gray-900" id="detailComplaint">-</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</p>
                            <p class="mt-1 text-sm text-gray-900" id="detailScheduled">-</p>
                        </div>
                    </div>
                </div>

                <!-- DIAGNOSIS FORM -->
                <form id="diagnosisForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="diagnosisConsultationId" name="consultation_request_id">

                    {{-- Banner ID Konsultasi untuk diinput ke Flask --}}
                    <div id="consultationIdBanner" class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg flex items-center gap-3">
                        <div>
                            <p class="text-xs text-indigo-500 font-medium uppercase tracking-wider">ID Konsultasi (untuk diketik di Flask App)</p>
                            <p class="text-2xl font-bold font-mono text-indigo-700" id="bannerConsultationId">—</p>
                        </div>
                        <button type="button" onclick="copyConsultationId()"
                            class="ml-auto flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Salin
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Processed Video from Earscope -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Video Pemeriksaan Telinga
                                <span id="pollingBadge" class="ml-2 inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                                    <span class="animate-pulse w-2 h-2 rounded-full bg-yellow-500 inline-block"></span>
                                    Menunggu data earscope...
                                </span>
                            </label>
                            <div id="earVideoContainer" class="mt-2 p-4 border-2 border-dashed border-gray-300 rounded-lg text-center bg-gray-50 min-h-[120px] flex items-center justify-center">
                                <p class="text-sm text-gray-400">Video hasil pemeriksaan akan muncul otomatis setelah perangkat earscope mengirim data.</p>
                            </div>
                        </div>

                        <!-- AI Screening Result -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hasil Deteksi AI (Earscope)</label>
                            <div id="aiResultContainer" class="mt-2 p-4 border border-gray-300 rounded-lg bg-gray-50 min-h-[52px] flex items-center">
                                <p class="text-sm text-gray-400 italic" id="aiResultPlaceholder">Menunggu hasil deteksi dari earscope...</p>
                                <p class="text-sm font-semibold text-indigo-700 hidden" id="aiResultText"></p>
                            </div>
                        </div>

                        <!-- Diagnosis Result -->
                        <div>
                            <label for="diagnosis_result" class="block text-sm font-medium text-gray-700">Diagnosis Result</label>
                            <textarea id="diagnosis_result" name="diagnosis_result" rows="4" required class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border"></textarea>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Submit Diagnosis
                        </button>
                        <button type="button" onclick="closeDiagnosisForm()" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-800 hover:bg-gray-300">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        // ============================================================
        // POLLING: Cek hasil earscope setiap 5 detik
        // ============================================================
        let pollingInterval = null;
        let earscopeLoaded  = false;

        function startEarscopePolling(consultationId) {
            earscopeLoaded = false;
            // Cek langsung, lalu tiap 5 detik
            fetchEarscopeResult(consultationId);
            pollingInterval = setInterval(function () {
                fetchEarscopeResult(consultationId);
            }, 5000);
        }

        function stopEarscopePolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        function fetchEarscopeResult(consultationId) {
            if (earscopeLoaded) return;

            $.ajax({
                url: '/api/earscope/latest-result?consultation_id=' + consultationId,
                type: 'GET',
                success: function (data) {
                    if (data.success) {
                        earscopeLoaded = true;
                        stopEarscopePolling();
                        renderEarscopeResult(data);
                    }
                },
                error: function () {
                    // 404 = belum ada data, lanjut polling
                }
            });
        }

        function renderEarscopeResult(data) {
            // --- Badge status ---
            $('#pollingBadge')
                .removeClass('bg-yellow-100 text-yellow-800')
                .addClass('bg-green-100 text-green-800')
                .html('<span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Data diterima dari earscope');

            // --- Video processed ---
            if (data.processed_video_url) {
                $('#earVideoContainer').html(
                    '<video controls class="w-full rounded-lg shadow" style="max-height:320px;">'
                    + '<source src="' + data.processed_video_url + '" type="video/mp4">'
                    + 'Browser Anda tidak mendukung video HTML5.'
                    + '</video>'
                );
            } else {
                $('#earVideoContainer').html(
                    '<p class="text-sm text-gray-400">Video earscope tidak tersedia.</p>'
                );
            }

            // --- AI Result label ---
            $('#aiResultPlaceholder').addClass('hidden');
            $('#aiResultText').removeClass('hidden').text(data.ai_result);

            // --- Pre-fill textarea diagnosis_result ---
            const textarea = document.getElementById('diagnosis_result');
            if (textarea && !textarea.value.trim()) {
                textarea.value = data.ai_result;
            }
        }

        // ============================================================
        // BUKA / TUTUP FORM
        // ============================================================
        function openDiagnosisForm(consultationId) {
            document.getElementById('consultationsSection').style.display = 'none';
            document.getElementById('diagnosisFormSection').style.display = 'block';
            document.getElementById('diagnosisConsultationId').value = consultationId;

            // Tampilkan ID di banner
            document.getElementById('bannerConsultationId').textContent = consultationId;

            // Reset tampilan earscope
            $('#pollingBadge')
                .removeClass('bg-green-100 text-green-800')
                .addClass('bg-yellow-100 text-yellow-800')
                .html('<span class="animate-pulse w-2 h-2 rounded-full bg-yellow-500 inline-block"></span> Menunggu data earscope...');
            $('#earVideoContainer').html('<p class="text-sm text-gray-400">Video hasil pemeriksaan akan muncul otomatis setelah perangkat earscope mengirim data.</p>');
            $('#aiResultPlaceholder').removeClass('hidden');
            $('#aiResultText').addClass('hidden').text('');

            // Fetch detail konsultasi
            $.ajax({
                url: '/doctor/consultation/' + consultationId + '/details',
                type: 'GET',
                success: function (data) {
                    $('#detailPatientName').text(data.patient?.name || '-');
                    $('#detailPatientAge').text(data.patient?.age || '-');
                    $('#detailPatientGender').text(data.patient?.gender || '-');
                    $('#detailPatientEmail').text(data.patient?.email || '-');
                    $('#detailComplaint').text(data.complaint || '-');
                    $('#detailScheduled').text(data.scheduled_date
                        ? new Date(data.scheduled_date).toLocaleDateString('id-ID', {year: 'numeric', month: 'long', day: 'numeric'})
                          + ' ' + (data.scheduled_time || '')
                        : '-');
                },
                error: function () {
                    alert('Gagal memuat detail konsultasi');
                    closeDiagnosisForm();
                }
            });

            // Set action form
            $('#diagnosisForm').attr('action', '{{ route("doctor.diagnoses.store") }}');

            // Mulai polling earscope
            startEarscopePolling(consultationId);
        }

        function closeDiagnosisForm() {
            stopEarscopePolling();
            document.getElementById('diagnosisFormSection').style.display = 'none';
            document.getElementById('consultationsSection').style.display = 'block';
            document.getElementById('diagnosisForm').reset();
            document.getElementById('diagnosisConsultationId').value = '';
            document.getElementById('bannerConsultationId').textContent = '\u2014';
        }

        function copyConsultationId() {
            const id = document.getElementById('bannerConsultationId').textContent;
            navigator.clipboard.writeText(id).then(function () {
                const btn = event.currentTarget;
                const original = btn.innerHTML;
                btn.textContent = '✓ Tersalin!';
                btn.classList.replace('bg-indigo-600', 'bg-green-600');
                setTimeout(function () {
                    btn.innerHTML = original;
                    btn.classList.replace('bg-green-600', 'bg-indigo-600');
                }, 1500);
            });
        }

        // CSRF token untuk semua AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Client-side search
        function filterConsultations() {
            const q = ($('#searchConsultations').val() || '').toLowerCase().trim();
            if (!q) {
                $('#consultationsSection table tbody tr').show();
                return;
            }
            $('#consultationsSection table tbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) !== -1);
            });
        }

        $(document).on('input', '#searchConsultations', filterConsultations);

        function clearSearch() {
            $('#searchConsultations').val('');
            filterConsultations();
            $('#searchConsultations').focus();
        }
    </script>
</x-app-layout>
