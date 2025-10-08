import React, { useState, useEffect } from "react";
import { Head } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import DangerButton from "@/Components/DangerButton";
import TextInput from "@/Components/TextInput";
import InputLabel from "@/Components/InputLabel";
import Modal from "@/Components/Modal";
import {
    Calendar,
    Clock,
    Plus,
    Edit,
    Trash2,
    Filter,
    Search,
} from "lucide-react";

export default function TkaSchedules({
    auth,
    schedules = [],
    schools = [],
    error = null,
}) {
    // Debug data
    console.log("TkaSchedules data:", schedules);
    console.log("TkaSchedules.data:", schedules?.data);
    console.log("TkaSchedules.total:", schedules?.total);

    const [schedulesData, setSchedulesData] = useState(schedules?.data || []);
    const [filteredSchedules, setFilteredSchedules] = useState(
        schedules?.data || []
    );
    const [loading, setLoading] = useState(false);
    const [searchTerm, setSearchTerm] = useState("");
    const [statusFilter, setStatusFilter] = useState("all");
    const [typeFilter, setTypeFilter] = useState("all");
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [editingSchedule, setEditingSchedule] = useState(null);
    const [formData, setFormData] = useState({
        title: "",
        description: "",
        start_date: "",
        end_date: "",
        type: "regular",
        status: "scheduled",
        instructions: "",
        target_schools: [],
        is_active: true,
        created_by: "System",
        // PUSMENDIK Essential Fields
        gelombang: "1",
        hari_pelaksanaan: "Hari Pertama",
        exam_venue: "",
        exam_room: "",
        contact_person: "",
        contact_phone: "",
        requirements: "",
        materials_needed: "",
    });

    // Filter schedules
    useEffect(() => {
        let filtered = [...(schedulesData || [])];

        if (searchTerm) {
            filtered = filtered.filter(
                (schedule) =>
                    schedule.title
                        .toLowerCase()
                        .includes(searchTerm.toLowerCase()) ||
                    schedule.description
                        ?.toLowerCase()
                        .includes(searchTerm.toLowerCase())
            );
        }

        if (statusFilter !== "all") {
            filtered = filtered.filter(
                (schedule) => schedule.status === statusFilter
            );
        }

        if (typeFilter !== "all") {
            filtered = filtered.filter(
                (schedule) => schedule.type === typeFilter
            );
        }

        setFilteredSchedules(filtered);
    }, [schedulesData, searchTerm, statusFilter, typeFilter]);

    // Update schedulesData when schedules prop changes
    useEffect(() => {
        setSchedulesData(schedules?.data || []);
        setFilteredSchedules(schedules?.data || []);
    }, [schedules]);

    // Debug CSRF token on component mount
    useEffect(() => {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta?.content || '';
        console.log('🚀 Component Mount CSRF Debug:', {
            metaElement: csrfMeta,
            tokenValue: csrfToken,
            tokenLength: csrfToken.length,
            hasToken: !!csrfToken,
            allMetaTags: document.querySelectorAll('meta'),
            csrfMetaTags: document.querySelectorAll('meta[name="csrf-token"]')
        });
    }, []);

    // Handle form submission
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        // Debug CSRF token
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta?.content || '';
        console.log('🔐 CSRF Debug:', {
            metaElement: csrfMeta,
            tokenValue: csrfToken,
            tokenLength: csrfToken.length,
            hasToken: !!csrfToken
        });

        try {
            // Validate required fields
            if (!formData.title || !formData.start_date || !formData.end_date) {
                alert(
                    "Mohon isi semua field yang wajib diisi (Judul, Tanggal Mulai, Tanggal Selesai)"
                );
                return;
            }

            const url = editingSchedule
                ? `/tka-schedules/${editingSchedule.id}`
                : "/tka-schedules";

            const method = editingSchedule ? "PUT" : "POST";
            
            console.log("🔍 URL Debug:", {
                editingSchedule: editingSchedule,
                url: url,
                method: method,
                isEdit: !!editingSchedule
            });

            // Format data for server
            const submitData = {
                title: formData.title || "",
                description: formData.description || "",
                start_date: formData.start_date || "",
                end_date: formData.end_date || "",
                type: formData.type || "regular",
                instructions: formData.instructions || "",
                target_schools: formData.target_schools || [],
                status: formData.status || "scheduled",
                is_active:
                    formData.is_active !== undefined
                        ? formData.is_active
                        : true,
                created_by: formData.created_by || "System",
            };

            console.log("Submitting data:", submitData);
            console.log("🌐 Request URL:", url);
            console.log("🌐 Request Method:", method);
            console.log("🌐 CSRF Token in headers:", csrfToken);
            console.log("🌐 Full headers:", {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest",
            });

            const response = await fetch(url, {
                method,
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(submitData),
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            console.log("📥 Response Debug:", {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok,
                contentType: contentType,
                headers: Object.fromEntries(response.headers.entries())
            });

            let data;
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
                console.log("📥 JSON Response:", data);
            } else {
                const textResponse = await response.text();
                console.log("📥 HTML Response:", textResponse.substring(0, 500) + "...");
                throw new Error(`Server returned HTML instead of JSON. Status: ${response.status}. Response: ${textResponse.substring(0, 200)}...`);
            }

            if (data.success) {
                setShowCreateModal(false);
                setShowEditModal(false);
                setEditingSchedule(null);
                resetForm();
                // Refresh page to get updated data from database
                await fetchSchedules();
            } else {
                console.error("Error saving schedule:", data.message);
                if (data.errors) {
                    console.error("Validation errors:", data.errors);
                    // You can show these errors to the user
                    alert("Validasi gagal: " + JSON.stringify(data.errors));
                }
            }
        } catch (error) {
            console.error("❌ Error saving schedule:", {
                error: error,
                message: error.message,
                stack: error.stack,
                name: error.name
            });
            alert("Terjadi kesalahan saat menyimpan jadwal: " + error.message);
        } finally {
            setLoading(false);
        }
    };

    // Fetch schedules
    const fetchSchedules = async () => {
        try {
            // Reload the page to get fresh data from server
            window.location.reload();
        } catch (error) {
            console.error("Error fetching schedules:", error);
        }
    };

    // Handle edit
    const handleEdit = (schedule) => {
        setEditingSchedule(schedule);
        setFormData({
            title: schedule.title,
            description: schedule.description || "",
            start_date: schedule.start_date,
            end_date: schedule.end_date,
            type: schedule.type,
            instructions: schedule.instructions || "",
            target_schools: schedule.target_schools || [],
        });
        setShowEditModal(true);
    };

    // Handle delete
    const handleDelete = async (scheduleId) => {
        // Debug CSRF token for delete
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta?.content || '';
        console.log('🗑️ Delete CSRF Debug:', {
            metaElement: csrfMeta,
            tokenValue: csrfToken,
            hasToken: !!csrfToken
        });

        try {
            const response = await fetch(`/tka-schedules/${scheduleId}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                const textResponse = await response.text();
                console.error("Delete error response:", textResponse);
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Refresh page to get updated data from database
                await fetchSchedules();
            } else {
                console.error("Error deleting schedule:", data.message);
            }
        } catch (error) {
            console.error("Error deleting schedule:", error);
        }
    };

    // Handle cancel
    const handleCancel = async (scheduleId) => {
        // Debug CSRF token for cancel
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta?.content || '';
        console.log('❌ Cancel CSRF Debug:', {
            metaElement: csrfMeta,
            tokenValue: csrfToken,
            hasToken: !!csrfToken
        });

        try {
            const response = await fetch(
                `/tka-schedules/${scheduleId}/cancel`,
                {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                // Refresh page to get updated data from database
                await fetchSchedules();
            } else {
                console.error("Error cancelling schedule:", data.message);
            }
        } catch (error) {
            console.error("Error cancelling schedule:", error);
        }
    };

    // Reset form
    const resetForm = () => {
        setFormData({
            title: "",
            description: "",
            start_date: "",
            end_date: "",
            type: "regular",
            status: "scheduled",
            instructions: "",
            target_schools: [],
            is_active: true,
            created_by: "System",
            // PUSMENDIK Essential Fields
            gelombang: "1",
            hari_pelaksanaan: "Hari Pertama",
            exam_venue: "",
            exam_room: "",
            contact_person: "",
            contact_phone: "",
            requirements: "",
            materials_needed: "",
        });
    };

    // Get status badge
    const getStatusBadge = (status) => {
        const badges = {
            scheduled: { variant: "default", text: "Terjadwal" },
            ongoing: { variant: "secondary", text: "Berlangsung" },
            completed: { variant: "outline", text: "Selesai" },
            cancelled: { variant: "destructive", text: "Dibatalkan" },
        };
        return badges[status] || badges.scheduled;
    };

    // Get type badge
    const getTypeBadge = (type) => {
        const badges = {
            regular: { variant: "default", text: "Reguler" },
            makeup: { variant: "secondary", text: "Susulan" },
            special: { variant: "outline", text: "Khusus" },
        };
        return badges[type] || badges.regular;
    };

    // Format date
    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    return (
        <SuperAdminLayout>
            <Head title="Jadwal TKA" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {error && (
                        <div className="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <h3 className="font-bold text-lg">Error:</h3>
                            <p>{error}</p>
                        </div>
                    )}

                    {/* Header */}
                    <div className="flex items-center justify-between mb-6">
                        <div>
                            <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                                Manajemen Jadwal TKA
                            </h2>
                            <p className="text-sm text-gray-600 mt-1">
                                Kelola jadwal pelaksanaan TKA untuk semua
                                sekolah
                            </p>
                        </div>
                        <PrimaryButton
                            onClick={() => setShowCreateModal(true)}
                            className="flex items-center gap-2"
                        >
                            <Plus className="w-4 h-4" />
                            Tambah Jadwal
                        </PrimaryButton>
                    </div>

                    {/* Statistics */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <Calendar className="h-8 w-8 text-maroon-600" />
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Total Jadwal
                                            </dt>
                                            <dd className="text-lg font-medium text-gray-900">
                                                {schedulesData.length}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <Clock className="h-8 w-8 text-green-600" />
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Terjadwal
                                            </dt>
                                            <dd className="text-lg font-medium text-gray-900">
                                                {
                                                    (
                                                        schedulesData || []
                                                    ).filter(
                                                        (s) =>
                                                            s.status ===
                                                            "scheduled"
                                                    ).length
                                                }
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <Clock className="h-8 w-8 text-yellow-600" />
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Sedang Berlangsung
                                            </dt>
                                            <dd className="text-lg font-medium text-gray-900">
                                                {
                                                    (
                                                        schedulesData || []
                                                    ).filter(
                                                        (s) =>
                                                            s.status ===
                                                            "ongoing"
                                                    ).length
                                                }
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <Clock className="h-8 w-8 text-gray-600" />
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Selesai
                                            </dt>
                                            <dd className="text-lg font-medium text-gray-900">
                                                {
                                                    (
                                                        schedulesData || []
                                                    ).filter(
                                                        (s) =>
                                                            s.status ===
                                                            "completed"
                                                    ).length
                                                }
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Filters */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div className="p-6 border-b border-gray-200">
                            <h3 className="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <Filter className="w-5 h-5" />
                                Filter & Pencarian
                            </h3>
                        </div>
                        <div className="p-6">
                            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <InputLabel
                                        htmlFor="search"
                                        value="Pencarian"
                                    />
                                    <div className="relative">
                                        <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                                        <TextInput
                                            id="search"
                                            type="text"
                                            placeholder="Cari jadwal..."
                                            value={searchTerm}
                                            onChange={(e) =>
                                                setSearchTerm(e.target.value)
                                            }
                                            className="pl-10"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <InputLabel
                                        htmlFor="status"
                                        value="Status"
                                    />
                                    <select
                                        id="status"
                                        value={statusFilter}
                                        onChange={(e) =>
                                            setStatusFilter(e.target.value)
                                        }
                                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="all">
                                            Semua Status
                                        </option>
                                        <option value="scheduled">
                                            Terjadwal
                                        </option>
                                        <option value="ongoing">
                                            Berlangsung
                                        </option>
                                        <option value="completed">
                                            Selesai
                                        </option>
                                        <option value="cancelled">
                                            Dibatalkan
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel htmlFor="type" value="Jenis" />
                                    <select
                                        id="type"
                                        value={typeFilter}
                                        onChange={(e) =>
                                            setTypeFilter(e.target.value)
                                        }
                                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="all">Semua Jenis</option>
                                        <option value="regular">Reguler</option>
                                        <option value="makeup">Susulan</option>
                                        <option value="special">Khusus</option>
                                    </select>
                                </div>
                                <div className="flex items-end">
                                    <SecondaryButton
                                        onClick={fetchSchedules}
                                        className="w-full"
                                    >
                                        <Search className="w-4 h-4 mr-2" />
                                        Refresh
                                    </SecondaryButton>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Schedules List */}
                    <div className="space-y-4">
                        {filteredSchedules.length === 0 ? (
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6 text-center">
                                    <Calendar className="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 className="mt-2 text-sm font-medium text-gray-900">
                                        Tidak ada jadwal
                                    </h3>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Mulai dengan membuat jadwal TKA pertama
                                    </p>
                                    <PrimaryButton
                                        onClick={() => setShowCreateModal(true)}
                                        className="mt-4"
                                    >
                                        <Plus className="w-4 h-4 mr-2" />
                                        Tambah Jadwal
                                    </PrimaryButton>
                                </div>
                            </div>
                        ) : (
                            filteredSchedules.map((schedule) => {
                                const statusBadge = getStatusBadge(
                                    schedule.status
                                );
                                const typeBadge = getTypeBadge(schedule.type);

                                return (
                                    <div
                                        key={schedule.id}
                                        className="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow"
                                    >
                                        <div className="p-6 border-b border-gray-200">
                                            <div className="flex items-start justify-between">
                                                <div className="flex-1">
                                                    <h3 className="text-lg font-medium text-gray-900">
                                                        {schedule.title}
                                                    </h3>
                                                    <p className="mt-1 text-sm text-gray-600">
                                                        {schedule.description}
                                                    </p>
                                                </div>
                                                <div className="flex gap-2">
                                                    <span
                                                        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                            statusBadge.variant ===
                                                            "default"
                                                                ? "bg-maroon-100 text-maroon-800"
                                                                : statusBadge.variant ===
                                                                  "secondary"
                                                                ? "bg-green-100 text-green-800"
                                                                : statusBadge.variant ===
                                                                  "outline"
                                                                ? "bg-gray-100 text-gray-800"
                                                                : "bg-red-100 text-red-800"
                                                        }`}
                                                    >
                                                        {statusBadge.text}
                                                    </span>
                                                    <span
                                                        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                            typeBadge.variant ===
                                                            "default"
                                                                ? "bg-maroon-100 text-maroon-800"
                                                                : typeBadge.variant ===
                                                                  "secondary"
                                                                ? "bg-yellow-100 text-yellow-800"
                                                                : "bg-purple-100 text-purple-800"
                                                        }`}
                                                    >
                                                        {typeBadge.text}
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div className="flex items-center gap-3">
                                                    <Calendar className="w-4 h-4 text-gray-500" />
                                                    <div>
                                                        <p className="text-sm font-medium">
                                                            Mulai
                                                        </p>
                                                        <p className="text-sm text-gray-600">
                                                            {formatDate(
                                                                schedule.start_date
                                                            )}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-3">
                                                    <Clock className="w-4 h-4 text-gray-500" />
                                                    <div>
                                                        <p className="text-sm font-medium">
                                                            Selesai
                                                        </p>
                                                        <p className="text-sm text-gray-600">
                                                            {formatDate(
                                                                schedule.end_date
                                                            )}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            {schedule.instructions && (
                                                <div className="mt-4">
                                                    <p className="text-sm font-medium text-gray-700 mb-1">
                                                        Instruksi:
                                                    </p>
                                                    <p className="text-sm text-gray-600">
                                                        {schedule.instructions}
                                                    </p>
                                                </div>
                                            )}

                                            <div className="flex items-center justify-between pt-4 border-t">
                                                <div className="text-sm text-gray-500">
                                                    Dibuat oleh:{" "}
                                                    {schedule.created_by ||
                                                        "Super Admin"}
                                                </div>
                                                <div className="flex gap-2">
                                                    <SecondaryButton
                                                        onClick={() =>
                                                            handleEdit(schedule)
                                                        }
                                                        className="text-sm"
                                                    >
                                                        <Edit className="w-4 h-4 mr-1" />
                                                        Edit
                                                    </SecondaryButton>
                                                    {schedule.status ===
                                                        "scheduled" && (
                                                        <SecondaryButton
                                                            onClick={() =>
                                                                handleCancel(
                                                                    schedule.id
                                                                )
                                                            }
                                                            className="text-sm"
                                                        >
                                                            Batalkan
                                                        </SecondaryButton>
                                                    )}
                                                    <DangerButton
                                                        onClick={() => {
                                                            if (
                                                                window.confirm(
                                                                    `Apakah Anda yakin ingin menghapus jadwal "${schedule.title}"? Tindakan ini tidak dapat dibatalkan.`
                                                                )
                                                            ) {
                                                                handleDelete(
                                                                    schedule.id
                                                                );
                                                            }
                                                        }}
                                                        className="text-sm"
                                                    >
                                                        <Trash2 className="w-4 h-4 mr-1" />
                                                        Hapus
                                                    </DangerButton>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })
                        )}
                    </div>
                </div>
            </div>

            {/* Create/Edit Modal */}
            <Modal
                show={showCreateModal || showEditModal}
                maxWidth="7xl"
                onClose={() => {
                    setShowCreateModal(false);
                    setShowEditModal(false);
                    setEditingSchedule(null);
                    resetForm();
                }}
            >
                <div className="p-8">
                    <h2 className="text-xl font-medium text-gray-900 mb-6">
                        {editingSchedule
                            ? "Edit Jadwal TKA"
                            : "Tambah Jadwal TKA"}
                    </h2>
                    <p className="text-sm text-gray-600 mb-6">
                        {editingSchedule
                            ? "Ubah informasi jadwal TKA"
                            : "Buat jadwal TKA baru"}
                    </p>

                    <form onSubmit={handleSubmit}>
                        <div className="max-h-[70vh] overflow-y-auto space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <InputLabel
                                        htmlFor="title"
                                        value="Judul Jadwal *"
                                    />
                                    <TextInput
                                        id="title"
                                        type="text"
                                        value={formData.title}
                                        onChange={(e) =>
                                            setFormData({
                                                ...formData,
                                                title: e.target.value,
                                            })
                                        }
                                        placeholder="Contoh: TKA Gelombang 1 - Tahun 2024"
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel
                                        htmlFor="type"
                                        value="Jenis TKA *"
                                    />
                                    <select
                                        id="type"
                                        value={formData.type}
                                        onChange={(e) =>
                                            setFormData({
                                                ...formData,
                                                type: e.target.value,
                                            })
                                        }
                                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="regular">Reguler</option>
                                        <option value="makeup">Susulan</option>
                                        <option value="special">Khusus</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="description"
                                    value="Deskripsi"
                                />
                                <textarea
                                    id="description"
                                    value={formData.description}
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            description: e.target.value,
                                        })
                                    }
                                    placeholder="Deskripsi jadwal TKA..."
                                    rows={3}
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <InputLabel
                                        htmlFor="start_date"
                                        value="Tanggal & Waktu Mulai *"
                                    />
                                    <TextInput
                                        id="start_date"
                                        type="datetime-local"
                                        value={formData.start_date}
                                        onChange={(e) =>
                                            setFormData({
                                                ...formData,
                                                start_date: e.target.value,
                                            })
                                        }
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel
                                        htmlFor="end_date"
                                        value="Tanggal & Waktu Selesai *"
                                    />
                                    <TextInput
                                        id="end_date"
                                        type="datetime-local"
                                        value={formData.end_date}
                                        onChange={(e) =>
                                            setFormData({
                                                ...formData,
                                                end_date: e.target.value,
                                            })
                                        }
                                        required
                                    />
                                </div>
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="instructions"
                                    value="Instruksi Khusus"
                                />
                                <textarea
                                    id="instructions"
                                    value={formData.instructions}
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            instructions: e.target.value,
                                        })
                                    }
                                    placeholder="Instruksi khusus untuk peserta..."
                                    rows={3}
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="target_schools"
                                    value="Sekolah Terpilih (Opsional)"
                                />
                                <select
                                    id="target_schools"
                                    value={
                                        formData.target_schools.length > 0
                                            ? formData.target_schools[0]
                                            : ""
                                    }
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            target_schools: e.target.value
                                                ? [parseInt(e.target.value)]
                                                : [],
                                        })
                                    }
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Semua Sekolah</option>
                                    {schools.map((school) => (
                                        <option
                                            key={school.id}
                                            value={school.id}
                                        >
                                            {school.name}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* PUSMENDIK Essential Fields */}
                            <div className="border-t pt-6 mt-6">
                                <h3 className="text-lg font-medium text-gray-900 mb-6">
                                    📋 Informasi PUSMENDIK (Sesuai Jadwal Resmi)
                                </h3>

                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel
                                            htmlFor="gelombang"
                                            value="Gelombang *"
                                        />
                                        <select
                                            id="gelombang"
                                            value={formData.gelombang}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    gelombang: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                            <option value="1">
                                                Gelombang 1
                                            </option>
                                            <option value="2">
                                                Gelombang 2
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <InputLabel
                                            htmlFor="hari_pelaksanaan"
                                            value="Hari Pelaksanaan *"
                                        />
                                        <select
                                            id="hari_pelaksanaan"
                                            value={formData.hari_pelaksanaan}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    hari_pelaksanaan:
                                                        e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                            <option value="Hari Pertama">
                                                Hari Pertama
                                            </option>
                                            <option value="Hari Kedua">
                                                Hari Kedua
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <InputLabel
                                            htmlFor="exam_venue"
                                            value="Tempat Ujian"
                                        />
                                        <TextInput
                                            id="exam_venue"
                                            type="text"
                                            value={formData.exam_venue}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    exam_venue: e.target.value,
                                                })
                                            }
                                            placeholder="SMK Negeri 1 Jakarta"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            htmlFor="exam_room"
                                            value="Ruangan Ujian"
                                        />
                                        <TextInput
                                            id="exam_room"
                                            type="text"
                                            value={formData.exam_room}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    exam_room: e.target.value,
                                                })
                                            }
                                            placeholder="Lab Komputer 1"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            htmlFor="contact_person"
                                            value="Kontak Person"
                                        />
                                        <TextInput
                                            id="contact_person"
                                            type="text"
                                            value={formData.contact_person}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    contact_person:
                                                        e.target.value,
                                                })
                                            }
                                            placeholder="Dr. Sari Indah, M.Pd"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            htmlFor="contact_phone"
                                            value="Nomor Telepon"
                                        />
                                        <TextInput
                                            id="contact_phone"
                                            type="tel"
                                            value={formData.contact_phone}
                                            onChange={(e) =>
                                                setFormData({
                                                    ...formData,
                                                    contact_phone:
                                                        e.target.value,
                                                })
                                            }
                                            placeholder="021-3844294"
                                        />
                                    </div>
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="requirements"
                                        value="Persyaratan Peserta"
                                    />
                                    <textarea
                                        id="requirements"
                                        value={formData.requirements}
                                        onChange={(e) =>
                                            setFormData({
                                                ...formData,
                                                requirements: e.target.value,
                                            })
                                        }
                                        placeholder="Siswa kelas 12 SMK, membawa KTP/Kartu Pelajar, alat tulis"
                                        rows={2}
                                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="materials_needed"
                                        value="Bahan yang Diperlukan"
                                    />
                                    <textarea
                                        id="materials_needed"
                                        value={formData.materials_needed}
                                        onChange={(e) =>
                                            setFormData({
                                                ...formData,
                                                materials_needed:
                                                    e.target.value,
                                            })
                                        }
                                        placeholder="Pensil 2B, Penghapus, Rautan, KTP/Kartu Pelajar"
                                        rows={2}
                                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="sticky bottom-0 bg-white border-t pt-4 mt-6 flex justify-end space-x-3">
                            <SecondaryButton
                                type="button"
                                onClick={() => {
                                    setShowCreateModal(false);
                                    setShowEditModal(false);
                                    setEditingSchedule(null);
                                    resetForm();
                                }}
                            >
                                Batal
                            </SecondaryButton>
                            <PrimaryButton type="submit" disabled={loading}>
                                {loading
                                    ? "Menyimpan..."
                                    : editingSchedule
                                    ? "Update"
                                    : "Simpan"}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </SuperAdminLayout>
    );
}
