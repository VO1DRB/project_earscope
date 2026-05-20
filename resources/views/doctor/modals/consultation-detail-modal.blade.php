<!-- Consultation Detail Modal -->
<div id="consultationDetailModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Consultation Request Detail
                    </h3>
                    <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="modalContent" class="mt-4">
                    <!-- Content will be loaded here -->
                    <div class="text-center">
                        <p class="text-gray-500">Loading...</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                <button type="button" onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openDetailModal(consultationId) {
        // Fetch consultation details
        $.ajax({
            url: '/doctor/consultation/' + consultationId + '/details',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Build the modal content
                let statusBadge = getBadgeClass(data.status);
                let content = `
                    <div class="space-y-4">
                        <div class="border-b pb-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Patient Information</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</p>
                                    <p class="mt-1 text-sm text-gray-900">${data.patient.name}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Age</p>
                                    <p class="mt-1 text-sm text-gray-900">${data.patient.age}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</p>
                                    <p class="mt-1 text-sm text-gray-900">${data.patient.contact}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</p>
                                    <p class="mt-1 text-sm text-gray-900">${data.patient.gender}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</p>
                                    <p class="mt-1 text-sm text-gray-900">${data.patient.address}</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-b pb-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Consultation Details</h4>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint</p>
                                <p class="mt-1 text-sm text-gray-900">${data.complaint}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Request Status</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
                                    <p class="mt-1"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusBadge}">
                                        ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                                    </span></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</p>
                                    <p class="mt-1 text-sm text-gray-900">${new Date(data.created_at).toLocaleDateString()}</p>
                                </div>
                                ${data.scheduled_date ? `
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Date</p>
                                    <p class="mt-1 text-sm text-gray-900">${data.scheduled_date}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Time</p>
                                    <p class="mt-1 text-sm text-gray-900">${data.scheduled_time}</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
                $('#modalContent').html(content);
            },
            error: function() {
                $('#modalContent').html('<p class="text-red-600">Failed to load consultation details</p>');
            }
        });

        $('#consultationDetailModal').removeClass('hidden');
    }

    function closeDetailModal() {
        $('#consultationDetailModal').addClass('hidden');
    }

    function getBadgeClass(status) {
        const classes = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800',
            'done': 'bg-blue-100 text-blue-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    // Close modal when clicking outside
    $(document).click(function(event) {
        if (event.target.id === 'consultationDetailModal') {
            closeDetailModal();
        }
    });
</script>
