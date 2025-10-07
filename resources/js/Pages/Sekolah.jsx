import React from "react";
import { Building2, Plus, Pencil, Trash2 } from "lucide-react";

const Sekolah = () => {
    return (
        <div className="p-6">
            {/* Header */}
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-900">
                    Manajemen Sekolah
                </h1>
                <p className="text-gray-600">
                    Kelola data sekolah yang terdaftar dalam sistem
                </p>
            </div>

            {/* Action Buttons */}
            <div className="mb-6 flex justify-between items-center">
                <div className="flex space-x-4">
                    <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                        <Plus className="h-5 w-5 mr-2" />
                        Tambah Sekolah
                    </button>
                    <button className="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                        Import Excel
                    </button>
                </div>
                <div className="flex items-center space-x-2">
                    <input
                        type="text"
                        placeholder="Cari sekolah..."
                        className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
            </div>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div className="bg-white p-6 rounded-lg shadow">
                    <div className="flex items-center">
                        <Building2 className="h-8 w-8 text-blue-600" />
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">
                                Total Sekolah
                            </p>
                            <p className="text-2xl font-bold text-gray-900">
                                0
                            </p>
                        </div>
                    </div>
                </div>
                <div className="bg-white p-6 rounded-lg shadow">
                    <div className="flex items-center">
                        <Building2 className="h-8 w-8 text-green-600" />
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">
                                Sekolah Aktif
                            </p>
                            <p className="text-2xl font-bold text-gray-900">
                                0
                            </p>
                        </div>
                    </div>
                </div>
                <div className="bg-white p-6 rounded-lg shadow">
                    <div className="flex items-center">
                        <Building2 className="h-8 w-8 text-yellow-600" />
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">
                                Sekolah Nonaktif
                            </p>
                            <p className="text-2xl font-bold text-gray-900">
                                0
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Table */}
            <div className="bg-white rounded-lg shadow">
                <div className="px-6 py-4 border-b border-gray-200">
                    <h3 className="text-lg font-medium text-gray-900">
                        Daftar Sekolah
                    </h3>
                </div>
                <div className="p-6">
                    <div className="text-center py-12">
                        <Building2 className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                        <h3 className="text-lg font-medium text-gray-900 mb-2">
                            Belum ada data sekolah
                        </h3>
                        <p className="text-gray-500">
                            Mulai dengan menambahkan sekolah pertama Anda
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Sekolah;
