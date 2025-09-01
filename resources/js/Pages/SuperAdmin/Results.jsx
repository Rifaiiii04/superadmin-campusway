import React from "react";
import { Head } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { FileText, Clock, BarChart3 } from "lucide-react";

export default function Results() {
    return (
        <SuperAdminLayout>
            <Head title="Hasil Tes" />

            <div className="p-4 sm:p-6">
                <div className="max-w-4xl mx-auto">
                    {/* Header */}
                    <div className="text-center mb-8">
                        <div className="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                            <FileText className="h-10 w-10 text-green-600" />
                        </div>
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">
                            Hasil Tes
                        </h1>
                        <p className="text-lg text-gray-600">
                            Monitoring dan analisis hasil tes siswa TKAWEB
                        </p>
                    </div>

                    {/* Coming Soon Card */}
                    <div className="bg-white rounded-lg shadow-sm border p-8 text-center">
                        <div className="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                            <Clock className="h-8 w-8 text-yellow-600" />
                        </div>
                        <h2 className="text-2xl font-bold text-gray-900 mb-2">
                            Coming Soon! 🚀
                        </h2>
                        <p className="text-gray-600 mb-4 max-w-md mx-auto">
                            Fitur Hasil Tes sedang dalam pengembangan. 
                            Anda akan dapat melihat dan menganalisis hasil tes siswa dengan detail dan visualisasi yang informatif.
                        </p>
                        <div className="bg-gray-50 rounded-lg p-4 inline-block">
                            <p className="text-sm text-gray-500">
                                Fitur yang akan tersedia:
                            </p>
                            <ul className="text-sm text-gray-600 mt-2 space-y-1">
                                <li>• Dashboard hasil tes per sekolah</li>
                                <li>• Analisis performa per mata pelajaran</li>
                                <li>• Grafik dan statistik hasil tes</li>
                                <li>• Export hasil tes ke PDF/Excel</li>
                                <li>• Filter berdasarkan tanggal dan kriteria</li>
                                <li>• Perbandingan antar sekolah</li>
                            </ul>
                        </div>
                    </div>

                    {/* Preview Info Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                        <div className="bg-white rounded-lg shadow-sm border p-4 text-center">
                            <div className="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-3">
                                <BarChart3 className="h-6 w-6 text-blue-600" />
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                Analisis Performa
                            </h3>
                            <p className="text-sm text-gray-600">
                                Grafik dan statistik detail untuk setiap mata pelajaran dan sekolah
                            </p>
                        </div>
                        
                        <div className="bg-white rounded-lg shadow-sm border p-4 text-center">
                            <div className="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-3">
                                <FileText className="h-6 w-6 text-green-600" />
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                Export & Laporan
                            </h3>
                            <p className="text-sm text-gray-600">
                                Generate laporan dalam berbagai format untuk kebutuhan administrasi
                            </p>
                        </div>
                        
                        <div className="bg-white rounded-lg shadow-sm border p-4 text-center">
                            <div className="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-3">
                                <Clock className="h-6 w-6 text-purple-600" />
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                Real-time Monitoring
                            </h3>
                            <p className="text-sm text-gray-600">
                                Pantau progress tes dan hasil secara real-time
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
