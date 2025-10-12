import React from 'react';
import { Head } from '@inertiajs/react';

export default function About({ title, version }) {
    return (
        <>
            <Head title={title} />
            <div className="min-h-screen bg-gray-50 py-8">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg p-8">
                        <h1 className="text-3xl font-bold text-gray-900 mb-6">
                            {title}
                        </h1>
                        
                        <div className="prose max-w-none">
                            <p className="text-lg text-gray-600 mb-6">
                                SuperAdmin CampusWay adalah sistem manajemen terpadu untuk mengelola 
                                data sekolah, jurusan, dan rekomendasi program studi.
                            </p>
                            
                            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <h2 className="text-xl font-semibold text-blue-900 mb-2">
                                    Informasi Sistem
                                </h2>
                                <ul className="text-blue-800 space-y-1">
                                    <li><strong>Framework:</strong> Laravel {version}</li>
                                    <li><strong>Frontend:</strong> React + Inertia.js</li>
                                    <li><strong>Database:</strong> SQLite (In-Memory)</li>
                                    <li><strong>Session:</strong> Array-based</li>
                                </ul>
                            </div>
                            
                            <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                                Fitur Utama
                            </h2>
                            <ul className="list-disc list-inside text-gray-600 space-y-2">
                                <li>Manajemen Data Sekolah</li>
                                <li>Rekomendasi Jurusan</li>
                                <li>Bank Soal TKA</li>
                                <li>Hasil Tes Siswa</li>
                                <li>Jadwal TKA</li>
                                <li>Dashboard Analytics</li>
                            </ul>
                            
                            <div className="mt-8 pt-6 border-t border-gray-200">
                                <p className="text-sm text-gray-500">
                                    © 2024 SuperAdmin CampusWay. All rights reserved.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
