import React, { useState, useEffect } from "react";
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

export default function MajorRecommendations({
    majorRecommendations = [],
    availableSubjects = [],
    rumpunIlmu = [],
    stats = null,
    debug = null,
}) {
    // Debug data
    console.log("🔍 MajorRecommendations data:", majorRecommendations);
    console.log("🔍 MajorRecommendations.data:", majorRecommendations?.data);
    console.log("🔍 MajorRecommendations.total:", majorRecommendations?.total);
    console.log("🔍 Available subjects:", availableSubjects);
    console.log("🔍 Rumpun ilmu:", rumpunIlmu);
    console.log("🔍 Debug info:", debug);

    // Debug categories in data
    const majorsArray = majorRecommendations?.data || majorRecommendations || [];
    if (majorsArray.length > 0) {
        const categories = [
            ...new Set(
                majorsArray.map((major) => major.category)
            ),
        ];
        console.log("Categories in data:", categories);
    }
    const [showAddModal, setShowAddModal] = useState(false);
    const [editingMajor, setEditingMajor] = useState(null);
    const [showEditModal, setShowEditModal] = useState(false);
    const [selectedMajor, setSelectedMajor] = useState(null);
    const [showDetailModal, setShowDetailModal] = useState(false);
    const [searchTerm, setSearchTerm] = useState("");
    const [statusFilter, setStatusFilter] = useState("all"); // all, active, inactive
    const [rumpunIlmuFilter, setRumpunIlmuFilter] = useState("all"); // all, HUMANIORA, ILMU SOSIAL, ILMU ALAM, ILMU FORMAL, ILMU TERAPAN
    // Use stats from props instead of API
    const statsData = stats || {
        total_majors: 0,
        active_majors: 0,
        category_stats: {},
    };

    // Function to truncate text to specified number of words
    const truncateText = (text, maxWords = 15) => {
        if (!text) return "";
        const words = text.split(" ");
        if (words.length <= maxWords) return text;
        return words.slice(0, maxWords).join(" ") + "...";
    };

    const {
        data,
        setData,
        post,
        put,
        patch,
        delete: destroy,
        processing,
        errors,
        reset,
    } = useForm({
        major_name: "",
        category: "",
        description: "",
        required_subjects: [],
        preferred_subjects: [],
        kurikulum_merdeka_subjects: [],
        kurikulum_2013_ipa_subjects: [],
        kurikulum_2013_ips_subjects: [],
        kurikulum_2013_bahasa_subjects: [],
        optional_subjects: [],
        career_prospects: "",
        is_active: true,
    });

    // Use subjects from database instead of hardcoded list

    const handleAddMajor = () => {
        // Ensure all array fields are properly formatted
        const formData = {
            ...data,
            required_subjects: Array.isArray(data.required_subjects) ? data.required_subjects : [],
            preferred_subjects: Array.isArray(data.preferred_subjects) ? data.preferred_subjects : [],
            kurikulum_merdeka_subjects: Array.isArray(data.kurikulum_merdeka_subjects) ? data.kurikulum_merdeka_subjects : [],
            kurikulum_2013_ipa_subjects: Array.isArray(data.kurikulum_2013_ipa_subjects) ? data.kurikulum_2013_ipa_subjects : [],
            kurikulum_2013_ips_subjects: Array.isArray(data.kurikulum_2013_ips_subjects) ? data.kurikulum_2013_ips_subjects : [],
            kurikulum_2013_bahasa_subjects: Array.isArray(data.kurikulum_2013_bahasa_subjects) ? data.kurikulum_2013_bahasa_subjects : [],
            optional_subjects: Array.isArray(data.optional_subjects) ? data.optional_subjects : [],
        };

        console.log("Add major form data:", formData);

        post("/major-recommendations", formData, {
            onSuccess: () => {
                setShowAddModal(false);
                reset();
                // Force page refresh to show updated data
                window.location.reload();
            },
            onError: (errors) => {
                console.error("Add major errors:", errors);
                alert("Error: " + JSON.stringify(errors));
            },
        });
    };

    const handleToggleStatus = (majorId) => {
        if (confirm('Apakah Anda yakin ingin mengubah status jurusan ini?')) {
            patch(`/major-recommendations/${majorId}/toggle`, {
                onSuccess: () => {
                    // Refresh the page to show updated data
                    window.location.reload();
                },
            });
        }
    };

    const handleDeleteMajor = (majorId) => {
        if (confirm('Apakah Anda yakin ingin menghapus jurusan ini? Tindakan ini tidak dapat dibatalkan.')) {
            destroy(`/major-recommendations/${majorId}`, {
                onSuccess: () => {
                    // Refresh the page to show updated data
                    window.location.reload();
                },
            });
        }
    };

    const handleEditMajor = () => {
        console.log("=== EDIT MAJOR DEBUG ===");
        console.log("Edit data being sent:", data);
        console.log("Editing major ID:", editingMajor?.id);
        console.log("Processing state:", processing);
        console.log("Form errors:", errors);

        if (!editingMajor?.id) {
            console.error("No editing major ID found!");
            alert("Error: No major ID found for editing!");
            return;
        }

        // Validate required fields
        if (!data.major_name || !data.category) {
            console.error("Missing required fields:", { major_name: data.major_name, category: data.category });
            alert("Error: Nama Jurusan dan Rumpun Ilmu harus diisi!");
            return;
        }

        console.log(
            "Sending PUT request to:",
            `/major-recommendations/${editingMajor.id}`
        );

        // Ensure all array fields are properly formatted
        const formData = {
            ...data,
            required_subjects: Array.isArray(data.required_subjects) ? data.required_subjects : [],
            preferred_subjects: Array.isArray(data.preferred_subjects) ? data.preferred_subjects : [],
            kurikulum_merdeka_subjects: Array.isArray(data.kurikulum_merdeka_subjects) ? data.kurikulum_merdeka_subjects : [],
            kurikulum_2013_ipa_subjects: Array.isArray(data.kurikulum_2013_ipa_subjects) ? data.kurikulum_2013_ipa_subjects : [],
            kurikulum_2013_ips_subjects: Array.isArray(data.kurikulum_2013_ips_subjects) ? data.kurikulum_2013_ips_subjects : [],
            kurikulum_2013_bahasa_subjects: Array.isArray(data.kurikulum_2013_bahasa_subjects) ? data.kurikulum_2013_bahasa_subjects : [],
            optional_subjects: Array.isArray(data.optional_subjects) ? data.optional_subjects : [],
        };

        console.log("Formatted form data:", formData);
        console.log("CSRF Token:", document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

        put(`/major-recommendations/${editingMajor.id}`, formData, {
            onSuccess: (page) => {
                console.log("Edit successful:", page);
                alert("Jurusan berhasil diupdate!");
                setShowEditModal(false);
                setEditingMajor(null);
                reset();
                // Force page refresh to show updated data
                window.location.reload();
            },
            onError: (errors) => {
                console.error("Edit errors:", errors);
                alert("Error: " + JSON.stringify(errors));
            },
            onFinish: () => {
                console.log("Edit request finished");
            },
        });
    };


    // Sample data for testing when database is not available
    const sampleData = [
        { id: 1, major_name: 'Seni Rupa', category: 'Humaniora', is_active: true },
        { id: 2, major_name: 'Sosiologi', category: 'Ilmu Sosial', is_active: true },
        { id: 3, major_name: 'Kimia', category: 'Ilmu Alam', is_active: true },
        { id: 4, major_name: 'Matematika', category: 'Ilmu Formal', is_active: true },
        { id: 5, major_name: 'Fisika', category: 'Ilmu Formal', is_active: true },
        { id: 6, major_name: 'Teknik Informatika', category: 'Ilmu Formal', is_active: true },
        { id: 7, major_name: 'Ekonomi', category: 'Ilmu Sosial', is_active: true },
        { id: 8, major_name: 'Psikologi', category: 'Ilmu Sosial', is_active: true },
        { id: 9, major_name: 'Teknik Mesin', category: 'Ilmu Terapan', is_active: true },
        { id: 10, major_name: 'Teknik Sipil', category: 'Ilmu Terapan', is_active: true },
        { id: 11, major_name: 'Kedokteran', category: 'Ilmu Terapan', is_active: true },
        { id: 12, major_name: 'Sejarah', category: 'Humaniora', is_active: true },
        { id: 13, major_name: 'Sastra Indonesia', category: 'Humaniora', is_active: true },
        { id: 14, major_name: 'Biologi', category: 'Ilmu Alam', is_active: true },
        { id: 15, major_name: 'Geografi', category: 'Ilmu Alam', is_active: true }
    ];

    // Use data from API or fallback to sample data
    const majorsData = (majorsArray && majorsArray.length > 0) 
        ? majorsArray 
        : sampleData;
    
    console.log("🔍 Using majorsData:", majorsData.length, "items");
    console.log("🔍 First few majors:", majorsData.slice(0, 3).map(m => ({ name: m.major_name, category: m.category })));

    // Filter majors based on search term, status, and category
    const filteredMajors = majorsData.filter(
        (major) => {
            // Debug: Log all majors and their categories
            console.log("📊 All majors data:", majorsData.map(m => ({ name: m.major_name, category: m.category })));
            console.log("🔍 Current filter:", rumpunIlmuFilter);
            const matchesSearch =
                searchTerm === "" ||
                major.major_name
                    .toLowerCase()
                    .includes(searchTerm.toLowerCase()) ||
                major.description
                    ?.toLowerCase()
                    .includes(searchTerm.toLowerCase()) ||
                (Array.isArray(major.required_subjects)
                    ? major.required_subjects.some((subject) =>
                          subject
                              .toLowerCase()
                              .includes(searchTerm.toLowerCase())
                      )
                    : false) ||
                (Array.isArray(major.preferred_subjects)
                    ? major.preferred_subjects.some((subject) =>
                          subject
                              .toLowerCase()
                              .includes(searchTerm.toLowerCase())
                      )
                    : false) ||
                (Array.isArray(major.kurikulum_merdeka_subjects)
                    ? major.kurikulum_merdeka_subjects.some((subject) =>
                          subject
                              .toLowerCase()
                              .includes(searchTerm.toLowerCase())
                      )
                    : false) ||
                (Array.isArray(major.kurikulum_2013_ipa_subjects)
                    ? major.kurikulum_2013_ipa_subjects.some((subject) =>
                          subject
                              .toLowerCase()
                              .includes(searchTerm.toLowerCase())
                      )
                    : false) ||
                (Array.isArray(major.kurikulum_2013_ips_subjects)
                    ? major.kurikulum_2013_ips_subjects.some((subject) =>
                          subject
                              .toLowerCase()
                              .includes(searchTerm.toLowerCase())
                      )
                    : false) ||
                (Array.isArray(major.kurikulum_2013_bahasa_subjects)
                    ? major.kurikulum_2013_bahasa_subjects.some((subject) =>
                          subject
                              .toLowerCase()
                              .includes(searchTerm.toLowerCase())
                      )
                    : false);

            const matchesStatus =
                statusFilter === "all" ||
                (statusFilter === "active" && major.is_active) ||
                (statusFilter === "inactive" && !major.is_active);

            // Normalize category for comparison (trim and case-insensitive)
            const normalizedCategory = (major.category || "").trim().toLowerCase();
            const normalizedFilter = rumpunIlmuFilter.trim().toLowerCase();
            
            // Map old categories to new categories for backward compatibility
            const categoryMapping = {
                'soshum': 'ilmu sosial',
                'saintek': 'ilmu terapan',
                'ilmu kesehatan': 'ilmu terapan',
                'ilmu lingkungan': 'ilmu terapan',
                'ilmu teknologi': 'ilmu terapan',
                'humaniora': 'humaniora',
                'ilmu sosial': 'ilmu sosial',
                'ilmu alam': 'ilmu alam',
                'ilmu formal': 'ilmu formal',
                'ilmu terapan': 'ilmu terapan',
                // Add title case mappings
                'Humaniora': 'humaniora',
                'Ilmu Sosial': 'ilmu sosial',
                'Ilmu Alam': 'ilmu alam',
                'Ilmu Formal': 'ilmu formal',
                'Ilmu Terapan': 'ilmu terapan'
            };
            
            const mappedCategory = categoryMapping[normalizedCategory] || normalizedCategory;
            
            const matchesRumpunIlmu =
                rumpunIlmuFilter === "all" ||
                mappedCategory === normalizedFilter;

            // Debug logging
            if (rumpunIlmuFilter !== "all") {
                console.log(
                    `🔍 Filter Debug - Major: ${major.major_name}, Category: "${major.category}" (normalized: "${normalizedCategory}", mapped: "${mappedCategory}"), Filter: "${rumpunIlmuFilter}" (normalized: "${normalizedFilter}"), Matches: ${matchesRumpunIlmu}`
                );
            }

            return matchesSearch && matchesStatus && matchesRumpunIlmu;
        }
    );

    const openEditModal = (major) => {
        console.log("=== OPEN EDIT MODAL DEBUG ===");
        console.log("Major data received:", major);
        console.log("Major ID:", major.id);
        console.log("Major preferred_subjects:", major.preferred_subjects);

        setEditingMajor(major);
        // Map old categories to new categories for edit modal
        const categoryMapping = {
            'Soshum': 'Ilmu Sosial',
            'Saintek': 'Ilmu Terapan',
            'Ilmu Kesehatan': 'Ilmu Terapan',
            'Ilmu Lingkungan': 'Ilmu Terapan',
            'Ilmu Teknologi': 'Ilmu Terapan',
            'Humaniora': 'Humaniora',
            'Ilmu Sosial': 'Ilmu Sosial',
            'Ilmu Alam': 'Ilmu Alam',
            'Ilmu Formal': 'Ilmu Formal',
            'Ilmu Terapan': 'Ilmu Terapan',
            // Add lowercase mappings
            'humaniora': 'Humaniora',
            'ilmu sosial': 'Ilmu Sosial',
            'ilmu alam': 'Ilmu Alam',
            'ilmu formal': 'Ilmu Formal',
            'ilmu terapan': 'Ilmu Terapan'
        };
        const mappedCategory = categoryMapping[major.category] || major.category || "Ilmu Alam";
        
        setData({
            major_name: major.major_name,
            category: mappedCategory,
            description: major.description,
            required_subjects: major.required_subjects || [],
            preferred_subjects: major.preferred_subjects || [], // Use preferred_subjects from database
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
        console.log(`=== TOGGLE SUBJECT DEBUG ===`);
        console.log(`Subject: ${typeof subject === 'object' ? subject.name : subject}`);
        console.log(`Type: ${type}`);
        console.log(`Current data[${type}]:`, data[type]);

        const currentSubjects = data[type] || [];
        console.log(`Current subjects array:`, currentSubjects);
        
        // Handle both object and string subjects
        const subjectValue = typeof subject === 'object' ? subject.name : subject;
        const isIncluded = currentSubjects.some(s => 
            typeof s === 'object' ? s.name === subjectValue : s === subjectValue
        );
        
        console.log(`Subject included:`, isIncluded);

        if (isIncluded) {
            const newSubjects = currentSubjects.filter((s) => {
                if (typeof s === 'object' && typeof subject === 'object') {
                    return s.name !== subject.name;
                } else if (typeof s === 'string' && typeof subject === 'string') {
                    return s !== subject;
                } else {
                    return s !== subject;
                }
            });
            console.log(`Removing subject. New array:`, newSubjects);
            setData({
                ...data,
                [type]: newSubjects,
            });
        } else {
            const newSubjects = [...currentSubjects, subject];
            console.log(`Adding subject. New array:`, newSubjects);
            setData({
                ...data,
                [type]: newSubjects,
            });
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
                                            "/major-recommendations/export")
                                    }
                                    className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
                                >
                                    <Download className="h-4 w-4" />
                                    Export CSV
                                </button>
                                <button
                                    onClick={() => {
                                        reset(); // Reset form data
                                        setShowAddModal(true);
                                    }}
                                    className="bg-maroon-600 hover:bg-maroon-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
                                >
                                    <Plus className="h-4 w-4" />
                                    Tambah Jurusan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <div className="bg-white rounded-lg shadow-sm border p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-gray-500">
                                <BookOpen className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-600">
                                    Total Jurusan
                                </p>
                                <p className="text-xl font-bold text-gray-900">
                                    {statsData.total_majors}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-blue-500">
                                <span className="text-white text-lg">🔬</span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-blue-700">
                                    ILMU ALAM
                                </p>
                                <p className="text-xl font-bold text-blue-900">
                                    {statsData.category_stats["Ilmu Alam"] || 0}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-green-500">
                                <span className="text-white text-lg">👥</span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-green-700">
                                    ILMU SOSIAL
                                </p>
                                <p className="text-xl font-bold text-green-900">
                                    {statsData.category_stats["Ilmu Sosial"] ||
                                        0}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-purple-50 rounded-lg shadow-sm border border-purple-200 p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-purple-500">
                                <span className="text-white text-lg">🎨</span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-purple-700">
                                    HUMANIORA
                                </p>
                                <p className="text-xl font-bold text-purple-900">
                                    {statsData.category_stats["Humaniora"] || 0}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-orange-50 rounded-lg shadow-sm border border-orange-200 p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-orange-500">
                                <span className="text-white text-lg">📐</span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-orange-700">
                                    ILMU FORMAL
                                </p>
                                <p className="text-xl font-bold text-orange-900">
                                    {statsData.category_stats["Ilmu Formal"] ||
                                        0}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-red-50 rounded-lg shadow-sm border border-red-200 p-4">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-red-500">
                                <span className="text-white text-lg">⚙️</span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-red-700">
                                    ILMU TERAPAN
                                </p>
                                <p className="text-xl font-bold text-red-900">
                                    {statsData.category_stats["Ilmu Terapan"] ||
                                        0}
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
                                    {statsData.active_majors}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Filtered Results Summary */}
                {(searchTerm !== "" ||
                    statusFilter !== "all" ||
                    rumpunIlmuFilter !== "all") && (
                    <div className="bg-maroon-50 border border-maroon-200 rounded-lg p-4 mb-6">
                        <div className="flex items-center">
                            <div className="p-2 rounded-lg bg-maroon-500">
                                <Search className="h-5 w-5 text-white" />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-maroon-800">
                                    Hasil Pencarian & Filter
                                </p>
                                <p className="text-lg font-bold text-maroon-900">
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
                                        className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 sm:text-sm"
                                    />
                                </div>

                                {/* Rumpun Ilmu Filter */}
                                <select
                                    value={rumpunIlmuFilter}
                                    onChange={(e) =>
                                        setRumpunIlmuFilter(e.target.value)
                                    }
                                    className="block w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 sm:text-sm"
                                >
                                    <option value="all">
                                        Semua Rumpun Ilmu
                                    </option>
                                    <option value="humaniora">
                                        🎨 HUMANIORA
                                    </option>
                                    <option value="ilmu sosial">
                                        📚 ILMU SOSIAL
                                    </option>
                                    <option value="ilmu alam">
                                        🔬 ILMU ALAM
                                    </option>
                                    <option value="ilmu formal">
                                        📐 ILMU FORMAL
                                    </option>
                                    <option value="ilmu terapan">
                                        ⚙️ ILMU TERAPAN
                                    </option>
                                </select>

                                {/* Status Filter */}
                                <select
                                    value={statusFilter}
                                    onChange={(e) =>
                                        setStatusFilter(e.target.value)
                                    }
                                    className="block w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 sm:text-sm"
                                >
                                    <option value="all">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Non-Aktif</option>
                                </select>
                            </div>
                        </div>

                        {/* Search Results Info */}
                        {searchTerm !== "" ||
                        statusFilter !== "all" ||
                        rumpunIlmuFilter !== "all" ? (
                            <div className="mt-3 text-sm text-gray-600">
                                Menampilkan {filteredMajors.length} dari{" "}
                                {majorRecommendations?.total || 0} jurusan
                                {searchTerm !== "" && (
                                    <span className="ml-2">
                                        untuk pencarian:{" "}
                                        <span className="font-medium">
                                            "{searchTerm}"
                                        </span>
                                    </span>
                                )}
                                {rumpunIlmuFilter !== "all" && (
                                    <span className="ml-2">
                                        rumpun ilmu:{" "}
                                        <span className="font-medium">
                                            {rumpunIlmuFilter === "Humaniora"
                                                ? "🎨 HUMANIORA"
                                                : rumpunIlmuFilter ===
                                                  "Ilmu Sosial"
                                                ? "📚 ILMU SOSIAL"
                                                : rumpunIlmuFilter ===
                                                  "Ilmu Alam"
                                                ? "🔬 ILMU ALAM"
                                                : rumpunIlmuFilter ===
                                                  "Ilmu Formal"
                                                ? "📐 ILMU FORMAL"
                                                : rumpunIlmuFilter ===
                                                  "Ilmu Terapan"
                                                ? "⚙️ ILMU TERAPAN"
                                                : rumpunIlmuFilter}
                                        </span>
                                    </span>
                                )}
                                {statusFilter !== "all" && (
                                    <span className="ml-2">
                                        status:{" "}
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
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                        Jurusan
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                        Kategori
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">
                                        Mata Pelajaran Wajib
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">
                                        Mata Pelajaran Pilihan
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
                                        <td className="px-6 py-4">
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    {major.major_name}
                                                </div>
                                                <div
                                                    className="text-sm text-gray-500 cursor-help max-w-xs break-words"
                                                    title={major.description}
                                                >
                                                    {truncateText(
                                                        major.description,
                                                        15
                                                    )}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span
                                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    major.category ===
                                                    "Ilmu Alam"
                                                        ? "bg-blue-100 text-blue-800 border border-blue-200"
                                                        : major.category ===
                                                          "Ilmu Sosial"
                                                        ? "bg-green-100 text-green-800 border border-green-200"
                                                        : major.category ===
                                                          "Humaniora"
                                                        ? "bg-purple-100 text-purple-800 border border-purple-200"
                                                        : major.category ===
                                                          "Ilmu Formal"
                                                        ? "bg-orange-100 text-orange-800 border border-orange-200"
                                                        : major.category ===
                                                          "Ilmu Terapan"
                                                        ? "bg-red-100 text-red-800 border border-red-200"
                                                        : "bg-gray-100 text-gray-800 border border-gray-200"
                                                }`}
                                            >
                                                <span className="mr-1">
                                                    {major.category ===
                                                    "Ilmu Alam"
                                                        ? "🔬"
                                                        : major.category ===
                                                          "Ilmu Sosial"
                                                        ? "👥"
                                                        : major.category ===
                                                          "Humaniora"
                                                        ? "🎨"
                                                        : major.category ===
                                                          "Ilmu Formal"
                                                        ? "📐"
                                                        : major.category ===
                                                          "Ilmu Terapan"
                                                        ? "⚙️"
                                                        : "📋"}
                                                </span>
                                                {major.category || "Ilmu Alam"}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex flex-wrap gap-1">
                                                {Array.isArray(
                                                    major.mandatory_subjects
                                                )
                                                    ? major.mandatory_subjects.map(
                                                          (subject, index) => (
                                                              <span
                                                                  key={index}
                                                                  className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                              >
                                                                  {typeof subject === 'object' ? subject.name : subject}
                                                              </span>
                                                          )
                                                      )
                                                    : null}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex flex-wrap gap-1">
                                                {Array.isArray(
                                                    major.preferred_subjects
                                                )
                                                    ? major.preferred_subjects.map(
                                                          (subject, index) => (
                                                              <span
                                                                  key={index}
                                                                  className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                                                                      (typeof subject === 'object' ? subject.name : subject) ===
                                                                      "Produk/Projek Kreatif dan Kewirausahaan"
                                                                          ? "bg-yellow-100 text-yellow-800"
                                                                          : "bg-green-100 text-green-800"
                                                                  }`}
                                                              >
                                                                  {typeof subject === 'object' ? subject.name : subject}
                                                              </span>
                                                          )
                                                      )
                                                    : null}
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
                                                    className="text-maroon-600 hover:text-maroon-900"
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
                                        statusFilter !== "all" ||
                                        rumpunIlmuFilter !== "all") && (
                                        <button
                                            onClick={() => {
                                                setSearchTerm("");
                                                setStatusFilter("all");
                                                setRumpunIlmuFilter("all");
                                            }}
                                            className="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-maroon-700 bg-maroon-100 hover:bg-maroon-200"
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
                                            Rumpun Ilmu
                                        </label>
                                        <select
                                            value={data.category}
                                            onChange={(e) =>
                                                setData({
                                                    ...data,
                                                    category: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                        >
                                            <option value="">
                                                Pilih Rumpun Ilmu
                                            </option>
                                            <option value="Ilmu Alam">
                                                🔬 ILMU ALAM
                                            </option>
                                            <option value="Ilmu Sosial">
                                                📚 ILMU SOSIAL
                                            </option>
                                            <option value="Humaniora">
                                                🎨 HUMANIORA
                                            </option>
                                            <option value="Ilmu Formal">
                                                📐 ILMU FORMAL
                                            </option>
                                            <option value="Ilmu Terapan">
                                                ⚙️ ILMU TERAPAN
                                            </option>
                                        </select>
                                        {errors.category && (
                                            <p className="text-red-500 text-xs mt-1">
                                                {errors.category}
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
                                                setData({
                                                    ...data,
                                                    description: e.target.value,
                                                })
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
                                        <div className="mt-2 p-3 bg-maroon-50 border border-maroon-200 rounded-md">
                                            <p className="text-sm text-maroon-800 mb-2">
                                                <strong>
                                                    Mata pelajaran wajib untuk
                                                    semua jurusan:
                                                </strong>
                                            </p>
                                            <div className="flex flex-wrap gap-2">
                                                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-maroon-100 text-maroon-800">
                                                    Matematika
                                                </span>
                                                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-maroon-100 text-maroon-800">
                                                    Bahasa Inggris
                                                </span>
                                                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-maroon-100 text-maroon-800">
                                                    Bahasa Indonesia
                                                </span>
                                            </div>
                                            <p className="text-xs text-maroon-600 mt-2">
                                                Mata pelajaran wajib ini akan
                                                otomatis ditambahkan untuk semua
                                                jurusan.
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Mata Pelajaran Pilihan
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                setData({
                                                    ...data,
                                                    career_prospects:
                                                        e.target.value,
                                                })
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
                                                setData({
                                                    ...data,
                                                    is_active: e.target.checked,
                                                })
                                            }
                                            className="rounded border-gray-300 text-maroon-600"
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
                                        className="px-4 py-2 text-sm font-medium text-white bg-maroon-600 border border-transparent rounded-md hover:bg-maroon-700 disabled:opacity-50"
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
                                                setData({
                                                    ...data,
                                                    major_name: e.target.value,
                                                })
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
                                            Rumpun Ilmu
                                        </label>
                                        <select
                                            value={data.category}
                                            onChange={(e) =>
                                                setData({
                                                    ...data,
                                                    category: e.target.value,
                                                })
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                        >
                                            <option value="Ilmu Alam">
                                                🔬 ILMU ALAM
                                            </option>
                                            <option value="Ilmu Sosial">
                                                📚 ILMU SOSIAL
                                            </option>
                                            <option value="Humaniora">
                                                🎨 HUMANIORA
                                            </option>
                                            <option value="Ilmu Formal">
                                                📐 ILMU FORMAL
                                            </option>
                                            <option value="Ilmu Terapan">
                                                ⚙️ ILMU TERAPAN
                                            </option>
                                        </select>
                                        {errors.category && (
                                            <p className="text-red-500 text-xs mt-1">
                                                {errors.category}
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
                                                setData({
                                                    ...data,
                                                    description: e.target.value,
                                                })
                                            }
                                            rows="3"
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Mata Pelajaran Wajib
                                        </label>
                                        <div className="mt-2 p-3 bg-maroon-50 border border-maroon-200 rounded-md">
                                            <p className="text-sm text-maroon-800 mb-2">
                                                <strong>
                                                    Mata pelajaran wajib untuk
                                                    semua jurusan:
                                                </strong>
                                            </p>
                                            <div className="flex flex-wrap gap-2">
                                                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-maroon-100 text-maroon-800">
                                                    Matematika
                                                </span>
                                                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-maroon-100 text-maroon-800">
                                                    Bahasa Inggris
                                                </span>
                                                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-maroon-100 text-maroon-800">
                                                    Bahasa Indonesia
                                                </span>
                                            </div>
                                            <p className="text-xs text-maroon-600 mt-2">
                                                Mata pelajaran wajib ini akan
                                                otomatis ditambahkan untuk semua
                                                jurusan.
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Mata Pelajaran Pilihan
                                        </label>
                                        <div className="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            {availableSubjects.map(
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                (subject, index) => (
                                                    <label
                                                        key={`${typeof subject === 'object' ? subject.id : subject}-${index}`}
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
                                                            {typeof subject === 'object' ? subject.name : subject}
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
                                                setData({
                                                    ...data,
                                                    career_prospects:
                                                        e.target.value,
                                                })
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
                                                setData({
                                                    ...data,
                                                    is_active: e.target.checked,
                                                })
                                            }
                                            className="rounded border-gray-300 text-maroon-600"
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
                                        className="px-4 py-2 text-sm font-medium text-white bg-maroon-600 border border-transparent rounded-md hover:bg-maroon-700 disabled:opacity-50"
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
                                                        {selectedMajor.mandatory_subjects?.map(
                                                            (
                                                                subject,
                                                                index
                                                            ) => (
                                                                <span
                                                                    key={index}
                                                                    className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                                >
                                                                    {typeof subject === 'object' ? subject.name : subject}
                                                                </span>
                                                            )
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Mata Pelajaran Referensi */}
                                                <div>
                                                    <h4 className="text-sm font-medium text-gray-700 mb-2">
                                                        Mata Pelajaran Pilihan
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
                                                                    {typeof subject === 'object' ? subject.name : subject}
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
                                                                    {typeof subject === 'object' ? subject.name : subject}
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
                                                                    {typeof subject === 'object' ? subject.name : subject}
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
                                                                    {typeof subject === 'object' ? subject.name : subject}
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
                                                                    {typeof subject === 'object' ? subject.name : subject}
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
                                        className="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-maroon-600 text-base font-medium text-white hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500 sm:ml-3 sm:w-auto sm:text-sm"
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
