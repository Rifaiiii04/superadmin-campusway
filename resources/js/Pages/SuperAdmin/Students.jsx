import React, { useState } from "react";
import { Head, useForm, Link } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { User, Plus, Edit, Trash2, Download, Search, Eye } from "lucide-react";

export default function Students({ students, schools = [], debug, error }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [editingStudent, setEditingStudent] = useState(null);
    const [searchTerm, setSearchTerm] = useState("");
    const [statusFilter, setStatusFilter] = useState("all");

    // Debug logging
    console.log("Students component - students:", students);
    console.log("Students component - schools:", schools);
    console.log("Students component - debug:", debug);
    console.log("Students component - error:", error);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        nisn: "",
        name: "",
        school_id: "",
        kelas: "",
        email: "",
        phone: "",
        parent_phone: "",
        password: "",
        status: "active",
    });

    const filteredStudents = (students?.data || []).filter((student) => {
        const matchesSearch =
            searchTerm === "" ||
            student.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            student.nisn.includes(searchTerm) ||
            student.school?.name
                .toLowerCase()
                .includes(searchTerm.toLowerCase());

        const matchesStatus =
            statusFilter === "all" || student.status === statusFilter;

        return matchesSearch && matchesStatus;
    });

    const handleAddStudent = () => {
        post("/students", {
            onSuccess: () => {
                setShowAddModal(false);
                reset();
            },
        });
    };

    const handleEditStudent = () => {
        put(`/students/${editingStudent.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setEditingStudent(null);
                reset();
            },
        });
    };

    const openEditModal = (student) => {
        setEditingStudent(student);
        setData({
            nisn: student.nisn,
            name: student.name,
            school_id: student.school_id,
            kelas: student.kelas,
            email: student.email || "",
            phone: student.phone || "",
            parent_phone: student.parent_phone || "",
            password: "",
            status: student.status,
        });
        setShowEditModal(true);
    };

    return (
        <SuperAdminLayout>
            <Head title="Manajemen Siswa" />

            <div className="p-4 sm:p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-4 sm:mb-6">
                    <div className="px-4 sm:px-6 py-4">
                        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                            <div>
                                <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
                                    Manajemen Siswa
                                </h1>
                                <p className="mt-1 text-sm text-gray-500">
                                    Daftar dan kelola semua siswa terdaftar
                                </p>
                            </div>
                            <div className="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                                <button
                                    onClick={() =>
                                        (window.location.href =
                                            "/students/export")
                                    }
                                    className="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    <Download className="h-4 w-4 mr-2" />
                                    <span className="hidden sm:inline">
                                        Export CSV
                                    </span>
                                    <span className="sm:hidden">Export</span>
                                </button>
                                <button
                                    onClick={() => setShowAddModal(true)}
                                    className="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-maroon-600 hover:bg-maroon-700"
                                >
                                    <Plus className="h-4 w-4 mr-2" />
                                    <span className="hidden sm:inline">
                                        Tambah Siswa
                                    </span>
                                    <span className="sm:hidden">Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div className="bg-white rounded-lg shadow-sm border p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-blue-500">
                                <User className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-600">
                                    Total Siswa
                                </p>
                                <p className="text-xl font-bold text-gray-900">
                                    {students?.total || 0}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-green-500">
                                <span className="text-white text-lg">✓</span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-green-700">
                                    Aktif
                                </p>
                                <p className="text-xl font-bold text-green-900">
                                    {
                                        (students?.data || []).filter(
                                            (s) => s.status === "active"
                                        ).length
                                    }
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200 p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-yellow-500">
                                <span className="text-white text-lg">⏸</span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-yellow-700">
                                    Non-Aktif
                                </p>
                                <p className="text-xl font-bold text-yellow-900">
                                    {
                                        (students?.data || []).filter(
                                            (s) => s.status === "inactive"
                                        ).length
                                    }
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-purple-50 rounded-lg shadow-sm border border-purple-200 p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-purple-500">
                                <span className="text-white text-lg">🏫</span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-purple-700">
                                    Sekolah
                                </p>
                                <p className="text-xl font-bold text-purple-900">
                                    {
                                        new Set(
                                            (students?.data || []).map(
                                                (s) => s.school_id
                                            )
                                        ).size
                                    }
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Search and Filter */}
                <div className="bg-white shadow-sm border rounded-lg mb-4 sm:mb-6">
                    <div className="px-4 sm:px-6 py-4">
                        <div className="flex flex-col sm:flex-row gap-3">
                            <div className="relative flex-1">
                                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Cari siswa, NISN, atau sekolah..."
                                    value={searchTerm}
                                    onChange={(e) =>
                                        setSearchTerm(e.target.value)
                                    }
                                    className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 text-sm"
                                />
                            </div>
                            <select
                                value={statusFilter}
                                onChange={(e) =>
                                    setStatusFilter(e.target.value)
                                }
                                className="block w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 text-sm"
                            >
                                <option value="all">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                {/* Students Table */}
                <div className="bg-white shadow overflow-hidden rounded-lg">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Siswa
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NISN
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sekolah
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kelas
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
                                {filteredStudents.map((student) => (
                                    <tr key={student.id}>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex items-center">
                                                <div className="flex-shrink-0 h-10 w-10">
                                                    <div className="h-10 w-10 rounded-full bg-maroon-100 flex items-center justify-center">
                                                        <User className="h-5 w-5 text-maroon-600" />
                                                    </div>
                                                </div>
                                                <div className="ml-4">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {student.name}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        {student.email ||
                                                            "Tidak ada email"}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {student.nisn}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {student.school?.name ||
                                                "Tidak ada sekolah"}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {student.kelas}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span
                                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    student.status === "active"
                                                        ? "bg-green-100 text-green-800"
                                                        : "bg-red-100 text-red-800"
                                                }`}
                                            >
                                                {student.status === "active"
                                                    ? "Aktif"
                                                    : "Non-Aktif"}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div className="flex space-x-2">
                                                <Link
                                                    href={`/students/${student.id}`}
                                                    className="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>
                                                <button
                                                    onClick={() =>
                                                        openEditModal(student)
                                                    }
                                                    className="text-maroon-600 hover:text-maroon-900"
                                                >
                                                    <Edit className="h-4 w-4" />
                                                </button>
                                                <Link
                                                    href={`/students/${student.id}`}
                                                    method="delete"
                                                    as="button"
                                                    className="text-red-600 hover:text-red-900"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* No Results Message */}
                    {filteredStudents.length === 0 && (
                        <div className="text-center py-12">
                            <div className="text-gray-500">
                                <Search className="mx-auto h-12 w-12 text-gray-400 mb-4" />
                                <h3 className="text-lg font-medium text-gray-900 mb-2">
                                    Tidak ada siswa ditemukan
                                </h3>
                                <p className="text-sm text-gray-500">
                                    {searchTerm !== ""
                                        ? `Tidak ada siswa yang cocok dengan pencarian "${searchTerm}"`
                                        : "Tidak ada siswa dengan status yang dipilih"}
                                </p>
                            </div>
                        </div>
                    )}
                </div>

                {/* Add Student Modal */}
                {showAddModal && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div className="relative top-10 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                            <div className="mt-3">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">
                                    Tambah Siswa Baru
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            NISN
                                        </label>
                                        <input
                                            type="text"
                                            value={data.nisn}
                                            onChange={(e) =>
                                                setData("nisn", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Masukkan NISN"
                                        />
                                        {errors.nisn && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.nisn}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Nama Lengkap
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData("name", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Masukkan nama lengkap"
                                        />
                                        {errors.name && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Sekolah
                                        </label>
                                        <select
                                            value={data.school_id}
                                            onChange={(e) =>
                                                setData(
                                                    "school_id",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        >
                                            <option value="">
                                                Pilih Sekolah
                                            </option>
                                            {schools.map((school) => (
                                                <option
                                                    key={school.id}
                                                    value={school.id}
                                                >
                                                    {school.name}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.school_id && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.school_id}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kelas
                                        </label>
                                        <input
                                            type="text"
                                            value={data.kelas}
                                            onChange={(e) =>
                                                setData("kelas", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Contoh: X IPA 1"
                                        />
                                        {errors.kelas && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.kelas}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Email
                                        </label>
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={(e) =>
                                                setData("email", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="email@example.com"
                                        />
                                        {errors.email && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            No. Telepon
                                        </label>
                                        <input
                                            type="text"
                                            value={data.phone}
                                            onChange={(e) =>
                                                setData("phone", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="08xxxxxxxxxx"
                                        />
                                        {errors.phone && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.phone}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            No. Telepon Orang Tua
                                        </label>
                                        <input
                                            type="text"
                                            value={data.parent_phone}
                                            onChange={(e) =>
                                                setData(
                                                    "parent_phone",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="08xxxxxxxxxx"
                                        />
                                        {errors.parent_phone && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.parent_phone}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Password
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
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Password (opsional)"
                                        />
                                        {errors.password && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.password}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Status
                                        </label>
                                        <select
                                            value={data.status}
                                            onChange={(e) =>
                                                setData(
                                                    "status",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        >
                                            <option value="active">
                                                Aktif
                                            </option>
                                            <option value="inactive">
                                                Non-Aktif
                                            </option>
                                        </select>
                                        {errors.status && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.status}
                                            </p>
                                        )}
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
                                        onClick={handleAddStudent}
                                        disabled={processing}
                                        className="px-4 py-2 text-sm font-medium text-white bg-maroon-600 border border-transparent rounded-md hover:bg-maroon-700 disabled:opacity-50"
                                    >
                                        {processing ? "Menyimpan..." : "Simpan"}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Edit Student Modal */}
                {showEditModal && editingStudent && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div className="relative top-10 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                            <div className="mt-3">
                                <h3 className="text-lg font-medium text-gray-900 mb-4">
                                    Edit Siswa: {editingStudent.name}
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            NISN
                                        </label>
                                        <input
                                            type="text"
                                            value={data.nisn}
                                            onChange={(e) =>
                                                setData("nisn", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        />
                                        {errors.nisn && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.nisn}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Nama Lengkap
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData("name", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        />
                                        {errors.name && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Sekolah
                                        </label>
                                        <select
                                            value={data.school_id}
                                            onChange={(e) =>
                                                setData(
                                                    "school_id",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        >
                                            <option value="">
                                                Pilih Sekolah
                                            </option>
                                            {schools.map((school) => (
                                                <option
                                                    key={school.id}
                                                    value={school.id}
                                                >
                                                    {school.name}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.school_id && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.school_id}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Kelas
                                        </label>
                                        <input
                                            type="text"
                                            value={data.kelas}
                                            onChange={(e) =>
                                                setData("kelas", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        />
                                        {errors.kelas && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.kelas}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Email
                                        </label>
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={(e) =>
                                                setData("email", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        />
                                        {errors.email && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            No. Telepon
                                        </label>
                                        <input
                                            type="text"
                                            value={data.phone}
                                            onChange={(e) =>
                                                setData("phone", e.target.value)
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        />
                                        {errors.phone && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.phone}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            No. Telepon Orang Tua
                                        </label>
                                        <input
                                            type="text"
                                            value={data.parent_phone}
                                            onChange={(e) =>
                                                setData(
                                                    "parent_phone",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        />
                                        {errors.parent_phone && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.parent_phone}
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
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                            placeholder="Kosongkan jika tidak ingin mengubah password"
                                        />
                                        {errors.password && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.password}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Status
                                        </label>
                                        <select
                                            value={data.status}
                                            onChange={(e) =>
                                                setData(
                                                    "status",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-maroon-500 focus:border-maroon-500"
                                        >
                                            <option value="active">
                                                Aktif
                                            </option>
                                            <option value="inactive">
                                                Non-Aktif
                                            </option>
                                        </select>
                                        {errors.status && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.status}
                                            </p>
                                        )}
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
                                        onClick={handleEditStudent}
                                        disabled={processing}
                                        className="px-4 py-2 text-sm font-medium text-white bg-maroon-600 border border-transparent rounded-md hover:bg-maroon-700 disabled:opacity-50"
                                    >
                                        {processing ? "Menyimpan..." : "Update"}
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
