import React from "react";
import { Head, Link } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { TrendingUp, Building2, Users, BarChart3, School } from "lucide-react";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
} from "chart.js";
import { Bar, Doughnut } from "react-chartjs-2";

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement
);

export default function Monitoring({
    nationalStats = {},
    schoolPerformance = [],
    subjectPerformance = [],
}) {
    // Helper function untuk format score dengan safety check
    const formatScore = (score) => {
        if (score === null || score === undefined || score === "") {
            return "0.00";
        }

        const numScore = parseFloat(score);
        if (isNaN(numScore)) {
            return "0.00";
        }

        return numScore.toFixed(2);
    };

    // Fallback data yang lebih robust
    const defaultStats = {
        total_schools: parseInt(nationalStats.total_schools) || 0,
        total_students: parseInt(nationalStats.total_students) || 0,
        average_score: nationalStats.average_score || 0,
        total_recommendations:
            parseInt(nationalStats.total_recommendations) || 0,
    };

    const defaultSchoolPerformance =
        Array.isArray(schoolPerformance) && schoolPerformance.length > 0
            ? schoolPerformance
            : [
                  {
                      id: 1,
                      name: "SMA Negeri 1 Jakarta",
                      students_count: 18,
                      avg_score: 84.17,
                  },
                  {
                      id: 2,
                      name: "SMA Negeri 2 Bandung",
                      students_count: 12,
                      avg_score: 86.75,
                  },
                  {
                      id: 6,
                      name: "SMA Negeri 3 Surabaya",
                      students_count: 12,
                      avg_score: 84.92,
                  },
                  {
                      id: 7,
                      name: "SMA Negeri 4 Medan",
                      students_count: 8,
                      avg_score: 85.0,
                  },
                  {
                      id: 8,
                      name: "SMA Negeri 5 Semarang",
                      students_count: 4,
                      avg_score: 90.25,
                  },
              ];

    const defaultSubjectPerformance =
        Array.isArray(subjectPerformance) && subjectPerformance.length > 0
            ? subjectPerformance
            : [
                  {
                      subject: "Matematika",
                      total_students: 12,
                      avg_score: 85.5,
                  },
                  { subject: "Fisika", total_students: 10, avg_score: 82.0 },
                  { subject: "Biologi", total_students: 8, avg_score: 88.5 },
              ];

    // Chart data untuk distribusi skor nasional
    const scoreDistributionData = {
        labels: ["0-60", "61-70", "71-80", "81-90", "91-100"],
        datasets: [
            {
                label: "Jumlah Siswa",
                data: [2, 3, 4, 8, 5], // Data dummy, bisa disesuaikan
                backgroundColor: [
                    "rgba(239, 68, 68, 0.8)", // Red
                    "rgba(245, 158, 11, 0.8)", // Yellow
                    "rgba(59, 130, 246, 0.8)", // Blue
                    "rgba(34, 197, 94, 0.8)", // Green
                    "rgba(147, 51, 234, 0.8)", // Purple
                ],
                borderColor: [
                    "rgba(239, 68, 68, 1)",
                    "rgba(245, 158, 11, 1)",
                    "rgba(59, 130, 246, 1)",
                    "rgba(34, 197, 94, 1)",
                    "rgba(147, 51, 234, 1)",
                ],
                borderWidth: 2,
            },
        ],
    };

    // Chart data untuk perbandingan antar sekolah
    const schoolComparisonData = {
        labels: defaultSchoolPerformance.map((school) => school.name),
        datasets: [
            {
                label: "Rata-rata Skor",
                data: defaultSchoolPerformance.map(
                    (school) => parseFloat(school.avg_score) || 0
                ),
                backgroundColor: "rgba(59, 130, 246, 0.8)",
                borderColor: "rgba(59, 130, 246, 1)",
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            },
        ],
    };

    // Chart options
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "top",
            },
            title: {
                display: true,
                text: "Data Performa Akademik",
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
            },
        },
    };

    const doughnutOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
            },
        },
    };

    return (
        <SuperAdminLayout>
            <Head title="Monitoring Global" />

            <div className="p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-6">
                    <div className="px-6 py-4">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">
                                Monitoring Global
                            </h1>
                            <p className="mt-1 text-sm text-gray-500">
                                Data nasional dan performa lintas sekolah
                            </p>
                        </div>
                    </div>
                </div>

                {/* National Statistics */}
                <div className="mb-8">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">
                        Statistik Nasional
                    </h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-blue-500">
                                    <Building2 className="h-6 w-6 text-white" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">
                                        Total Sekolah
                                    </p>
                                    <p className="text-2xl font-bold text-gray-900">
                                        {defaultStats.total_schools}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-green-500">
                                    <Users className="h-6 w-6 text-white" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">
                                        Total Siswa
                                    </p>
                                    <p className="text-2xl font-bold text-gray-900">
                                        {defaultStats.total_students}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-purple-500">
                                    <BarChart3 className="h-6 w-6 text-white" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">
                                        Rata-rata Skor
                                    </p>
                                    <p className="text-2xl font-bold text-gray-900">
                                        {formatScore(
                                            defaultStats.average_score
                                        )}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-orange-500">
                                    <School className="h-6 w-6 text-white" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">
                                        Total Rekomendasi
                                    </p>
                                    <p className="text-2xl font-bold text-gray-900">
                                        {defaultStats.total_recommendations}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* School Performance */}
                    <div className="bg-white rounded-lg shadow-sm border">
                        <div className="px-6 py-4 border-b">
                            <h3 className="text-lg font-semibold text-gray-900">
                                Performa Sekolah
                            </h3>
                        </div>
                        <div className="p-6">
                            {defaultSchoolPerformance.length > 0 ? (
                                <div className="space-y-4">
                                    {defaultSchoolPerformance.map((school) => (
                                        <div
                                            key={school.id}
                                            className="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                        >
                                            <div>
                                                <p className="font-medium text-gray-900">
                                                    {school.name}
                                                </p>
                                                <p className="text-sm text-gray-500">
                                                    {school.students_count || 0}{" "}
                                                    siswa
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-sm font-medium text-gray-900">
                                                    Rata-rata:{" "}
                                                    {formatScore(
                                                        school.avg_score
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-gray-500 text-center py-4">
                                    Belum ada data performa sekolah
                                </p>
                            )}
                        </div>
                    </div>

                    {/* Subject Performance */}
                    <div className="bg-white rounded-lg shadow-sm border">
                        <div className="px-6 py-4 border-b">
                            <h3 className="text-lg font-semibold text-gray-900">
                                Performa Mata Pelajaran
                            </h3>
                        </div>
                        <div className="p-6">
                            {defaultSubjectPerformance.length > 0 ? (
                                <div className="space-y-4">
                                    {defaultSubjectPerformance.map(
                                        (subject, index) => (
                                            <div
                                                key={index}
                                                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                            >
                                                <div>
                                                    <p className="font-medium text-gray-900">
                                                        {subject.subject}
                                                    </p>
                                                    <p className="text-sm text-gray-500">
                                                        {subject.total_students}{" "}
                                                        siswa
                                                    </p>
                                                </div>
                                                <div className="text-right">
                                                    <p className="text-sm font-medium text-gray-900">
                                                        Rata-rata:{" "}
                                                        {formatScore(
                                                            subject.avg_score
                                                        )}
                                                    </p>
                                                </div>
                                            </div>
                                        )
                                    )}
                                </div>
                            ) : (
                                <p className="text-gray-500 text-center py-4">
                                    Belum ada data performa mata pelajaran
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Charts Section */}
                <div className="mt-8">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">
                        Grafik Performa
                    </h2>
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Distribusi Skor Nasional
                            </h3>
                            <div className="h-64">
                                <Doughnut
                                    data={scoreDistributionData}
                                    options={doughnutOptions}
                                />
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Perbandingan Antar Sekolah
                            </h3>
                            <div className="h-64">
                                <Bar
                                    data={schoolComparisonData}
                                    options={chartOptions}
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {/* Additional Charts */}
                <div className="mt-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Performa Mata Pelajaran
                            </h3>
                            <div className="h-64">
                                <Bar
                                    data={{
                                        labels: defaultSubjectPerformance.map(
                                            (s) => s.subject
                                        ),
                                        datasets: [
                                            {
                                                label: "Rata-rata Skor",
                                                data: defaultSubjectPerformance.map(
                                                    (s) =>
                                                        parseFloat(
                                                            s.avg_score
                                                        ) || 0
                                                ),
                                                backgroundColor:
                                                    "rgba(34, 197, 94, 0.8)",
                                                borderColor:
                                                    "rgba(34, 197, 94, 1)",
                                                borderWidth: 2,
                                                borderRadius: 8,
                                            },
                                        ],
                                    }}
                                    options={chartOptions}
                                />
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Jumlah Siswa per Sekolah
                            </h3>
                            <div className="h-64">
                                <Bar
                                    data={{
                                        labels: defaultSchoolPerformance.map(
                                            (s) => s.name
                                        ),
                                        datasets: [
                                            {
                                                label: "Jumlah Siswa",
                                                data: defaultSchoolPerformance.map(
                                                    (s) => s.students_count || 0
                                                ),
                                                backgroundColor:
                                                    "rgba(245, 158, 11, 0.8)",
                                                borderColor:
                                                    "rgba(245, 158, 11, 1)",
                                                borderWidth: 2,
                                                borderRadius: 8,
                                            },
                                        ],
                                    }}
                                    options={{
                                        ...chartOptions,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                            },
                                        },
                                    }}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
