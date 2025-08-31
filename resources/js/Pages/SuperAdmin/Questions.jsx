import React, { useState } from "react";
import { Head, useForm, router } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { Plus, Search, Upload } from "lucide-react";
import QuestionTable from "./components/QuestionTable";
import AddQuestionModal from "./components/AddQuestionModal";
import DeleteConfirmationModal from "./components/DeleteConfirmationModal";
import ImportQuestionsModal from "./components/ImportQuestionsModal";
import Pagination from "./components/Pagination";

export default function Questions({
    questions,
    errors,
    flash,
    subjects,
    filters,
}) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showImportModal, setShowImportModal] = useState(false);
    const [editingQuestion, setEditingQuestion] = useState(null);
    const [searchTerm, setSearchTerm] = useState(filters?.search || "");
    const [selectedSubject, setSelectedSubject] = useState(
        filters?.subject || ""
    );
    const [selectedType, setSelectedType] = useState(filters?.type || "");
    const [sortBy, setSortBy] = useState(filters?.sort_by || "subject");
    const [sortOrder, setSortOrder] = useState(filters?.sort_order || "asc");
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [questionToDelete, setQuestionToDelete] = useState(null);

    const { data, setData, post, put, processing, reset } = useForm({
        subject: "",
        type: "Pilihan Ganda",
        content: "",
        media_url: "",
        options: [
            { option_text: "", is_correct: false, label: "A" },
            { option_text: "", is_correct: false, label: "B" },
            { option_text: "", is_correct: false, label: "C" },
            { option_text: "", is_correct: false, label: "D" },
        ],
    });

    const resetForm = () => {
        reset();
        setData("options", [
            { option_text: "", is_correct: false, label: "A" },
            { option_text: "", is_correct: false, label: "B" },
            { option_text: "", is_correct: false, label: "C" },
            { option_text: "", is_correct: false, label: "D" },
        ]);
    };

    const handleAddQuestion = () => {
        if (data.type === "Pilihan Ganda") {
            const hasCorrectAnswer = data.options.some(
                (option) => option.is_correct
            );
            if (!hasCorrectAnswer) {
                alert(
                    "Pilih minimal satu jawaban yang benar untuk soal pilihan ganda!"
                );
                return;
            }
        }

        post("/super-admin/questions", {
            onSuccess: () => {
                setShowAddModal(false);
                resetForm();
            },
        });
    };

    const handleEditQuestion = () => {
        if (data.type === "Pilihan Ganda") {
            const hasCorrectAnswer = data.options.some(
                (option) => option.is_correct
            );
            if (!hasCorrectAnswer) {
                alert(
                    "Pilih minimal satu jawaban yang benar untuk soal pilihan ganda!"
                );
                return;
            }
        }

        put(`/super-admin/questions/${editingQuestion.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setEditingQuestion(null);
                resetForm();
            },
        });
    };

    const handleDeleteQuestion = (question) => {
        setQuestionToDelete(question);
        setShowDeleteModal(true);
    };

    const confirmDelete = (questionId) => {
        if (!questionId) {
            alert("ID soal tidak valid!");
            return;
        }

        router.delete(`/super-admin/questions/${questionId}`, {
            onSuccess: () => {
                console.log("Soal berhasil dihapus");
            },
            onError: (errors) => {
                console.error("Delete failed:", errors);
                alert("Gagal menghapus soal. Silakan coba lagi.");
            },
        });
    };

    const openEditModal = (question) => {
        setEditingQuestion(question);
        setData({
            subject: question.subject,
            type: "Pilihan Ganda",
            content: question.content,
            media_url: question.media_url || "",
            options:
                question.question_options?.length > 0
                    ? question.question_options.map((option, index) => ({
                          ...option,
                          label:
                              ["A", "B", "C", "D"][index] ||
                              String.fromCharCode(65 + index),
                      }))
                    : [
                          { option_text: "", is_correct: false, label: "A" },
                          { option_text: "", is_correct: false, label: "B" },
                          { option_text: "", is_correct: false, label: "C" },
                          { option_text: "", is_correct: false, label: "D" },
                      ],
        });
        setShowEditModal(true);
    };

    const handleOptionChange = (index, field, value) => {
        const newOptions = [...data.options];
        newOptions[index][field] = value;
        setData("options", newOptions);
    };

    const handleFileUpload = async (file) => {
        if (file.size > 5 * 1024 * 1024) {
            alert("Ukuran file terlalu besar! Maksimal 5MB");
            return;
        }

        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/gif",
            "image/webp",
            "audio/mpeg",
            "audio/wav",
            "audio/ogg",
        ];
        if (!allowedTypes.includes(file.type)) {
            alert("Tipe file tidak didukung! Gunakan JPG, PNG, MP3, WAV");
            return;
        }

        try {
            const formData = new FormData();
            formData.append("media", file);
            formData.append(
                "_token",
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content")
            );

            const response = await fetch("/super-admin/upload-media", {
                method: "POST",
                body: formData,
            });

            if (response.ok) {
                const result = await response.json();
                setData("media_url", result.url);
                alert("File berhasil diupload!");
            } else {
                throw new Error("Upload gagal");
            }
        } catch (error) {
            console.error("Upload error:", error);
            alert(
                "Gagal upload file. Silakan coba lagi atau gunakan URL manual."
            );
        }
    };

    // Handle search and filter changes with debounce
    const handleSearch = (value) => {
        setSearchTerm(value);
        // Debounce search to avoid too many requests
        clearTimeout(searchTimeout.current);
        searchTimeout.current = setTimeout(() => {
            updateURL({ search: value, page: 1 });
        }, 500);
    };

    // Use ref for search timeout
    const searchTimeout = React.useRef(null);

    const handleSubjectFilter = (value) => {
        setSelectedSubject(value);
        updateURL({ subject: value, page: 1 });
    };

    const handleTypeFilter = (value) => {
        setSelectedType(value);
        updateURL({ type: value, page: 1 });
    };

    const handleSortChange = (newSortBy) => {
        let newSortOrder = "asc";

        // If clicking the same column, toggle order
        if (newSortBy === sortBy) {
            newSortOrder = sortOrder === "asc" ? "desc" : "asc";
        }

        setSortBy(newSortBy);
        setSortOrder(newSortOrder);
        updateURL({
            sort_by: newSortBy,
            sort_order: newSortOrder,
            page: 1,
        });
    };

    const updateURL = (params) => {
        const url = new URL(window.location);
        Object.keys(params).forEach((key) => {
            if (params[key]) {
                url.searchParams.set(key, params[key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        window.history.pushState({}, "", url);
    };

    // Get pagination data from backend
    const currentPage = questions.current_page || 1;
    const totalPages = questions.last_page || 1;
    const totalItems = questions.total || 0;
    const itemsPerPage = questions.per_page || 20;

    const handlePageChange = (page) => {
        const url = new URL(window.location);
        url.searchParams.set("page", page);
        window.location.href = url.toString();
    };

    return (
        <SuperAdminLayout>
            <Head title="Kelola Bank Soal" />

            <div className="p-4 sm:p-6">
                {/* Header Section */}
                <div className="bg-white shadow-sm border rounded-lg mb-4 sm:mb-6">
                    <div className="px-4 sm:px-6 py-4">
                        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                            <div>
                                <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
                                    Kelola Bank Soal
                                </h1>
                                <p className="mt-1 text-sm text-gray-500">
                                    Daftar dan kelola semua soal dalam sistem
                                </p>
                                {totalItems > 0 && (
                                    <p className="mt-2 text-sm text-gray-600">
                                        Total:{" "}
                                        <span className="font-medium">
                                            {totalItems}
                                        </span>{" "}
                                        soal
                                    </p>
                                )}
                            </div>
                            <div className="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                                <button
                                    onClick={() => setShowImportModal(true)}
                                    className="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    <Upload className="h-4 w-4 mr-2" />
                                    <span className="hidden sm:inline">
                                        Import CSV
                                    </span>
                                    <span className="sm:hidden">Import</span>
                                </button>
                                <button
                                    onClick={() => setShowAddModal(true)}
                                    className="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                                >
                                    <Plus className="h-4 w-4 mr-2" />
                                    <span className="hidden sm:inline">
                                        Tambah Soal
                                    </span>
                                    <span className="sm:hidden">Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="mb-4 sm:mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg
                                    className="h-5 w-5 text-green-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clipRule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-green-800">
                                    {flash.success}
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {flash?.error && (
                    <div className="mb-4 sm:mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg
                                    className="h-5 w-5 text-red-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clipRule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-red-800">
                                    {flash.error}
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Search and Filters Section */}
                <div className="mb-4 sm:mb-6 space-y-4">
                    {/* Search and Filters Row */}
                    <div className="flex flex-col space-y-4">
                        {/* Search Bar */}
                        <div className="relative flex-1 max-w-md">
                            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                            <input
                                type="text"
                                placeholder="Cari soal berdasarkan mata pelajaran, pertanyaan, atau opsi jawaban..."
                                value={searchTerm}
                                onChange={(e) => handleSearch(e.target.value)}
                                className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            />
                        </div>

                        {/* Filters Row */}
                        <div className="flex flex-col sm:flex-row gap-2 sm:gap-3">
                            {/* Subject Filter */}
                            <select
                                value={selectedSubject}
                                onChange={(e) =>
                                    handleSubjectFilter(e.target.value)
                                }
                                className="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 min-w-[150px]"
                            >
                                <option value="">Semua Mata Pelajaran</option>
                                {subjects?.map((subject) => (
                                    <option key={subject} value={subject}>
                                        {subject}
                                    </option>
                                ))}
                            </select>

                            {/* Type Filter */}
                            <select
                                value={selectedType}
                                onChange={(e) =>
                                    handleTypeFilter(e.target.value)
                                }
                                className="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 min-w-[150px]"
                            >
                                <option value="">Semua Tipe</option>
                                <option value="Pilihan Ganda">
                                    Pilihan Ganda
                                </option>
                                <option value="Essay">Essay</option>
                            </select>

                            {/* Sorting Controls */}
                            <div className="flex flex-col sm:flex-row sm:items-center gap-2">
                                <label className="text-sm text-gray-700 whitespace-nowrap">
                                    Urutkan:
                                </label>
                                <div className="flex items-center space-x-2">
                                    <select
                                        value={sortBy}
                                        onChange={(e) =>
                                            handleSortChange(e.target.value)
                                        }
                                        className="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 min-w-[140px]"
                                    >
                                        <option value="subject">
                                            Mata Pelajaran
                                        </option>
                                        <option value="created_at">
                                            Tanggal Dibuat
                                        </option>
                                        <option value="type">Tipe Soal</option>
                                    </select>
                                    <button
                                        onClick={() => handleSortChange(sortBy)}
                                        className={`p-2 rounded-md border ${
                                            sortOrder === "asc"
                                                ? "bg-blue-50 border-blue-300 text-blue-600"
                                                : "bg-gray-50 border-gray-300 text-gray-600"
                                        } hover:bg-blue-100 transition-colors`}
                                        title={
                                            sortOrder === "asc"
                                                ? "Urutkan A-Z"
                                                : "Urutkan Z-A"
                                        }
                                    >
                                        {sortOrder === "asc" ? "↑" : "↓"}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Active Filters Display */}
                    {(searchTerm ||
                        selectedSubject ||
                        selectedType ||
                        sortBy !== "subject" ||
                        sortOrder !== "asc") && (
                        <div className="flex flex-wrap gap-2">
                            {searchTerm && (
                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Pencarian: {searchTerm}
                                    <button
                                        onClick={() => handleSearch("")}
                                        className="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500"
                                    >
                                        ×
                                    </button>
                                </span>
                            )}
                            {selectedSubject && (
                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Mata Pelajaran: {selectedSubject}
                                    <button
                                        onClick={() => handleSubjectFilter("")}
                                        className="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-green-400 hover:bg-green-200 hover:text-green-500"
                                    >
                                        ×
                                    </button>
                                </span>
                            )}
                            {selectedType && (
                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Tipe: {selectedType}
                                    <button
                                        onClick={() => handleTypeFilter("")}
                                        className="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-purple-400 hover:bg-purple-200 hover:text-purple-500"
                                    >
                                        ×
                                    </button>
                                </span>
                            )}
                            {(sortBy !== "subject" || sortOrder !== "asc") && (
                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    Urutan:{" "}
                                    {sortBy === "subject"
                                        ? "Mata Pelajaran"
                                        : sortBy === "created_at"
                                        ? "Tanggal Dibuat"
                                        : "Tipe Soal"}{" "}
                                    ({sortOrder === "asc" ? "A-Z" : "Z-A"})
                                    <button
                                        onClick={() => {
                                            setSortBy("subject");
                                            setSortOrder("asc");
                                            updateURL({
                                                sort_by: "subject",
                                                sort_order: "asc",
                                                page: 1,
                                            });
                                        }}
                                        className="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-orange-400 hover:bg-orange-200 hover:text-orange-500"
                                    >
                                        ×
                                    </button>
                                </span>
                            )}
                            <button
                                onClick={() => {
                                    handleSearch("");
                                    handleSubjectFilter("");
                                    handleTypeFilter("");
                                    setSortBy("subject");
                                    setSortOrder("asc");
                                    updateURL({
                                        search: "",
                                        subject: "",
                                        type: "",
                                        sort_by: "subject",
                                        sort_order: "asc",
                                        page: 1,
                                    });
                                }}
                                className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            >
                                Reset Semua
                            </button>
                        </div>
                    )}
                </div>

                {/* Questions Table */}
                <QuestionTable
                    questions={questions.data}
                    onEdit={openEditModal}
                    onDelete={handleDeleteQuestion}
                    searchTerm={searchTerm}
                />

                {/* Pagination Component */}
                <Pagination
                    currentPage={currentPage}
                    totalPages={totalPages}
                    onPageChange={handlePageChange}
                    totalItems={totalItems}
                    itemsPerPage={itemsPerPage}
                    showPageInfo={true}
                />

                {/* Add Question Modal */}
                <AddQuestionModal
                    isOpen={showAddModal}
                    onClose={() => {
                        setShowAddModal(false);
                        resetForm();
                    }}
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    onSubmit={handleAddQuestion}
                    handleFileUpload={handleFileUpload}
                    handleOptionChange={handleOptionChange}
                    resetForm={resetForm}
                />

                {/* Edit Question Modal */}
                {showEditModal && editingQuestion && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div className="relative top-4 sm:top-10 mx-auto p-4 sm:p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                            <h3 className="text-base sm:text-lg font-medium text-gray-900 mb-4">
                                Edit Soal
                            </h3>
                            <div className="space-y-4">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Mata Pelajaran
                                        </label>
                                        <input
                                            type="text"
                                            value={data.subject}
                                            onChange={(e) =>
                                                setData(
                                                    "subject",
                                                    e.target.value
                                                )
                                            }
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Tipe Soal
                                        </label>
                                        <input
                                            type="text"
                                            value="Pilihan Ganda"
                                            disabled
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-500 text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Soal
                                    </label>
                                    <textarea
                                        value={data.content}
                                        onChange={(e) =>
                                            setData("content", e.target.value)
                                        }
                                        rows={4}
                                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Media URL (Opsional)
                                    </label>
                                    <input
                                        type="text"
                                        value={data.media_url}
                                        onChange={(e) =>
                                            setData("media_url", e.target.value)
                                        }
                                        placeholder="https://example.com/image.jpg"
                                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    />
                                </div>

                                {/* Opsi Jawaban */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-3">
                                        Opsi Jawaban (Pilih minimal satu yang
                                        benar)
                                    </label>
                                    <div className="space-y-3">
                                        {data.options.map((option, index) => (
                                            <div
                                                key={index}
                                                className="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3"
                                            >
                                                <div className="flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        checked={
                                                            option.is_correct
                                                        }
                                                        onChange={(e) =>
                                                            handleOptionChange(
                                                                index,
                                                                "is_correct",
                                                                e.target.checked
                                                            )
                                                        }
                                                        className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                    />
                                                    <span className="ml-2 text-sm font-medium text-gray-700 w-6">
                                                        {option.label}:
                                                    </span>
                                                </div>
                                                <input
                                                    type="text"
                                                    value={option.option_text}
                                                    onChange={(e) =>
                                                        handleOptionChange(
                                                            index,
                                                            "option_text",
                                                            e.target.value
                                                        )
                                                    }
                                                    placeholder={`Masukkan opsi ${option.label}`}
                                                    className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                                />
                                            </div>
                                        ))}
                                    </div>
                                    <p className="mt-2 text-xs text-gray-500">
                                        Centang kotak untuk menandai jawaban
                                        yang benar
                                    </p>
                                </div>
                            </div>
                            <div className="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
                                <button
                                    onClick={() => {
                                        setShowEditModal(false);
                                        setEditingQuestion(null);
                                        resetForm();
                                    }}
                                    className="w-full sm:w-auto px-3 sm:px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    onClick={handleEditQuestion}
                                    disabled={processing}
                                    className="w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? "Menyimpan..." : "Update"}
                                </button>
                            </div>
                        </div>
                    </div>
                )}

                {/* Delete Confirmation Modal */}
                <DeleteConfirmationModal
                    isOpen={showDeleteModal}
                    onClose={() => {
                        setShowDeleteModal(false);
                        setQuestionToDelete(null);
                    }}
                    onConfirm={confirmDelete}
                    questionTitle={`soal "${questionToDelete?.content?.substring(
                        0,
                        50
                    )}${questionToDelete?.content?.length > 50 ? "..." : ""}"`}
                    questionId={questionToDelete?.id}
                />

                {/* Import Questions Modal */}
                <ImportQuestionsModal
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
