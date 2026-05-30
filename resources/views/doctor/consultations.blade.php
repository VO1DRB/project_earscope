<x-app-layout>
        <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Consultation Request') }}
        </h2>
    </x-slot>

        <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">                    
                    <!-- Status Filter Tabs -->
                    <div class="mb-6 flex flex-wrap gap-2 border-b border-gray-200 pb-4">
                        <a href="{{ route('doctor.consultations', ['status' => 'all']) }}" 
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $status === 'all' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            All
                        </a>
                        <a href="{{ route('doctor.consultations', ['status' => 'pending']) }}" 
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Pending
                        </a>
                        <a href="{{ route('doctor.consultations', ['status' => 'scheduled']) }}" 
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $status === 'scheduled' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Scheduled
                        </a>
                        <a href="{{ route('doctor.consultations', ['status' => 'cancelled']) }}" 
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Cancelled
                        </a>
                        <a href="{{ route('doctor.consultations', ['status' => 'done']) }}" 
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $status === 'done' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Done
                        </a>
                    </div>
                    
                    @if($consultations->isEmpty())
                        <p class="text-gray-500">No consultation requests found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($consultations as $consultation)
                                        <tr id="row-{{ $consultation->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $consultation->patient->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $consultation->patient->user->email ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ Str::limit($consultation->complaint, 40) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span id="status-{{ $consultation->id }}" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $consultation->status === 'scheduled' ? 'bg-green-100 text-green-800' : 
                                                       ($consultation->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                                       ($consultation->status === 'done' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                    {{ ucfirst($consultation->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                @if($consultation->scheduled_date)
                                                    {{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('M d, Y') }}<br>
                                                    <span class="text-xs">{{ $consultation->scheduled_time }}</span>
                                                @else
                                                    <span class="text-gray-400">Not scheduled</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button type="button" onclick="openDetailModal('{{ $consultation->id }}')" class="text-indigo-600 hover:text-indigo-900 underline">
                                                    View
                                                </button>

                                                @if($consultation->status === 'pending')
                                                    <button type="button"
                                                        onclick="openScheduleModal('{{ $consultation->id }}', true)"
                                                        class="text-green-600 hover:text-green-900 underline">
                                                        Set Schedule
                                                    </button>
                                                @endif

                                                @if($consultation->status === 'scheduled')
                                                    <button type="button" onclick="openScheduleModal('{{ $consultation->id }}', false)" class="text-blue-600 hover:text-blue-900 underline">
                                                        Reschedule
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
            if (confirm('Are you sure you want to reject this consultation?')) {
                $.ajax({
                    url: '/doctor/consultation/' + consultationId + '/reject',
                    type: 'POST',
                    data: {
                        _token: $('[name="_token"]').val()
                    },
                    success: function(response) {
                        // Update status badge
                                $('#status-' + consultationId).removeClass('bg-yellow-100 text-yellow-800').addClass('bg-red-100 text-red-800').text('Cancelled');
                        
                        showNotification('Consultation cancelled successfully', 'success');
                    },
                    error: function(xhr) {
                        showNotification('Failed to reject consultation', 'error');
                    }
                });
            }
        }

        // Show Notification
        function showNotification(message, type) {
            let bgColor = type === 'success' ? 'bg-green-100' : 'bg-red-100';
            let textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
            
            let notification = `
                <div class="fixed top-4 right-4 rounded-md ${bgColor} p-4 shadow-lg z-50">
                    <p class="text-sm font-medium ${textColor}">${message}</p>
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