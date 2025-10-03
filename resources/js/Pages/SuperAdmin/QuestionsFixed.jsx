import React, { useState } from "react";
import { Head, useForm, router } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { Plus, Search, Upload } from "lucide-react";
import QuestionTable from "./components/QuestionTable";
import AddQuestionModal from "./components/AddQuestionModal";
import DeleteConfirmationModal from "./components/DeleteConfirmationModal";
import ImportQuestionsModal from "./components/ImportQuestionsModal";

export default function QuestionsFixed({ questions, errors, flash }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showImportModal, setShowImportModal] = useState(false);
    const [editingQuestion, setEditingQuestion] = useState(null);
    const [searchTerm, setSearchTerm] = useState("");
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

        post("/questions", {
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

        put(`/questions/${editingQuestion.id}`, {
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

        router.delete(`/questions/${questionId}`, {
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

            const response = await fetch("/upload-media", {
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

    return (
        <SuperAdminLayout>
            <Head title="Kelola Bank Soal" />

            <div className="p-6">
                <div className="bg-white shadow-sm border rounded-lg mb-6">
                    <div className="px-6 py-4">
                        <div className="flex justify-between items-center">
                            <div>
                                <h1 className="text-3xl font-bold text-gray-900">
                                    Kelola Bank Soal
                                </h1>
                                <p className="mt-1 text-sm text-gray-500">
                                    Daftar dan kelola semua soal dalam sistem
                                </p>
                            </div>
                            <div className="flex space-x-3">
                                <button
                                    onClick={() => setShowImportModal(true)}
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    <Upload className="h-4 w-4 mr-2" />
                                    Import CSV
                                </button>
                                <button
                                    onClick={() => setShowAddModal(true)}
                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-maroon-600 hover:bg-maroon-700"
                                >
                                    <Plus className="h-4 w-4 mr-2" />
                                    Tambah Soal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
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
                    <div className="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
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

                <div className="mb-6">
                    <div className="relative max-w-md">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Cari soal..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500"
                        />
                    </div>
                </div>

                <QuestionTable
                    questions={questions.data}
                    onEdit={openEditModal}
                    onDelete={handleDeleteQuestion}
                    searchTerm={searchTerm}
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
                        <div className="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Edit Soal
                            </h3>
                            <div className="space-y-4">
                                <div className="grid grid-cols-2 gap-4">
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
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500"
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
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-500"
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
                                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500"
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
                                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500"
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
                                                className="flex items-center space-x-3"
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
                                                        className="h-4 w-4 text-maroon-600 focus:ring-maroon-500 border-gray-300 rounded"
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
                                                    className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500"
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
                            <div className="flex justify-end space-x-3 mt-6">
                                <button
                                    onClick={() => {
                                        setShowEditModal(false);
                                        setEditingQuestion(null);
                                        resetForm();
                                    }}
                                    className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    onClick={handleEditQuestion}
                                    disabled={processing}
                                    className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-maroon-600 hover:bg-maroon-700 disabled:opacity-50"
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
