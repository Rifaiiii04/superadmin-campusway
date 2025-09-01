import React from "react";
import { Head } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { Building2, Users, GraduationCap, TrendingUp } from "lucide-react";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from "chart.js";
import { Bar } from "react-chartjs-2";

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

export default function Dashboard({
    stats = {},
    recent_schools = [],
    recent_students = [],
    studentsPerMajor = [],
}) {
    // Chart data untuk siswa per jurusan
    const chartData = {
        labels: studentsPerMajor.map((item) => item.major_name),
        datasets: [
            {
                label: "Jumlah Siswa",
                data: studentsPerMajor.map((item) => item.student_count),
                backgroundColor: "rgba(59, 130, 246, 0.8)",
                borderColor: "rgba(59, 130, 246, 1)",
                borderWidth: 2,
                borderRadius: 8,
            },
        ],
    };

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "top",
            },
            title: {
                display: true,
                text: "Jumlah Siswa per Jurusan",
            },
        },
        scales: {
            y: {
                beginAtZero: true,
            },
        },
    };

    return (
        <SuperAdminLayout>
            <Head title="Dashboard" />

            <div className="p-4 sm:p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-4 sm:mb-6">
                    <div className="px-4 sm:px-6 py-4">
                        <div>
                            <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
                                Dashboard Super Admin
                            </h1>
                            <p className="mt-1 text-sm text-gray-500">
                                Selamat datang di panel administrasi TKAWEB
                            </p>
                        </div>
                    </div>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div className="bg-white rounded-lg shadow-sm border p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-blue-500">
                                <Building2 className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-600">
                                    Total Sekolah
                                </p>
                                <p className="text-xl font-bold text-gray-900">
                                    {stats.total_schools || 0}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg shadow-sm border p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-green-500">
                                <Users className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-600">
                                    Total Siswa
                                </p>
                                <p className="text-xl font-bold text-gray-900">
                                    {stats.total_students || 0}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg shadow-sm border p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-purple-500">
                                <GraduationCap className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-600">
                                    Total Jurusan
                                </p>
                                <p className="text-xl font-bold text-gray-900">
                                    {stats.total_majors || 0}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Chart Siswa per Jurusan */}
                    <div className="bg-white rounded-lg shadow-sm border">
                        <div className="px-4 sm:px-6 py-4 border-b">
                            <h3 className="text-lg font-semibold text-gray-900">
                                Jumlah Siswa per Jurusan
                            </h3>
                        </div>
                        <div className="p-4 sm:p-6">
                            {studentsPerMajor.length > 0 ? (
                                <div className="h-64">
                                    <Bar
                                        data={chartData}
                                        options={chartOptions}
                                    />
                                </div>
                            ) : (
                                <div className="text-center py-8">
                                    <p className="text-gray-500">
                                        Belum ada data siswa per jurusan
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Recent Schools */}
                    <div className="bg-white rounded-lg shadow-sm border">
                        <div className="px-4 sm:px-6 py-4 border-b">
                            <h3 className="text-lg font-semibold text-gray-900">
                                Sekolah Terbaru
                            </h3>
                        </div>
                        <div className="p-4 sm:p-6">
                            {recent_schools.length > 0 ? (
                                <div className="space-y-3">
                                    {recent_schools.map((school) => (
                                        <div
                                            key={school.id}
                                            className="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                        >
                                            <div>
                                                <p className="font-medium text-gray-900">
                                                    {school.name}
                                                </p>
                                                <p className="text-sm text-gray-500">
                                                    NPSN: {school.npsn}
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-xs text-gray-500">
                                                    {new Date(
                                                        school.created_at
                                                    ).toLocaleDateString(
                                                        "id-ID"
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8">
                                    <p className="text-gray-500">
                                        Belum ada data sekolah
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Recent Students */}
                <div className="mt-6">
                    <div className="bg-white rounded-lg shadow-sm border">
                        <div className="px-4 sm:px-6 py-4 border-b">
                            <h3 className="text-lg font-semibold text-gray-900">
                                Siswa Terbaru
                            </h3>
                        </div>
                        <div className="p-4 sm:p-6">
                            {recent_students.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Siswa
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Sekolah
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Kelas
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tanggal Daftar
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {recent_students.map((student) => (
                                                <tr key={student.id}>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div>
                                                            <div className="text-sm font-medium text-gray-900">
                                                                {student.name}
                                                            </div>
                                                            <div className="text-sm text-gray-500">
                                                                NISN:{" "}
                                                                {student.nisn}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="text-sm text-gray-900">
                                                            {student.school
                                                                ?.name || "N/A"}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <div className="text-sm text-gray-900">
                                                            {student.kelas}
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                                student.status ===
                                                                "active"
                                                                    ? "bg-green-100 text-green-800"
                                                                    : "bg-gray-100 text-gray-800"
                                                            }`}
                                                        >
                                                            {student.status ||
                                                                "N/A"}
                                                        </span>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {new Date(
                                                            student.created_at
                                                        ).toLocaleDateString(
                                                            "id-ID"
                                                        )}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            ) : (
                                <div className="text-center py-8">
                                    <p className="text-gray-500">
                                        Belum ada data siswa
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
