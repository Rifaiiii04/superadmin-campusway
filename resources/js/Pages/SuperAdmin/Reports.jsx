import React, { useState } from "react";
import { Head, useForm } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import {
    FileText,
    Download,
    Calendar,
    BarChart3,
    Users,
    Building2,
} from "lucide-react";

export default function Reports({ errors, flash }) {
    const [selectedReport, setSelectedReport] = useState("");
    const [dateRange, setDateRange] = useState({ start: "", end: "" });

    const { post, processing } = useForm();

    const reportTypes = [
        {
            id: "schools",
            title: "Laporan Sekolah",
            description: "Data lengkap semua sekolah terdaftar",
            icon: Building2,
            color: "bg-blue-500",
        },
        {
            id: "students",
            title: "Laporan Siswa",
            description: "Data lengkap semua siswa dan performanya",
            icon: Users,
            color: "bg-green-500",
        },
        {
            id: "results",
            title: "Laporan Hasil",
            description: "Data hasil ujian dan skor siswa",
            icon: BarChart3,
            color: "bg-purple-500",
        },
        {
            id: "questions",
            title: "Laporan Bank Soal",
            description: "Data lengkap bank soal dan kunci jawaban",
            icon: FileText,
            color: "bg-orange-500",
        },
    ];

    const handleDownloadReport = () => {
        if (!selectedReport) return;

        // Set processing state
        post(
            "/super-admin/reports/download",
            {
                type: selectedReport,
                start_date: dateRange.start,
                end_date: dateRange.end,
            },
            {
                onSuccess: () => {
                    console.log("Download request berhasil dikirim");
                },
                onError: (errors) => {
                    console.error("Download error:", errors);
                    alert("Gagal mengunduh laporan. Silakan coba lagi.");
                },
                onFinish: () => {
                    console.log("Download request selesai");
                },
            }
        );
    };

    const handleDownloadReportDirect = () => {
        if (!selectedReport) return;

        // Debug: log data yang akan dikirim
        console.log("Data yang akan dikirim:", {
            type: selectedReport,
            start_date: dateRange.start,
            end_date: dateRange.end,
        });

        // Buat form data untuk download
        const formData = new FormData();
        formData.append("type", selectedReport);
        if (dateRange.start) formData.append("start_date", dateRange.start);
        if (dateRange.end) formData.append("end_date", dateRange.end);

        // Gunakan fetch untuk download file langsung
        fetch("/super-admin/reports/download", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
                Accept: "text/csv",
            },
            body: formData,
        })
            .then((response) => {
                if (response.ok) {
                    return response.blob();
                }
                // Jika ada error, coba parse JSON error
                return response.json().then((err) => {
                    throw new Error(err.error || "Download gagal");
                });
            })
            .then((blob) => {
                // Buat link untuk download
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = `laporan_${selectedReport}_${new Date()
                    .toISOString()
                    .slice(0, 10)}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                // Reset processing state
                console.log("Download berhasil!");
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Gagal mengunduh laporan: " + error.message);
            });
    };

    return (
        <SuperAdminLayout>
            <Head title="Laporan" />

            <div className="p-4 sm:p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-4 sm:mb-6">
                    <div className="px-4 sm:px-6 py-4">
                        <div>
                            <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
                                Laporan
                            </h1>
                            <p className="mt-1 text-sm text-gray-500">
                                Download laporan data sistem
                            </p>
                        </div>
                    </div>
                </div>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="mb-4 sm:mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg
                                    className="h-5 w-5 text-green-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clipRule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-green-800">
                                    {flash.success}
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {flash?.error && (
                    <div className="mb-4 sm:mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg
                                    className="h-5 w-5 text-red-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clipRule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-red-800">
                                    {flash.error}
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Report Selection */}
                <div className="mb-6 sm:mb-8">
                    <h2 className="text-base sm:text-lg font-semibold text-gray-900 mb-4">
                        Pilih Jenis Laporan
                    </h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        {reportTypes.map((report) => (
                            <div
                                key={report.id}
                                onClick={() => setSelectedReport(report.id)}
                                className={`bg-white rounded-lg shadow-sm border p-4 sm:p-6 cursor-pointer transition-all duration-200 hover:shadow-md ${
                                    selectedReport === report.id
                                        ? "ring-2 ring-blue-500 border-blue-500"
                                        : "hover:border-gray-300"
                                }`}
                            >
                                <div className="flex items-center">
                                    <div
                                        className={`p-2 sm:p-3 rounded-lg ${report.color}`}
                                    >
                                        <report.icon className="h-5 w-5 sm:h-6 sm:w-6 text-white" />
                                    </div>
                                    <div className="ml-3 sm:ml-4">
                                        <h3 className="text-sm sm:text-base font-semibold text-gray-900">
                                            {report.title}
                                        </h3>
                                        <p className="text-xs sm:text-sm text-gray-500">
                                            {report.description}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Date Range Selection */}
                {selectedReport && (
                    <div className="mb-6 sm:mb-8">
                        <h3 className="text-base sm:text-lg font-semibold text-gray-900 mb-4">
                            Rentang Waktu (Opsional)
                        </h3>
                        <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        <Calendar className="inline h-4 w-4 mr-2" />
                                        Tanggal Mulai
                                    </label>
                                    <input
                                        type="date"
                                        value={dateRange.start}
                                        onChange={(e) =>
                                            setDateRange({
                                                ...dateRange,
                                                start: e.target.value,
                                            })
                                        }
                                        className="block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        <Calendar className="inline h-4 w-4 mr-2" />
                                        Tanggal Akhir
                                    </label>
                                    <input
                                        type="date"
                                        value={dateRange.end}
                                        onChange={(e) =>
                                            setDateRange({
                                                ...dateRange,
                                                end: e.target.value,
                                            })
                                        }
                                        className="block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>
                            <div className="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                                <button
                                    onClick={() =>
                                        setDateRange({ start: "", end: "" })
                                    }
                                    className="text-sm text-gray-500 hover:text-gray-700 underline"
                                >
                                    Reset rentang waktu
                                </button>
                                {dateRange.start && dateRange.end && (
                                    <div className="text-sm text-gray-600">
                                        <span className="font-medium">
                                            Rentang:
                                        </span>{" "}
                                        {dateRange.start} s/d {dateRange.end}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                )}

                {/* Download Section */}
                {selectedReport && (
                    <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                            <div className="flex-1">
                                <h3 className="text-base sm:text-lg font-semibold text-gray-900">
                                    Download Laporan{" "}
                                    {
                                        reportTypes.find(
                                            (r) => r.id === selectedReport
                                        )?.title
                                    }
                                </h3>
                                <p className="text-xs sm:text-sm text-gray-500 mt-1">
                                    Laporan akan diunduh dalam format CSV (.csv)
                                    dengan delimiter semicolon (;) yang siap
                                    dibuka di Excel
                                </p>
                            </div>
                            <div className="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                                <button
                                    onClick={handleDownloadReportDirect}
                                    disabled={processing}
                                    className="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <Download className="h-4 w-4 mr-2" />
                                    {processing
                                        ? "Mengunduh..."
                                        : "Download Laporan"}
                                </button>
                                {processing && (
                                    <div className="text-xs sm:text-sm text-gray-500 text-center sm:text-left">
                                        ⏳ Sedang memproses laporan...
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                )}

                {/* Report Information */}
                <div className="mt-6 sm:mt-8">
                    <h3 className="text-base sm:text-lg font-semibold text-gray-900 mb-4">
                        Informasi Laporan
                    </h3>
                    <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <h4 className="font-medium text-gray-900 mb-2">
                                    Format Laporan
                                </h4>
                                <ul className="text-xs sm:text-sm text-gray-600 space-y-1">
                                    <li>
                                        • File CSV (.csv) dengan delimiter
                                        semicolon (;)
                                    </li>
                                    <li>
                                        • Kolom terpisah dengan sempurna di
                                        Excel
                                    </li>
                                    <li>
                                        • Siap dibuka di Excel dan Google Sheets
                                    </li>
                                    <li>
                                        • Format yang kompatibel dengan Excel
                                        Indonesia
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4 className="font-medium text-gray-900 mb-2">
                                    Konten Laporan
                                </h4>
                                <ul className="text-xs sm:text-sm text-gray-600 space-y-1">
                                    <li>• Data lengkap sesuai jenis laporan</li>
                                    <li>• Format kolom yang terstruktur</li>
                                    <li>• Filter berdasarkan rentang waktu</li>
                                    <li>• Nomor urut dan status yang jelas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
