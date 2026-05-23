    <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- STATS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="border-2 border-[#6BFAD1] p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Dokter Aktif</h3>
                    <p class="text-2xl font-bold">
                        {{ $stats['total_doctors'] ?? 0 }}
                    </p>
                </div>

                <div class="border-2 border-[#6BFAE0] p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Pasien Terdaftar</h3>
                    <p class="text-2xl font-bold">
                        {{ $stats['total_patients'] ?? 0 }}
                    </p>
                </div>

                <div class="border-2 border-[#009BFC] p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Konsultasi Bulan Ini</h3>
                    <p class="text-2xl font-bold">
                        {{ $stats['total_consultations_month'] ?? 0 }}
                    </p>
                </div>

            </div>

            <!-- QUICK ACTIONS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <a href="{{ route('admin.doctors.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                    <h3 class="font-bold text-lg">Manajemen Dokter</h3>
                    <p class="text-gray-600 text-sm mt-2">Kelola data dokter sistem</p>
                </a>
                <a href="{{ route('admin.patients.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                    <h3 class="font-bold text-lg">Manajemen User</h3>
                    <p class="text-gray-600 text-sm mt-2">Data Pasien sistem</p>
                </a>

                <!-- <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg">Manajemen User</h3>
                    <p class="text-gray-600 text-sm mt-2">Kelola data user sistem</p>
                </div> -->

            </div>

            <!-- CHART -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">
                    Statistik Konsultasi (6 Bulan Terakhir)
                </h3>

                <canvas id="consultationChart" height="100"></canvas>
            </div>

            <!-- ACTIVITY TABLE -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Aktivitas Sistem Terbaru</h3>

                @if(empty($activityLogs))
                    <p class="text-gray-500">Tidak ada aktivitas yang tercatat</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead>
                                <tr class="text-left text-xs text-gray-500 uppercase">
                                    <th class="px-4 py-2">Waktu</th>
                                    <th class="px-4 py-2">User</th>
                                    <th class="px-4 py-2">Aktivitas</th>
                                    <th class="px-4 py-2">Deskripsi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($activityLogs as $log)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">
                                            {{ $log['timestamp']->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $log['user_name'] }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $log['activity_type'] }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">
                                            {{ $log['description'] }}
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const consultationData = @json($consultationStats ?? ['labels' => [], 'data' => []]);

        const ctx = document.getElementById('consultationChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: consultationData.labels,
                datasets: [{
                    label: 'Jumlah Konsultasi',
                    data: consultationData.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

</x-app-layout>
