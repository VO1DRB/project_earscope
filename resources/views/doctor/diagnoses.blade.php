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

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Ear Image from Jetson Nano -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ear Image (from Jetson Nano)</label>
                            <div id="earImageContainer" class="mt-2 p-4 border-2 border-dashed border-gray-300 rounded-lg text-center bg-gray-50">
                                <p class="text-sm text-gray-500">Image will be loaded from Jetson Nano...</p>
                            </div>
                        </div>

                        <!-- AI Screening Result -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">AI Screening Result (from Jetson Nano)</label>
                            <div id="aiResultContainer" class="mt-2 p-4 border border-gray-300 rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-500">AI screening result will be loaded from Jetson Nano...</p>
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
        function openDiagnosisForm(consultationId) {
            document.getElementById('consultationsSection').style.display = 'none';
            document.getElementById('diagnosisFormSection').style.display = 'block';
            document.getElementById('diagnosisConsultationId').value = consultationId;

            // Fetch consultation details
            $.ajax({
                url: '/doctor/consultation/' + consultationId + '/details',
                type: 'GET',
                success: function(data) {
                    // Update consultation details
                    $('#detailPatientName').text(data.patient?.name || '-');
                    $('#detailPatientAge').text(data.patient?.age || '-');
                    $('#detailPatientGender').text(data.patient?.gender || '-');
                    $('#detailPatientEmail').text(data.patient?.email || '-');
                    $('#detailComplaint').text(data.complaint || '-');
                    $('#detailScheduled').text(data.scheduled_date ? 
                        new Date(data.scheduled_date).toLocaleDateString('id-ID', {year: 'numeric', month: 'long', day: 'numeric'}) + ' ' + (data.scheduled_time || '') 
                        : '-');

                    // TODO: Fetch ear image and AI screening result from Jetson Nano
                    // For now, show placeholder
                    $('#earImageContainer').html('<p class="text-sm text-gray-500">Waiting for Jetson Nano connection...</p>');
                    $('#aiResultContainer').html('<p class="text-sm text-gray-500">Waiting for Jetson Nano connection...</p>');
                },
                error: function() {
                    alert('Failed to load consultation details');
                    closeDiagnosisForm();
                }
            });

            // Update form action
            $('#diagnosisForm').attr('action', '{{ route("doctor.diagnoses.store") }}');
        }

        function closeDiagnosisForm() {
            document.getElementById('diagnosisFormSection').style.display = 'none';
            document.getElementById('consultationsSection').style.display = 'block';

            // Reset form
            document.getElementById('diagnosisForm').reset();
            document.getElementById('diagnosisConsultationId').value = '';
        }

        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Client-side search for consultations table
        function filterConsultations() {
            const q = ($('#searchConsultations').val() || '').toLowerCase().trim();
            if (!q) {
                $('#consultationsSection table tbody tr').show();
                return;
            }

            $('#consultationsSection table tbody tr').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(q) !== -1);
            });
        }

        $(document).on('input', '#searchConsultations', function() {
            filterConsultations();
        });

        function clearSearch() {
            $('#searchConsultations').val('');
            filterConsultations();
            $('#searchConsultations').focus();
        }
    </script>
</x-app-layout>
