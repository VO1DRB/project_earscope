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

            <!-- UPCOMING CONSULTATIONS -->
            <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                    <h3 class="font-bold text-lg">Upcoming Consultation</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('doctor.dashboard', ['filter' => 'all']) }}"
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'all' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            All
                        </a>
                        <a href="{{ route('doctor.dashboard', ['filter' => 'today']) }}"
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'today' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Hari Ini
                        </a>
                        <a href="{{ route('doctor.dashboard', ['filter' => 'week']) }}"
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'week' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Minggu Ini
                        </a>
                        <a href="{{ route('doctor.dashboard', ['filter' => 'month']) }}"
                           class="px-4 py-2 rounded-md text-sm font-medium {{ $filter === 'month' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Bulan Ini
                        </a>
                    </div>
                </div>

                @if($consultations->isEmpty())
                    <p class="text-gray-500">No scheduled consultations found for the selected period.</p>
                @else
                    <div class="overflow-x-auto rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs text-gray-500 uppercase">
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Date</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($consultations as $consultation)
                                    <tr class="border-t">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $consultation->patient->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ Str::limit($consultation->complaint, 40) }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $consultation->scheduled_time ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ ucfirst($consultation->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- WAITING FOR APPROVAL -->
            <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-lg">Waiting for Approval</h3>
                    <span class="text-sm text-gray-500">{{ $pendingRequests->count() }} request(s)</span>
                </div>

                @if($pendingRequests->isEmpty())
                    <p class="text-gray-500">No pending consultation requests at the moment.</p>
                @else
                    <div class="overflow-x-auto rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs text-gray-500 uppercase">
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaint</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingRequests as $request)
                                    <tr class="border-t">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $request->patient->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                            {{ $request->patient->user->email ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ Str::limit($request->complaint, 40) }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $request->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ ucfirst($request->status) }}
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