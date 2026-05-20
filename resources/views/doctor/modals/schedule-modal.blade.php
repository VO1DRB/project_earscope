<!-- Schedule Consultation Modal -->
<div id="scheduleModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Schedule Consultation
                    </h3>
                    <button type="button" onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="scheduleForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="consultationId" name="consultation_id">

                    <div>
                        <label for="scheduledDate" class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                        <input type="date" id="scheduledDate" name="scheduled_date" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <small class="text-gray-500">Must be after today</small>
                    </div>

                    <div>
                        <label for="scheduledTime" class="block text-sm font-medium text-gray-700">Scheduled Time</label>
                        <input type="time" id="scheduledTime" name="scheduled_time" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div id="scheduleError" class="hidden">
                        <div class="rounded-md bg-red-50 p-4">
                            <p class="text-sm font-medium text-red-800" id="scheduleErrorText"></p>
                        </div>
                    </div>

                    <div id="scheduleSuccess" class="hidden">
                        <div class="rounded-md bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800" id="scheduleSuccessText"></p>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                <button type="button" id="scheduleSubmitBtn" onclick="submitSchedule()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Schedule
                </button>
                <button type="button" onclick="closeScheduleModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openScheduleModal(consultationId) {
        $('#consultationId').val(consultationId);
        $('#scheduleForm')[0].reset();
        $('#scheduleError').addClass('hidden');
        $('#scheduleSuccess').addClass('hidden');
        $('#scheduleModal').removeClass('hidden');
        
        // Set minimum date to tomorrow
        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        let minDate = tomorrow.toISOString().split('T')[0];
        $('#scheduledDate').attr('min', minDate);
    }

    function closeScheduleModal() {
        $('#scheduleModal').addClass('hidden');
    }

    function submitSchedule() {
        let consultationId = $('#consultationId').val();
        let scheduledDate = $('#scheduledDate').val();
        let scheduledTime = $('#scheduledTime').val();

        if (!scheduledDate || !scheduledTime) {
            showScheduleError('Please fill in all fields');
            return;
        }

        $('#scheduleSubmitBtn').prop('disabled', true);
        $('#scheduleSubmitBtn').html('Scheduling...');

        $.ajax({
            url: '/doctor/consultation/' + consultationId + '/schedule',
            type: 'POST',
            data: {
                _token: $('[name="_token"]').val(),
                scheduled_date: scheduledDate,
                scheduled_time: scheduledTime
            },
            success: function(response) {
                showScheduleSuccess('Consultation scheduled successfully!');
                setTimeout(function() {
                    closeScheduleModal();
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(', ');
                }
                showScheduleError(errorMessage);
                $('#scheduleSubmitBtn').prop('disabled', false);
                $('#scheduleSubmitBtn').html('Schedule');
            }
        });
    }

    function showScheduleError(message) {
        $('#scheduleErrorText').text(message);
        $('#scheduleError').removeClass('hidden');
    }

    function showScheduleSuccess(message) {
        $('#scheduleSuccessText').text(message);
        $('#scheduleSuccess').removeClass('hidden');
    }

    // Close modal when clicking outside
    $(document).click(function(event) {
        if (event.target.id === 'scheduleModal') {
            closeScheduleModal();
        }
    });
</script>
