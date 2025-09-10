import React, { useState, useMemo } from "react";
import { Head, Link } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { Building2, Plus, Edit, Trash2, Upload, Search } from "lucide-react";
import ImportSchoolsModal from "./components/ImportSchoolsModal";
import {
    useSchools,
    useCreateSchool,
    useUpdateSchool,
    useDeleteSchool,
} from "@/hooks/useSchools";

export default function SchoolsOptimized({ schools: initialSchools }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showImportModal, setShowImportModal] = useState(false);
    const [editingSchool, setEditingSchool] = useState(null);
    const [searchTerm, setSearchTerm] = useState("");
    const [currentPage, setCurrentPage] = useState(1);

    // Use React Query for data fetching
    const {
        data: schoolsData,
        isLoading,
        error,
    } = useSchools(currentPage, searchTerm);
    const createSchoolMutation = useCreateSchool();
    const updateSchoolMutation = useUpdateSchool();
    const deleteSchoolMutation = useDeleteSchool();

    // Use initial data if available, otherwise use fetched data
    const schools = schoolsData || initialSchools;

    const [formData, setFormData] = useState({
        npsn: "",
        name: "",
        password: "",
    });

    const filteredSchools = useMemo(() => {
        if (!schools?.data) return [];
        return schools.data.filter(
            (school) =>
                school.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                school.npsn.includes(searchTerm)
        );
    }, [schools?.data, searchTerm]);

    const handleAddSchool = async () => {
        try {
            await createSchoolMutation.mutateAsync(formData);
            setShowAddModal(false);
            setFormData({ npsn: "", name: "", password: "" });
        } catch (error) {
            console.error("Error creating school:", error);
        }
    };

    const handleEditSchool = async () => {
        try {
            await updateSchoolMutation.mutateAsync({
                id: editingSchool.id,
                ...formData,
            });
            setShowEditModal(false);
            setEditingSchool(null);
            setFormData({ npsn: "", name: "", password: "" });
        } catch (error) {
            console.error("Error updating school:", error);
        }
    };

    const handleDeleteSchool = async (schoolId) => {
        if (confirm("Apakah Anda yakin ingin menghapus sekolah ini?")) {
            try {
                await deleteSchoolMutation.mutateAsync(schoolId);
            } catch (error) {
                console.error("Error deleting school:", error);
            }
        }
    };

    const openEditModal = (school) => {
        setEditingSchool(school);
        setFormData({
            npsn: school.npsn,
            name: school.name,
            password: "",
        });
        setShowEditModal(true);
    };

    if (error) {
        return (
            <SuperAdminLayout>
                <Head title="Kelola Sekolah" />
                <div className="p-4 sm:p-6">
                    <div className="bg-red-50 border border-red-200 rounded-md p-4">
                        <p className="text-red-800">
                            Error loading schools: {error.message}
                        </p>
                    </div>
                </div>
            </SuperAdminLayout>
        );
    }

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
                                    className="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-maroon-600 hover:bg-maroon-700"
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
                            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 text-sm"
                        />
                    </div>
                </div>

                <div className="bg-white shadow overflow-hidden rounded-lg">
                    {isLoading ? (
                        <div className="px-4 py-8 text-center">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-maroon-600 mx-auto"></div>
                            <p className="mt-2 text-sm text-gray-500">
                                Loading schools...
                            </p>
                        </div>
                    ) : (
                        <ul className="divide-y divide-gray-200">
                            {filteredSchools.map((school) => (
                                <li key={school.id}>
                                    <div className="px-4 py-4 sm:px-6">
                                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                                            <div className="flex items-center">
                                                <div className="flex-shrink-0">
                                                    <Building2 className="h-6 w-6 sm:h-8 sm:w-8 text-maroon-600" />
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
                                                <Link
                                                    href={`/super-admin/schools/${school.id}`}
                                                    className="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Lihat Detail
                                                </Link>
                                                <button
                                                    onClick={() =>
                                                        openEditModal(school)
                                                    }
                                                    className="text-maroon-600 hover:text-maroon-900"
                                                >
                                                    <Edit className="h-4 w-4" />
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDeleteSchool(
                                                            school.id
                                                        )
                                                    }
                                                    className="text-red-600 hover:text-red-900"
                                                    disabled={
                                                        deleteSchoolMutation.isLoading
                                                    }
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    )}
                </div>

                {/* Pagination */}
                {schools?.links && (
                    <div className="mt-4 flex justify-center">
                        <nav className="flex space-x-2">
                            {schools.links.map((link, index) => (
                                <button
                                    key={index}
                                    onClick={() => {
                                        if (link.url) {
                                            const url = new URL(link.url);
                                            const page =
                                                url.searchParams.get("page") ||
                                                1;
                                            setCurrentPage(parseInt(page));
                                        }
                                    }}
                                    disabled={!link.url}
                                    className={`px-3 py-2 text-sm font-medium rounded-md ${
                                        link.active
                                            ? "bg-maroon-600 text-white"
                                            : link.url
                                            ? "bg-white text-gray-700 hover:bg-gray-50 border border-gray-300"
                                            : "bg-gray-100 text-gray-400 cursor-not-allowed"
                                    }`}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            ))}
                        </nav>
                    </div>
                )}

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
                                            value={formData.npsn}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    npsn: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Masukkan NPSN"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Nama Sekolah
                                        </label>
                                        <input
                                            type="text"
                                            value={formData.name}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    name: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Masukkan nama sekolah"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Password Default
                                        </label>
                                        <input
                                            type="password"
                                            value={formData.password}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    password: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Masukkan password default"
                                        />
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
                                        disabled={
                                            createSchoolMutation.isLoading
                                        }
                                        className="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-maroon-600 rounded-md hover:bg-maroon-700 disabled:opacity-50"
                                    >
                                        {createSchoolMutation.isLoading
                                            ? "Menyimpan..."
                                            : "Simpan"}
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
                                            value={formData.npsn}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    npsn: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Nama Sekolah
                                        </label>
                                        <input
                                            type="text"
                                            value={formData.name}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    name: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Password Baru (opsional)
                                        </label>
                                        <input
                                            type="password"
                                            value={formData.password}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    password: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Kosongkan jika tidak ingin mengubah password"
                                        />
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
                                        disabled={
                                            updateSchoolMutation.isLoading
                                        }
                                        className="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-maroon-600 rounded-md hover:bg-maroon-700 disabled:opacity-50"
                                    >
                                        {updateSchoolMutation.isLoading
                                            ? "Menyimpan..."
                                            : "Simpan"}
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
                        // Refresh the data
                        window.location.reload();
                    }}
                />
            </div>
        </SuperAdminLayout>
    );
}
