<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Overview') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- STATS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Pending Consultations</h3>
                    <p class="text-2xl font-bold">{{ $pendingCount }}</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Today's Schedule</h3>
                    <p class="text-2xl font-bold">{{ $todayScheduleCount }}</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Patients Handled</h3>
                    <p class="text-2xl font-bold">{{ $patientsHandledCount }}</p>
                </div>

            </div>

            <!-- HISTORY TABLE -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Consultation History</h3>

                @if($histories->isEmpty())
                    <p class="text-gray-500">No history available</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead>
                                <tr class="text-left text-xs text-gray-500 uppercase">
                                    <th class="px-4 py-2">Patient</th>
                                    <th class="px-4 py-2">Complaint</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($histories as $history)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">
                                            {{ $history->patient->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ Str::limit($history->complaint, 40) }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ ucfirst($history->status) }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $history->created_at->format('d M Y') }}
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