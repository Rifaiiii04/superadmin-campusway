import React from "react";
import { Head } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { BookOpen, Clock } from "lucide-react";

export default function Questions() {
    return (
        <SuperAdminLayout>
            <Head title="Bank Soal" />

            <div className="p-4 sm:p-6">
                <div className="max-w-4xl mx-auto">
                    {/* Header */}
                    <div className="text-center mb-8">
                        <div className="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4">
                            <BookOpen className="h-10 w-10 text-blue-600" />
                        </div>
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">
                            Bank Soal
                        </h1>
                        <p className="text-lg text-gray-600">
                            Manajemen bank soal untuk sistem testing TKAWEB
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
                            Fitur Bank Soal sedang dalam pengembangan. 
                            Anda akan dapat mengelola soal, opsi jawaban, dan kategori mata pelajaran dengan mudah.
                        </p>
                        <div className="bg-gray-50 rounded-lg p-4 inline-block">
                            <p className="text-sm text-gray-500">
                                Fitur yang akan tersedia:
                            </p>
                            <ul className="text-sm text-gray-600 mt-2 space-y-1">
                                <li>• Tambah, edit, hapus soal</li>
                                <li>• Manajemen opsi jawaban</li>
                                <li>• Kategorisasi mata pelajaran</li>
                                <li>• Import soal dari CSV/Excel</li>
                                <li>• Preview dan validasi soal</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
