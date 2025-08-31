import React, { useState } from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { FileText, Download, Calendar, BarChart3, Users, Building2 } from 'lucide-react';

export default function Reports() {
    const [selectedReport, setSelectedReport] = useState('');
    const [dateRange, setDateRange] = useState({ start: '', end: '' });

    const { post, processing } = useForm();

    const reportTypes = [
        {
            id: 'schools',
            title: 'Laporan Sekolah',
            description: 'Data lengkap semua sekolah terdaftar',
            icon: Building2,
            color: 'bg-blue-500'
        },
        {
            id: 'students',
            title: 'Laporan Siswa',
            description: 'Data lengkap semua siswa dan performanya',
            icon: Users,
            color: 'bg-green-500'
        },
        {
            id: 'results',
            title: 'Laporan Hasil',
            description: 'Data hasil ujian dan skor siswa',
            icon: BarChart3,
            color: 'bg-purple-500'
        },
        {
            id: 'questions',
            title: 'Laporan Bank Soal',
            description: 'Data lengkap bank soal dan kunci jawaban',
            icon: FileText,
            color: 'bg-orange-500'
        }
    ];

    const handleDownloadReport = () => {
        if (!selectedReport) return;
        
        post('/super-admin/reports/download', {
            data: {
                type: selectedReport,
                start_date: dateRange.start,
                end_date: dateRange.end
            }
        });
    };

    return (
        <SuperAdminLayout>
            <Head title="Laporan" />
            
            <div className="p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-6">
                    <div className="px-6 py-4">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Laporan</h1>
                            <p className="mt-1 text-sm text-gray-500">Download laporan data sistem</p>
                        </div>
                    </div>
                </div>

                {/* Report Selection */}
                <div className="mb-8">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">Pilih Jenis Laporan</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {reportTypes.map((report) => (
                            <div
                                key={report.id}
                                onClick={() => setSelectedReport(report.id)}
                                className={`bg-white rounded-lg shadow-sm border p-6 cursor-pointer transition-all duration-200 hover:shadow-md ${
                                    selectedReport === report.id ? 'ring-2 ring-blue-500 border-blue-500' : 'hover:border-gray-300'
                                }`}
                            >
                                <div className="flex items-center">
                                    <div className={`p-3 rounded-lg ${report.color}`}>
                                        <report.icon className="h-6 w-6 text-white" />
                                    </div>
                                    <div className="ml-4">
                                        <h3 className="font-semibold text-gray-900">{report.title}</h3>
                                        <p className="text-sm text-gray-500">{report.description}</p>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Date Range Selection */}
                {selectedReport && (
                    <div className="mb-8">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Rentang Waktu (Opsional)</h3>
                        <div className="bg-white rounded-lg shadow-sm border p-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        <Calendar className="inline h-4 w-4 mr-2" />
                                        Tanggal Mulai
                                    </label>
                                    <input
                                        type="date"
                                        value={dateRange.start}
                                        onChange={(e) => setDateRange({ ...dateRange, start: e.target.value })}
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
                                        onChange={(e) => setDateRange({ ...dateRange, end: e.target.value })}
                                        className="block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>
                            <div className="mt-4">
                                <button
                                    onClick={() => setDateRange({ start: '', end: '' })}
                                    className="text-sm text-gray-500 hover:text-gray-700 underline"
                                >
                                    Reset rentang waktu
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {/* Download Section */}
                {selectedReport && (
                    <div className="bg-white rounded-lg shadow-sm border p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900">
                                    Download Laporan {reportTypes.find(r => r.id === selectedReport)?.title}
                                </h3>
                                <p className="text-sm text-gray-500 mt-1">
                                    Laporan akan diunduh dalam format Excel (.xlsx)
                                </p>
                            </div>
                            <button
                                onClick={handleDownloadReport}
                                disabled={processing}
                                className="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <Download className="h-4 w-4 mr-2" />
                                {processing ? 'Mengunduh...' : 'Download Laporan'}
                            </button>
                        </div>
                    </div>
                )}

                {/* Report Information */}
                <div className="mt-8">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4">Informasi Laporan</h3>
                    <div className="bg-white rounded-lg shadow-sm border p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 className="font-medium text-gray-900 mb-2">Format Laporan</h4>
                                <ul className="text-sm text-gray-600 space-y-1">
                                    <li>• File Excel (.xlsx) dengan multiple sheet</li>
                                    <li>• Data terorganisir dengan rapi</li>
                                    <li>• Siap untuk analisis lanjutan</li>
                                    <li>• Kompatibel dengan Excel dan Google Sheets</li>
                                </ul>
                            </div>
                            <div>
                                <h4 className="font-medium text-gray-900 mb-2">Konten Laporan</h4>
                                <ul className="text-sm text-gray-600 space-y-1">
                                    <li>• Data lengkap sesuai jenis laporan</li>
                                    <li>• Statistik dan ringkasan</li>
                                    <li>• Filter berdasarkan rentang waktu</li>
                                    <li>• Export otomatis dengan timestamp</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
