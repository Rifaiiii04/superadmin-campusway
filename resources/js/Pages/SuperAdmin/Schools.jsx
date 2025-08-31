import React, { useState } from "react";
import { Head, useForm, Link } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { Building2, Plus, Edit, Trash2, Upload, Search } from "lucide-react";
import ImportSchoolsModal from "./components/ImportSchoolsModal";

export default function Schools({ schools }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showImportModal, setShowImportModal] = useState(false);
    const [editingSchool, setEditingSchool] = useState(null);
    const [searchTerm, setSearchTerm] = useState("");

    const { data, setData, post, put, processing, errors, reset } = useForm({
        npsn: "",
        name: "",
        password: "",
    });

    const filteredSchools = schools.data.filter(
        (school) =>
            school.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            school.npsn.includes(searchTerm)
    );

    const handleAddSchool = () => {
        post("/super-admin/schools", {
            onSuccess: () => {
                setShowAddModal(false);
                reset();
            },
        });
    };

    const handleEditSchool = () => {
        put(`/super-admin/schools/${editingSchool.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setEditingSchool(null);
                reset();
            },
        });
    };

    const openEditModal = (school) => {
        setEditingSchool(school);
        setData({
            npsn: school.npsn,
            name: school.name,
            password: "",
        });
        setShowEditModal(true);
    };

    return (
        <SuperAdminLayout>
            <Head title="Kelola Sekolah" />

            <div className="p-4 sm:p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-4 sm:mb-6">
                    <div className="px-4 sm:px-6 py-4">
                        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                            <div>
                                <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
                                    Kelola Sekolah
                                </h1>
                                <p className="mt-1 text-sm text-gray-500">
                                    Daftar dan kelola semua sekolah terdaftar
                                </p>
                            </div>
                            <div className="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                                <button
                                    onClick={() => setShowImportModal(true)}
                                    className="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    <Upload className="h-4 w-4 mr-2" />
                                    <span className="hidden sm:inline">
                                        Import CSV
                                    </span>
                                    <span className="sm:hidden">Import</span>
                                </button>
                                <button
                                    onClick={() => setShowAddModal(true)}
                                    className="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                                >
                                    <Plus className="h-4 w-4 mr-2" />
                                    <span className="hidden sm:inline">
                                        Tambah Sekolah
                                    </span>
                                    <span className="sm:hidden">Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="mb-4 sm:mb-6">
                    <div className="relative max-w-md">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Cari sekolah..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        />
                    </div>
                </div>

                <div className="bg-white shadow overflow-hidden rounded-lg">
                    <ul className="divide-y divide-gray-200">
                        {filteredSchools.map((school) => (
                            <li key={school.id}>
                                <div className="px-4 py-4 sm:px-6">
                                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                                        <div className="flex items-center">
                                            <div className="flex-shrink-0">
                                                <Building2 className="h-6 w-6 sm:h-8 sm:w-8 text-blue-600" />
                                            </div>
                                            <div className="ml-3 min-w-0 flex-1">
                                                <div className="flex items-center">
                                                    <p className="text-sm sm:text-base font-medium text-gray-900 truncate">
                                                        {school.name}
                                                    </p>
                                                </div>
                                                <div className="mt-1 flex items-center">
                                                    <p className="text-xs sm:text-sm text-gray-500">
                                                        NPSN: {school.npsn}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-2 sm:space-x-3">
                                            <button
                                                onClick={() =>
                                                    openEditModal(school)
                                                }
                                                className="text-blue-600 hover:text-blue-900 p-1 sm:p-2 rounded-md hover:bg-blue-50"
                                                title="Edit Sekolah"
                                            >
                                                <Edit className="h-4 w-4 sm:h-5 sm:w-5" />
                                            </button>
                                            <Link
                                                href={`/super-admin/schools/${school.id}`}
                                                method="delete"
                                                as="button"
                                                className="text-red-600 hover:text-red-900 p-1 sm:p-2 rounded-md hover:bg-red-50"
                                                title="Hapus Sekolah"
                                            >
                                                <Trash2 className="h-4 w-4 sm:h-5 sm:w-5" />
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        ))}
                    </ul>
                </div>

                {/* Add School Modal */}
                {showAddModal && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div className="relative top-10 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                            <div className="mt-3">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">
                                    Tambah Sekolah Baru
                                </h3>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            NPSN
                                        </label>
                                        <input
                                            type="text"
                                            value={data.npsn}
                                            onChange={(e) =>
                                                setData("npsn", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Masukkan NPSN"
                                        />
                                        {errors.npsn && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.npsn}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Nama Sekolah
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData("name", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Masukkan nama sekolah"
                                        />
                                        {errors.name && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Password Default
                                        </label>
                                        <input
                                            type="password"
                                            value={data.password}
                                            onChange={(e) =>
                                                setData(
                                                    "password",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Masukkan password default"
                                        />
                                        {errors.password && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.password}
                                            </p>
                                        )}
                                    </div>
                                </div>
                                <div className="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
                                    <button
                                        onClick={() => setShowAddModal(false)}
                                        className="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        onClick={handleAddSchool}
                                        disabled={processing}
                                        className="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {processing ? "Menyimpan..." : "Simpan"}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Edit School Modal */}
                {showEditModal && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div className="relative top-10 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                            <div className="mt-3">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">
                                    Edit Sekolah
                                </h3>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            NPSN
                                        </label>
                                        <input
                                            type="text"
                                            value={data.npsn}
                                            onChange={(e) =>
                                                setData("npsn", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        {errors.npsn && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.npsn}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Nama Sekolah
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData("name", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        {errors.name && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Password Baru (opsional)
                                        </label>
                                        <input
                                            type="password"
                                            value={data.password}
                                            onChange={(e) =>
                                                setData(
                                                    "password",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Kosongkan jika tidak ingin mengubah password"
                                        />
                                        {errors.password && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.password}
                                            </p>
                                        )}
                                    </div>
                                </div>
                                <div className="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
                                    <button
                                        onClick={() => setShowEditModal(false)}
                                        className="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        onClick={handleEditSchool}
                                        disabled={processing}
                                        className="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {processing ? "Menyimpan..." : "Simpan"}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Import Schools Modal */}
                <ImportSchoolsModal
                    isOpen={showImportModal}
                    onClose={() => setShowImportModal(false)}
                    onSuccess={() => {
                        window.location.reload();
                    }}
                />
            </div>
        </SuperAdminLayout>
    );
}
