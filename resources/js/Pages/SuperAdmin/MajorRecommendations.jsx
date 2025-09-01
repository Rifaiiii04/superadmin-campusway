import React, { useState } from "react";
import { Head, useForm, router } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import {
    Plus,
    Edit,
    Trash2,
    Eye,
    EyeOff,
    BookOpen,
    Target,
    Users,
    Download,
    Search,
} from "lucide-react";

export default function MajorRecommendations({ majorRecommendations = [] }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [editingMajor, setEditingMajor] = useState(null);
    const [showEditModal, setShowEditModal] = useState(false);
    const [selectedMajor, setSelectedMajor] = useState(null);
    const [showDetailModal, setShowDetailModal] = useState(false);
    const [searchTerm, setSearchTerm] = useState("");
    const [statusFilter, setStatusFilter] = useState("all"); // all, active, inactive

    const {
        data,
        setData,
        post,
        put,
        delete: destroy,
        processing,
        errors,
        reset,
    } = useForm({
        major_name: "",
        description: "",
        required_subjects: [],
        preferred_subjects: [],
        kurikulum_merdeka_subjects: [],
        kurikulum_2013_ipa_subjects: [],
        kurikulum_2013_ips_subjects: [],
        kurikulum_2013_bahasa_subjects: [],
        career_prospects: "",
        is_active: true,
    });

    const availableSubjects = [
        "Bahasa Indonesia",
        "Bahasa Indonesia Tingkat Lanjut",
        "Bahasa Inggris",
        "Bahasa Inggris Tingkat Lanjut",
        "Bahasa Asing",
        "Bahasa dan Sastra Inggris",
        "Matematika",
        "Matematika Tingkat Lanjut",
        "Fisika",
        "Kimia",
        "Biologi",
        "Ekonomi",
        "Sejarah",
        "Sejarah Indonesia",
        "Geografi",
        "Sosiologi",
        "Antropologi",
        "PPKn",
        "Pendidikan Pancasila",
        "Seni Budaya",
        "PJOK",
    ];

    const handleAddMajor = () => {
        post("/super-admin/major-recommendations", {
            onSuccess: () => {
                setShowAddModal(false);
                reset();
            },
        });
    };

    const handleEditMajor = () => {
        put(`/super-admin/major-recommendations/${editingMajor.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setEditingMajor(null);
                reset();
            },
        });
    };

    const handleDeleteMajor = (id) => {
        if (
            confirm(
                "Apakah Anda yakin ingin menghapus rekomendasi jurusan ini?"
            )
        ) {
            destroy(`/super-admin/major-recommendations/${id}`);
        }
    };

    const handleToggleStatus = (id) => {
        // Use Inertia patch for toggle
        router.patch(`/super-admin/major-recommendations/${id}/toggle`);
    };

    // Filter majors based on search term and status
    const filteredMajors = majorRecommendations.filter((major) => {
        const matchesSearch =
            searchTerm === "" ||
            major.major_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            major.description
                ?.toLowerCase()
                .includes(searchTerm.toLowerCase()) ||
            major.required_subjects?.some((subject) =>
                subject.toLowerCase().includes(searchTerm.toLowerCase())
            ) ||
            major.preferred_subjects?.some((subject) =>
                subject.toLowerCase().includes(searchTerm.toLowerCase())
            ) ||
            major.kurikulum_merdeka_subjects?.some((subject) =>
                subject.toLowerCase().includes(searchTerm.toLowerCase())
            ) ||
            major.kurikulum_2013_ipa_subjects?.some((subject) =>
                subject.toLowerCase().includes(searchTerm.toLowerCase())
            ) ||
            major.kurikulum_2013_ips_subjects?.some((subject) =>
                subject.toLowerCase().includes(searchTerm.toLowerCase())
            ) ||
            major.kurikulum_2013_bahasa_subjects?.some((subject) =>
                subject.toLowerCase().includes(searchTerm.toLowerCase())
            );

        const matchesStatus =
            statusFilter === "all" ||
            (statusFilter === "active" && major.is_active) ||
            (statusFilter === "inactive" && !major.is_active);

        return matchesSearch && matchesStatus;
    });

    const openEditModal = (major) => {
        setEditingMajor(major);
        setData({
            major_name: major.major_name,
            description: major.description,
            required_subjects: major.required_subjects || [],
            preferred_subjects: major.preferred_subjects || [],
            kurikulum_merdeka_subjects: major.kurikulum_merdeka_subjects || [],
            kurikulum_2013_ipa_subjects:
                major.kurikulum_2013_ipa_subjects || [],
            kurikulum_2013_ips_subjects:
                major.kurikulum_2013_ips_subjects || [],
            kurikulum_2013_bahasa_subjects:
                major.kurikulum_2013_bahasa_subjects || [],
            career_prospects: major.career_prospects,
            is_active: major.is_active,
        });
        setShowEditModal(true);
    };

    const openDetailModal = (major) => {
        setSelectedMajor(major);
        setShowDetailModal(true);
    };

    const toggleSubject = (subject, type) => {
        const currentSubjects = data[type] || [];
        if (currentSubjects.includes(subject)) {
            setData(
                type,
                currentSubjects.filter((s) => s !== subject)
            );
        } else {
            setData(type, [...currentSubjects, subject]);
        }
    };

    return (
        <SuperAdminLayout>
            <Head title="Rekomendasi Jurusan" />

            <div className="p-4 sm:p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-4 sm:mb-6">
                    <div className="px-4 sm:px-6 py-4">
                        <div className="flex justify-between items-center">
                            <div>
                                <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
                                    Rekomendasi Jurusan
                                </h1>
                                <p className="mt-1 text-sm text-gray-500">
                                    Kelola kriteria dan rekomendasi jurusan
                                    untuk siswa
                                </p>
                            </div>
                            <div className="flex gap-2">
                                <button
                                    onClick={() =>
                                        (window.location.href =
                                            "/super-admin/major-recommendations/export")
                                    }
                                    className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
                                >
                                    <Download className="h-4 w-4" />
                                    Export CSV
                                </button>
                                <button
                                    onClick={() => setShowAddModal(true)}
                                    className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
                                >
                                    <Plus className="h-4 w-4" />
                                    Tambah Jurusan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div className="bg-white rounded-lg shadow-sm border p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-blue-500">
                                <BookOpen className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-600">
                                    Total Jurusan
                                </p>
                                <p className="text-xl font-bold text-gray-900">
                                    {majorRecommendations.length}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg shadow-sm border p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-green-500">
                                <Target className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-600">
                                    Jurusan Aktif
                                </p>
                                <p className="text-xl font-bold text-gray-900">
                                    {
                                        majorRecommendations.filter(
                                            (m) => m.is_active
                                        ).length
                                    }
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white rounded-lg shadow-sm border p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-purple-500">
                                <Users className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-600">
                                    Jurusan Non-Aktif
                                </p>
                                <p className="text-xl font-bold text-gray-900">
                                    {
                                        majorRecommendations.filter(
                                            (m) => !m.is_active
                                        ).length
                                    }
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Filtered Results Summary */}
                {(searchTerm !== "" || statusFilter !== "all") && (
                    <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-blue-500">
                                <Search className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-blue-800">
                                    Hasil Pencarian
                                </p>
                                <p className="text-lg font-bold text-blue-900">
                                    {filteredMajors.length} jurusan ditemukan
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Major Recommendations Table */}
                <div className="bg-white shadow-sm border rounded-lg">
                    <div className="px-4 sm:px-6 py-4 border-b">
                        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 className="text-lg font-semibold text-gray-900">
                                Daftar Rekomendasi Jurusan
                            </h3>

                            {/* Search and Filter */}
                            <div className="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                {/* Search Bar */}
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <Search className="h-4 w-4 text-gray-400" />
                                    </div>
                                    <input
                                        type="text"
                                        placeholder="Cari jurusan, mata pelajaran..."
                                        value={searchTerm}
                                        onChange={(e) =>
                                            setSearchTerm(e.target.value)
                                        }
                                        className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    />
                                </div>

                                {/* Status Filter */}
                                <select
                                    value={statusFilter}
                                    onChange={(e) =>
                                        setStatusFilter(e.target.value)
                                    }
                                    className="block w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                    <option value="all">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Non-Aktif</option>
                                </select>
                            </div>
                        </div>

                        {/* Search Results Info */}
                        {searchTerm !== "" || statusFilter !== "all" ? (
                            <div className="mt-3 text-sm text-gray-600">
                                Menampilkan {filteredMajors.length} dari{" "}
                                {majorRecommendations.length} jurusan
                                {searchTerm !== "" && (
                                    <span className="ml-2">
                                        untuk pencarian:{" "}
                                        <span className="font-medium">
                                            "{searchTerm}"
                                        </span>
                                    </span>
                                )}
                                {statusFilter !== "all" && (
                                    <span className="ml-2">
                                        dengan status:{" "}
                                        <span className="font-medium">
                                            {statusFilter === "active"
                                                ? "Aktif"
                                                : "Non-Aktif"}
                                        </span>
                                    </span>
                                )}
                            </div>
                        ) : null}
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jurusan
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mata Pelajaran Wajib
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mata Pelajaran Referensi
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {filteredMajors.map((major) => (
                                    <tr key={major.id}>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    {major.major_name}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {major.description}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex flex-wrap gap-1">
                                                {major.required_subjects?.map(
                                                    (subject, index) => (
                                                        <span
                                                            key={index}
                                                            className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                        >
                                                            {subject}
                                                        </span>
                                                    )
                                                )}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex flex-wrap gap-1">
                                                {major.preferred_subjects?.map(
                                                    (subject, index) => (
                                                        <span
                                                            key={index}
                                                            className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                        >
                                                            {subject}
                                                        </span>
                                                    )
                                                )}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span
                                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    major.is_active
                                                        ? "bg-green-100 text-green-800"
                                                        : "bg-red-100 text-red-800"
                                                }`}
                                            >
                                                {major.is_active
                                                    ? "Aktif"
                                                    : "Non-Aktif"}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div className="flex space-x-2">
                                                <button
                                                    onClick={() =>
                                                        openDetailModal(major)
                                                    }
                                                    className="text-blue-600 hover:text-blue-900"
                                                    title="Lihat Detail"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        openEditModal(major)
                                                    }
                                                    className="text-indigo-600 hover:text-indigo-900"
                                                    title="Edit"
                                                >
                                                    <Edit className="h-4 w-4" />
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleToggleStatus(
                                                            major.id
                                                        )
                                                    }
                                                    className="text-yellow-600 hover:text-yellow-900"
                                                >
                                                    {major.is_active ? (
                                                        <EyeOff className="h-4 w-4" />
                                                    ) : (
                                                        <Eye className="h-4 w-4" />
                                                    )}
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDeleteMajor(
                                                            major.id
                                                        )
                                                    }
                                                    className="text-red-600 hover:text-red-900"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        {/* No Results Message */}
                        {filteredMajors.length === 0 && (
                            <div className="text-center py-12">
                                <div className="text-gray-500">
                                    <Search className="mx-auto h-12 w-12 text-gray-400 mb-4" />
                                    <h3 className="text-lg font-medium text-gray-900 mb-2">
                                        Tidak ada hasil ditemukan
                                    </h3>
                                    <p className="text-sm text-gray-500">
                                        {searchTerm !== ""
                                            ? `Tidak ada jurusan yang cocok dengan pencarian "${searchTerm}"`
                                            : "Tidak ada jurusan dengan status yang dipilih"}
                                    </p>
                                    {(searchTerm !== "" ||
                                        statusFilter !== "all") && (
                                        <button
                                            onClick={() => {
                                                setSearchTerm("");
                                                setStatusFilter("all");
                                            }}
                                            className="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200"
                                        >
                                            Reset Filter
                                        </button>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                {/* Add Major Modal */}
                {showAddModal && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div className="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                            <div className="mt-3">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">
                                    Tambah Jurusan Baru
                                </h3>

                                <div className="space-y-4 max-h-96 overflow-y-auto">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Nama Jurusan
                                        </label>
                                        <input
                                            type="text"
                                            value={data.major_name}
                                            onChange={(e) =>
                                                setData(
                                                    "major_name",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                            placeholder="Contoh: Teknik Informatika"
                                        />
                                        {errors.major_name && (
                                            <p className="text-red-500 text-xs mt-1">
                                                {errors.major_name}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Deskripsi
                                        </label>
                                        <textarea
                                            value={data.description}
                                            onChange={(e) =>
                                                setData(
                                                    "description",
                                                    e.target.value
                                                )
                                            }
                                            rows="3"
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                            placeholder="Deskripsi singkat jurusan"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Mata Pelajaran Wajib
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.required_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "required_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-blue-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Mata Pelajaran Preferensi
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.preferred_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "preferred_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-green-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kurikulum Merdeka
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.kurikulum_merdeka_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "kurikulum_merdeka_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-purple-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kurikulum 2013 - IPA
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.kurikulum_2013_ipa_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "kurikulum_2013_ipa_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-red-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kurikulum 2013 - IPS
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.kurikulum_2013_ips_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "kurikulum_2013_ips_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-yellow-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kurikulum 2013 - Bahasa
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.kurikulum_2013_bahasa_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "kurikulum_2013_bahasa_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-indigo-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Prospek Karir
                                        </label>
                                        <textarea
                                            value={data.career_prospects}
                                            onChange={(e) =>
                                                setData(
                                                    "career_prospects",
                                                    e.target.value
                                                )
                                            }
                                            rows="2"
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                            placeholder="Contoh: Software Engineer, Data Scientist"
                                        />
                                    </div>

                                    <div className="flex items-center">
                                        <input
                                            type="checkbox"
                                            checked={data.is_active}
                                            onChange={(e) =>
                                                setData(
                                                    "is_active",
                                                    e.target.checked
                                                )
                                            }
                                            className="rounded border-gray-300 text-blue-600"
                                        />
                                        <span className="ml-2 text-sm text-gray-700">
                                            Jurusan Aktif
                                        </span>
                                    </div>
                                </div>

                                <div className="flex justify-end space-x-3 mt-6">
                                    <button
                                        onClick={() => setShowAddModal(false)}
                                        className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        onClick={handleAddMajor}
                                        disabled={processing}
                                        className="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {processing ? "Menyimpan..." : "Simpan"}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Edit Major Modal */}
                {showEditModal && editingMajor && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div className="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                            <div className="mt-3">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">
                                    Edit Jurusan: {editingMajor.major_name}
                                </h3>

                                <div className="space-y-4 max-h-96 overflow-y-auto">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Nama Jurusan
                                        </label>
                                        <input
                                            type="text"
                                            value={data.major_name}
                                            onChange={(e) =>
                                                setData(
                                                    "major_name",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                        />
                                        {errors.major_name && (
                                            <p className="text-red-500 text-xs mt-1">
                                                {errors.major_name}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Deskripsi
                                        </label>
                                        <textarea
                                            value={data.description}
                                            onChange={(e) =>
                                                setData(
                                                    "description",
                                                    e.target.value
                                                )
                                            }
                                            rows="3"
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Mata Pelajaran Wajib
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.required_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "required_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-blue-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Mata Pelajaran Preferensi
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.preferred_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "preferred_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-green-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kurikulum Merdeka
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.kurikulum_merdeka_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "kurikulum_merdeka_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-purple-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kurikulum 2013 - IPA
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.kurikulum_2013_ipa_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "kurikulum_2013_ipa_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-red-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kurikulum 2013 - IPS
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.kurikulum_2013_ips_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "kurikulum_2013_ips_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-yellow-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kurikulum 2013 - Bahasa
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject) => (
                                                    <label
                                                        key={subject}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="checkbox"
                                                            checked={data.kurikulum_2013_bahasa_subjects?.includes(
                                                                subject
                                                            )}
                                                            onChange={() =>
                                                                toggleSubject(
                                                                    subject,
                                                                    "kurikulum_2013_bahasa_subjects"
                                                                )
                                                            }
                                                            className="rounded border-gray-300 text-indigo-600"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700">
                                                            {subject}
                                                        </span>
                                                    </label>
                                                )
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Prospek Karir
                                        </label>
                                        <textarea
                                            value={data.career_prospects}
                                            onChange={(e) =>
                                                setData(
                                                    "career_prospects",
                                                    e.target.value
                                                )
                                            }
                                            rows="2"
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                        />
                                    </div>

                                    <div className="flex items-center">
                                        <input
                                            type="checkbox"
                                            checked={data.is_active}
                                            onChange={(e) =>
                                                setData(
                                                    "is_active",
                                                    e.target.checked
                                                )
                                            }
                                            className="rounded border-gray-300 text-blue-600"
                                        />
                                        <span className="ml-2 text-sm text-gray-700">
                                            Jurusan Aktif
                                        </span>
                                    </div>
                                </div>

                                <div className="flex justify-end space-x-3 mt-6">
                                    <button
                                        onClick={() => setShowEditModal(false)}
                                        className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        onClick={handleEditMajor}
                                        disabled={processing}
                                        className="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {processing ? "Menyimpan..." : "Update"}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Detail Modal */}
                {showDetailModal && selectedMajor && (
                    <div className="fixed inset-0 z-50 overflow-y-auto">
                        <div className="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div className="fixed inset-0 transition-opacity">
                                <div className="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>

                            <div className="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                                <div className="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div className="sm:flex sm:items-start">
                                        <div className="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                            <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                                Detail Jurusan:{" "}
                                                {selectedMajor.major_name}
                                            </h3>

                                            <div className="space-y-6">
                                                {/* Deskripsi */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Deskripsi
                                                    </h4>
                                                    <p className="text-sm text-gray-600">
                                                        {
                                                            selectedMajor.description
                                                        }
                                                    </p>
                                                </div>

                                                {/* Mata Pelajaran Wajib */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Mata Pelajaran Wajib
                                                    </h4>
                                                    <div className="flex flex-wrap gap-2">
                                                        {selectedMajor.required_subjects?.map(
                                                            (
                                                                subject,
                                                                index
                                                            ) => (
                                                                <span
                                                                    key={index}
                                                                    className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                                >
                                                                    {subject}
                                                                </span>
                                                            )
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Mata Pelajaran Referensi */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Mata Pelajaran Referensi
                                                    </h4>
                                                    <div className="flex flex-wrap gap-2">
                                                        {selectedMajor.preferred_subjects?.map(
                                                            (
                                                                subject,
                                                                index
                                                            ) => (
                                                                <span
                                                                    key={index}
                                                                    className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                                >
                                                                    {subject}
                                                                </span>
                                                            )
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Kurikulum Merdeka */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Kurikulum Merdeka
                                                    </h4>
                                                    <div className="flex flex-wrap gap-2">
                                                        {selectedMajor.kurikulum_merdeka_subjects?.map(
                                                            (
                                                                subject,
                                                                index
                                                            ) => (
                                                                <span
                                                                    key={index}
                                                                    className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                                                                >
                                                                    {subject}
                                                                </span>
                                                            )
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Kurikulum 2013 - IPA */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Kurikulum 2013 - IPA
                                                    </h4>
                                                    <div className="flex flex-wrap gap-2">
                                                        {selectedMajor.kurikulum_2013_ipa_subjects?.map(
                                                            (
                                                                subject,
                                                                index
                                                            ) => (
                                                                <span
                                                                    key={index}
                                                                    className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                                >
                                                                    {subject}
                                                                </span>
                                                            )
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Kurikulum 2013 - IPS */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Kurikulum 2013 - IPS
                                                    </h4>
                                                    <div className="flex flex-wrap gap-2">
                                                        {selectedMajor.kurikulum_2013_ips_subjects?.map(
                                                            (
                                                                subject,
                                                                index
                                                            ) => (
                                                                <span
                                                                    key={index}
                                                                    className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                                                >
                                                                    {subject}
                                                                </span>
                                                            )
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Kurikulum 2013 - Bahasa */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Kurikulum 2013 - Bahasa
                                                    </h4>
                                                    <div className="flex flex-wrap gap-2">
                                                        {selectedMajor.kurikulum_2013_bahasa_subjects?.map(
                                                            (
                                                                subject,
                                                                index
                                                            ) => (
                                                                <span
                                                                    key={index}
                                                                    className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                                                >
                                                                    {subject}
                                                                </span>
                                                            )
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Prospek Karir */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Prospek Karir
                                                    </h4>
                                                    <p className="text-sm text-gray-600">
                                                        {
                                                            selectedMajor.career_prospects
                                                        }
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        onClick={() =>
                                            setShowDetailModal(false)
                                        }
                                        className="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    >
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </SuperAdminLayout>
    );
}
