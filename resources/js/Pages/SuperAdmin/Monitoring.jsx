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
            : [];

    const defaultSubjectPerformance =
        Array.isArray(subjectPerformance) && subjectPerformance.length > 0
            ? subjectPerformance
            : [];

    // Chart data untuk distribusi skor nasional
    const scoreDistributionData = {
        labels: ["0-60", "61-70", "71-80", "81-90", "91-100"],
        datasets: [
            {
                label: "Jumlah Siswa",
                data: [0, 0, 0, 0, 0], // Data akan diisi dari database
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

            <div className="p-4 sm:p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-4 sm:mb-6">
                    <div className="px-4 sm:px-6 py-4">
                        <div>
                            <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
                                Monitoring Global
                            </h1>
                            <p className="mt-1 text-sm text-gray-500">
                                Data nasional dan performa lintas sekolah
                            </p>
                        </div>
                    </div>
                </div>

                {/* National Statistics */}
                <div className="mb-6 sm:mb-8">
                    <h2 className="text-base sm:text-lg font-semibold text-gray-900 mb-4">
                        Statistik Nasional
                    </h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                            <div className="flex items-center">
                                <div className="p-2 sm:p-3 rounded-lg bg-blue-500">
                                    <Building2 className="h-5 w-5 sm:h-6 sm:w-6 text-white" />
                                </div>
                                <div className="ml-3 sm:ml-4">
                                    <p className="text-xs sm:text-sm font-medium text-gray-600">
                                        Total Sekolah
                                    </p>
                                    <p className="text-xl sm:text-2xl font-bold text-gray-900">
                                        {defaultStats.total_schools}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                            <div className="flex items-center">
                                <div className="p-2 sm:p-3 rounded-lg bg-green-500">
                                    <Users className="h-5 w-5 sm:h-6 sm:w-6 text-white" />
                                </div>
                                <div className="ml-3 sm:ml-4">
                                    <p className="text-xs sm:text-sm font-medium text-gray-600">
                                        Total Siswa
                                    </p>
                                    <p className="text-xl sm:text-2xl font-bold text-gray-900">
                                        {defaultStats.total_students}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                            <div className="flex items-center">
                                <div className="p-2 sm:p-3 rounded-lg bg-purple-500">
                                    <BarChart3 className="h-5 w-5 sm:h-6 sm:w-6 text-white" />
                                </div>
                                <div className="ml-3 sm:ml-4">
                                    <p className="text-xs sm:text-sm font-medium text-gray-600">
                                        Rata-rata Skor
                                    </p>
                                    <p className="text-xl sm:text-2xl font-bold text-gray-900">
                                        {formatScore(
                                            defaultStats.average_score
                                        )}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                            <div className="flex items-center">
                                <div className="p-2 sm:p-3 rounded-lg bg-orange-500">
                                    <School className="h-5 w-5 sm:h-6 sm:w-6 text-white" />
                                </div>
                                <div className="ml-3 sm:ml-4">
                                    <p className="text-xs sm:text-sm font-medium text-gray-600">
                                        Total Rekomendasi
                                    </p>
                                    <p className="text-xl sm:text-2xl font-bold text-gray-900">
                                        {defaultStats.total_recommendations}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                    {/* School Performance */}
                    <div className="bg-white rounded-lg shadow-sm border">
                        <div className="px-4 sm:px-6 py-3 sm:py-4 border-b">
                            <h3 className="text-base sm:text-lg font-semibold text-gray-900">
                                Performa Sekolah
                            </h3>
                        </div>
                        <div className="p-4 sm:p-6">
                            {defaultSchoolPerformance.length > 0 ? (
                                <div className="space-y-3 sm:space-y-4">
                                    {defaultSchoolPerformance.map((school) => (
                                        <div
                                            key={school.id}
                                            className="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-gray-50 rounded-lg space-y-2 sm:space-y-0"
                                        >
                                            <div className="min-w-0 flex-1">
                                                <p className="text-sm sm:text-base font-medium text-gray-900 truncate">
                                                    {school.name}
                                                </p>
                                                <p className="text-xs sm:text-sm text-gray-500">
                                                    {school.students_count || 0}{" "}
                                                    siswa
                                                </p>
                                            </div>
                                            <div className="text-left sm:text-right">
                                                <p className="text-xs sm:text-sm font-medium text-gray-900">
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
                                <div className="text-center py-6 sm:py-8">
                                    <p className="text-sm sm:text-base text-gray-500 mb-2">
                                        Belum ada data performa sekolah
                                    </p>
                                    <p className="text-xs sm:text-sm text-gray-400">
                                        Data akan muncul setelah ada siswa yang
                                        mengikuti ujian
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Subject Performance */}
                    <div className="bg-white rounded-lg shadow-sm border">
                        <div className="px-4 sm:px-6 py-3 sm:py-4 border-b">
                            <h3 className="text-base sm:text-lg font-semibold text-gray-900">
                                Performa Mata Pelajaran
                            </h3>
                        </div>
                        <div className="p-4 sm:p-6">
                            {defaultSubjectPerformance.length > 0 ? (
                                <div className="space-y-3 sm:space-y-4">
                                    {defaultSubjectPerformance.map(
                                        (subject, index) => (
                                            <div
                                                key={index}
                                                className="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-gray-50 rounded-lg space-y-2 sm:space-y-0"
                                            >
                                                <div className="min-w-0 flex-1">
                                                    <p className="text-sm sm:text-base font-medium text-gray-900 truncate">
                                                        {subject.subject}
                                                    </p>
                                                    <p className="text-xs sm:text-sm text-gray-500">
                                                        {subject.total_students}{" "}
                                                        siswa
                                                    </p>
                                                </div>
                                                <div className="text-left sm:text-right">
                                                    <p className="text-xs sm:text-sm font-medium text-gray-900">
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
                                <div className="text-center py-6 sm:py-8">
                                    <p className="text-sm sm:text-base text-gray-500 mb-2">
                                        Belum ada data performa mata pelajaran
                                    </p>
                                    <p className="text-xs sm:text-sm text-gray-400">
                                        Data akan muncul setelah ada siswa yang
                                        mengikuti ujian
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Charts Section */}
                {defaultSchoolPerformance.length > 0 && (
                    <div className="mt-6 sm:mt-8">
                        <h2 className="text-base sm:text-lg font-semibold text-gray-900 mb-4">
                            Grafik Performa
                        </h2>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                            <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                                <h3 className="text-base sm:text-lg font-medium text-gray-900 mb-4">
                                    Distribusi Skor Nasional
                                </h3>
                                <div className="h-48 sm:h-64">
                                    <Doughnut
                                        data={scoreDistributionData}
                                        options={doughnutOptions}
                                    />
                                </div>
                            </div>
                            <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                                <h3 className="text-base sm:text-lg font-medium text-gray-900 mb-4">
                                    Perbandingan Antar Sekolah
                                </h3>
                                <div className="h-48 sm:h-64">
                                    <Bar
                                        data={schoolComparisonData}
                                        options={chartOptions}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Additional Charts */}
                {defaultSchoolPerformance.length > 0 &&
                    defaultSubjectPerformance.length > 0 && (
                        <div className="mt-6 sm:mt-8">
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                                <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                                    <h3 className="text-base sm:text-lg font-medium text-gray-900 mb-4">
                                        Performa Mata Pelajaran
                                    </h3>
                                    <div className="h-48 sm:h-64">
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
                                <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                                    <h3 className="text-base sm:text-lg font-medium text-gray-900 mb-4">
                                        Jumlah Siswa per Sekolah
                                    </h3>
                                    <div className="h-48 sm:h-64">
                                        <Bar
                                            data={{
                                                labels: defaultSchoolPerformance.map(
                                                    (s) => s.name
                                                ),
                                                datasets: [
                                                    {
                                                        label: "Jumlah Siswa",
                                                        data: defaultSchoolPerformance.map(
                                                            (s) =>
                                                                s.students_count ||
                                                                0
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
                    )}
            </div>
        </SuperAdminLayout>
    );
}
