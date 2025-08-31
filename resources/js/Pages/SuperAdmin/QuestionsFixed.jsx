import React, { useState } from "react";
import { Head, useForm, Link } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import {
    BookOpen,
    Plus,
    Edit,
    Trash2,
    Search,
    CheckCircle,
} from "lucide-react";

export default function Questions({ questions }) {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [editingQuestion, setEditingQuestion] = useState(null);
    const [searchTerm, setSearchTerm] = useState("");

    const { data, setData, post, put, processing, errors, reset } = useForm({
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

    const filteredQuestions = questions.data.filter(
        (question) =>
            question.subject.toLowerCase().includes(searchTerm.toLowerCase()) ||
            question.content.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const handleAddQuestion = () => {
        // Validate that at least one option is correct for multiple choice
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
        // Validate that at least one option is correct for multiple choice
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

    const handleDeleteQuestion = async (questionId) => {
        if (confirm("Apakah Anda yakin ingin menghapus soal ini?")) {
            try {
                const response = await fetch(
                    `/super-admin/questions/${questionId}`,
                    {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content"),
                            "Content-Type": "application/json",
                        },
                    }
                );

                if (response.ok) {
                    // Reload halaman untuk update data
                    window.location.reload();
                } else {
                    alert("Gagal menghapus soal");
                }
            } catch (error) {
                console.error("Delete error:", error);
                alert("Gagal menghapus soal");
            }
        }
    };

    const openEditModal = (question) => {
        setEditingQuestion(question);
        setData({
            subject: question.subject,
            type: question.type,
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
        if (field === "is_correct") {
            // For multiple choice, allow multiple correct answers
            newOptions[index][field] = value;
        } else {
            newOptions[index][field] = value;
        }
        setData("options", newOptions);
    };

    const resetForm = () => {
        reset();
        setData("options", [
            { option_text: "", is_correct: false, label: "A" },
            { option_text: "", is_correct: false, label: "B" },
            { option_text: "", is_correct: false, label: "C" },
            { option_text: "", is_correct: false, label: "D" },
        ]);
    };

    const handleFileUpload = async (file) => {
        // Validasi ukuran file (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert("Ukuran file terlalu besar! Maksimal 5MB");
            return;
        }

        // Validasi tipe file
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
            // Buat FormData untuk upload
            const formData = new FormData();
            formData.append("media", file);
            formData.append(
                "_token",
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content")
            );

            // Upload file ke server
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
                            <button
                                onClick={() => setShowAddModal(true)}
                                className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                            >
                                <Plus className="h-4 w-4 mr-2" />
                                Tambah Soal
                            </button>
                        </div>
                    </div>
                </div>

                <div className="mb-6">
                    <div className="relative max-w-md">
                        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Cari soal..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                </div>

                <div className="bg-white shadow-sm rounded-lg border">
                    <div className="px-6 py-4 border-b">
                        <h3 className="text-lg font-semibold text-gray-900">
                            Daftar Soal
                        </h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Mata Pelajaran
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Tipe
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Soal
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Media
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Opsi Jawaban
                                    </th>
                                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {filteredQuestions.map((question) => (
                                    <tr
                                        key={question.id}
                                        className="hover:bg-gray-50"
                                    >
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {question.subject}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span
                                                className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                    question.type ===
                                                    "Pilihan Ganda"
                                                        ? "bg-blue-100 text-blue-800"
                                                        : "bg-green-100 text-green-800"
                                                }`}
                                            >
                                                {question.type}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-900">
                                            <div className="max-w-xs truncate">
                                                {question.content}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-900">
                                            {question.media_url ? (
                                                <div className="max-w-xs">
                                                    {question.media_url.match(
                                                        /\.(jpg|jpeg|png|gif|webp)$/i
                                                    ) ? (
                                                        <img
                                                            src={
                                                                question.media_url
                                                            }
                                                            alt="Media"
                                                            className="h-16 w-16 object-cover rounded"
                                                        />
                                                    ) : question.media_url.match(
                                                          /\.(mp3|wav|ogg|m4a)$/i
                                                      ) ? (
                                                        <div className="text-blue-600">
                                                            <span className="text-xs">
                                                                🎵 Audio
                                                            </span>
                                                        </div>
                                                    ) : (
                                                        <span className="text-xs text-gray-500">
                                                            📎 File
                                                        </span>
                                                    )}
                                                </div>
                                            ) : (
                                                <span className="text-gray-400">
                                                    -
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-900">
                                            {question.type ===
                                                "Pilihan Ganda" &&
                                            question.question_options ? (
                                                <div className="space-y-1">
                                                    {question.question_options.map(
                                                        (option, index) => (
                                                            <div
                                                                key={index}
                                                                className="flex items-center space-x-2"
                                                            >
                                                                <span
                                                                    className={`inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold ${
                                                                        option.is_correct
                                                                            ? "bg-green-100 text-green-800"
                                                                            : "bg-gray-100 text-gray-600"
                                                                    }`}
                                                                >
                                                                    {String.fromCharCode(
                                                                        65 +
                                                                            index
                                                                    )}
                                                                </span>
                                                                <span
                                                                    className={`text-xs ${
                                                                        option.is_correct
                                                                            ? "text-green-700 font-medium"
                                                                            : "text-gray-600"
                                                                    }`}
                                                                >
                                                                    {
                                                                        option.option_text
                                                                    }
                                                                </span>
                                                                {option.is_correct && (
                                                                    <CheckCircle className="h-3 w-3 text-green-600" />
                                                                )}
                                                            </div>
                                                        )
                                                    )}
                                                </div>
                                            ) : (
                                                <span className="text-gray-400">
                                                    -
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button
                                                onClick={() =>
                                                    openEditModal(question)
                                                }
                                                className="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                <Edit className="h-4 w-4" />
                                            </button>
                                            <button
                                                onClick={() =>
                                                    handleDeleteQuestion(
                                                        question.id
                                                    )
                                                }
                                                className="text-red-600 hover:text-red-900"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Add Question Modal */}
                {showAddModal && (
                    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div className="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Tambah Soal Baru
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
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        {errors.subject && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.subject}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Tipe Soal
                                        </label>
                                        <select
                                            value={data.type}
                                            onChange={(e) => {
                                                setData("type", e.target.value);
                                                if (
                                                    e.target.value === "Essay"
                                                ) {
                                                    setData("options", []);
                                                } else {
                                                    resetForm();
                                                }
                                            }}
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="Pilihan Ganda">
                                                Pilihan Ganda
                                            </option>
                                            <option value="Essay">Essay</option>
                                        </select>
                                        {errors.type && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.type}
                                            </p>
                                        )}
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
                                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    {errors.content && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.content}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Media (opsional)
                                    </label>

                                    {/* Upload File */}
                                    <div className="mb-3">
                                        <label className="block text-sm text-gray-600 mb-1">
                                            Upload File
                                        </label>
                                        <input
                                            type="file"
                                            accept="image/*,audio/*"
                                            onChange={(e) => {
                                                const file = e.target.files[0];
                                                if (file) {
                                                    handleFileUpload(file);
                                                }
                                            }}
                                            className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        />
                                        <p className="text-xs text-gray-500 mt-1">
                                            Format: JPG, PNG, MP3, WAV (Max:
                                            5MB)
                                        </p>
                                    </div>

                                    {/* Manual URL */}
                                    <div>
                                        <label className="block text-sm text-gray-600 mb-1">
                                            Atau Masukkan URL Manual
                                        </label>
                                        <input
                                            type="text"
                                            value={data.media_url}
                                            onChange={(e) =>
                                                setData(
                                                    "media_url",
                                                    e.target.value
                                                )
                                            }
                                            placeholder="https://example.com/image.jpg atau audio.mp3"
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        {errors.media_url && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.media_url}
                                            </p>
                                        )}
                                    </div>

                                    {/* Preview Media */}
                                    {data.media_url && (
                                        <div className="mt-3 p-3 bg-gray-50 rounded-lg">
                                            <p className="text-sm font-medium text-gray-700 mb-2">
                                                Preview Media:
                                            </p>
                                            {data.media_url.match(
                                                /\.(jpg|jpeg|png|gif|webp)$/i
                                            ) ? (
                                                <div>
                                                    <img
                                                        src={data.media_url}
                                                        alt="Preview"
                                                        className="max-w-full h-32 object-cover rounded"
                                                        onError={(e) => {
                                                            e.target.style.display =
                                                                "none";
                                                            e.target.nextSibling.style.display =
                                                                "block";
                                                        }}
                                                    />
                                                    <p
                                                        className="text-xs text-gray-500 mt-1"
                                                        style={{
                                                            display: "none",
                                                        }}
                                                    >
                                                        Gambar tidak dapat
                                                        ditampilkan
                                                    </p>
                                                </div>
                                            ) : data.media_url.match(
                                                  /\.(mp3|wav|ogg|m4a)$/i
                                              ) ? (
                                                <audio
                                                    controls
                                                    className="w-full"
                                                >
                                                    <source
                                                        src={data.media_url}
                                                        type="audio/mpeg"
                                                    />
                                                    Browser tidak mendukung
                                                    audio
                                                </audio>
                                            ) : (
                                                <p className="text-sm text-gray-600">
                                                    URL: {data.media_url}
                                                </p>
                                            )}
                                        </div>
                                    )}
                                </div>

                                {/* Options for Multiple Choice */}
                                {data.type === "Pilihan Ganda" && (
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-3">
                                            Opsi Jawaban (Pilih minimal satu
                                            yang benar)
                                        </label>
                                        <div className="space-y-3">
                                            {data.options.map(
                                                (option, index) => (
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
                                                                        e.target
                                                                            .checked
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
                                                            value={
                                                                option.option_text
                                                            }
                                                            onChange={(e) =>
                                                                handleOptionChange(
                                                                    index,
                                                                    "option_text",
                                                                    e.target
                                                                        .value
                                                                )
                                                            }
                                                            placeholder={`Masukkan opsi ${option.label}`}
                                                            className="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                        />
                                                    </div>
                                                )
                                            )}
                                        </div>
                                        <p className="mt-2 text-xs text-gray-500">
                                            Centang kotak untuk menandai jawaban
                                            yang benar
                                        </p>
                                    </div>
                                )}
                            </div>
                            <div className="flex justify-end space-x-3 mt-6">
                                <button
                                    onClick={() => {
                                        setShowAddModal(false);
                                        resetForm();
                                    }}
                                    className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    onClick={handleAddQuestion}
                                    disabled={processing}
                                    className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? "Menyimpan..." : "Simpan"}
                                </button>
                            </div>
                        </div>
                    </div>
                )}

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
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        {errors.subject && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.subject}
                                            </p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Tipe Soal
                                        </label>
                                        <select
                                            value={data.type}
                                            onChange={(e) => {
                                                setData("type", e.target.value);
                                                if (
                                                    e.target.value === "Essay"
                                                ) {
                                                    setData("options", []);
                                                } else {
                                                    setData("options", [
                                                        {
                                                            option_text: "",
                                                            is_correct: false,
                                                            label: "A",
                                                        },
                                                        {
                                                            option_text: "",
                                                            is_correct: false,
                                                            label: "B",
                                                        },
                                                        {
                                                            option_text: "",
                                                            is_correct: false,
                                                            label: "C",
                                                        },
                                                        {
                                                            option_text: "",
                                                            is_correct: false,
                                                            label: "D",
                                                        },
                                                    ]);
                                                }
                                            }}
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="Pilihan Ganda">
                                                Pilihan Ganda
                                            </option>
                                            <option value="Essay">Essay</option>
                                        </select>
                                        {errors.type && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.type}
                                            </p>
                                        )}
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
                                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    {errors.content && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.content}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Media (opsional)
                                    </label>

                                    {/* Upload File */}
                                    <div className="mb-3">
                                        <label className="block text-sm text-gray-600 mb-1">
                                            Upload File
                                        </label>
                                        <input
                                            type="file"
                                            accept="image/*,audio/*"
                                            onChange={(e) => {
                                                const file = e.target.files[0];
                                                if (file) {
                                                    handleFileUpload(file);
                                                }
                                            }}
                                            className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        />
                                        <p className="text-xs text-gray-500 mt-1">
                                            Format: JPG, PNG, MP3, WAV (Max:
                                            5MB)
                                        </p>
                                    </div>

                                    {/* Manual URL */}
                                    <div>
                                        <label className="block text-sm text-gray-600 mb-1">
                                            Atau Masukkan URL Manual
                                        </label>
                                        <input
                                            type="text"
                                            value={data.media_url}
                                            onChange={(e) =>
                                                setData(
                                                    "media_url",
                                                    e.target.value
                                                )
                                            }
                                            placeholder="https://example.com/image.jpg atau audio.mp3"
                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        {errors.media_url && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.media_url}
                                            </p>
                                        )}
                                    </div>

                                    {/* Preview Media */}
                                    {data.media_url && (
                                        <div className="mt-3 p-3 bg-gray-50 rounded-lg">
                                            <p className="text-sm font-medium text-gray-700 mb-2">
                                                Preview Media:
                                            </p>
                                            {data.media_url.match(
                                                /\.(jpg|jpeg|png|gif|webp)$/i
                                            ) ? (
                                                <div>
                                                    <img
                                                        src={data.media_url}
                                                        alt="Preview"
                                                        className="max-w-full h-32 object-cover rounded"
                                                        onError={(e) => {
                                                            e.target.style.display =
                                                                "none";
                                                            e.target.nextSibling.style.display =
                                                                "block";
                                                        }}
                                                    />
                                                    <p
                                                        className="text-xs text-gray-500 mt-1"
                                                        style={{
                                                            display: "none",
                                                        }}
                                                    >
                                                        Gambar tidak dapat
                                                        ditampilkan
                                                    </p>
                                                </div>
                                            ) : data.media_url.match(
                                                  /\.(mp3|wav|ogg|m4a)$/i
                                              ) ? (
                                                <audio
                                                    controls
                                                    className="w-full"
                                                >
                                                    <source
                                                        src={data.media_url}
                                                        type="audio/mpeg"
                                                    />
                                                    Browser tidak mendukung
                                                    audio
                                                </audio>
                                            ) : (
                                                <p className="text-sm text-gray-600">
                                                    URL: {data.media_url}
                                                </p>
                                            )}
                                        </div>
                                    )}
                                </div>

                                {/* Options for Multiple Choice */}
                                {data.type === "Pilihan Ganda" && (
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-3">
                                            Opsi Jawaban (Pilih minimal satu
                                            yang benar)
                                        </label>
                                        <div className="space-y-3">
                                            {data.options.map(
                                                (option, index) => (
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
                                                                        e.target
                                                                            .checked
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
                                                            value={
                                                                option.option_text
                                                            }
                                                            onChange={(e) =>
                                                                handleOptionChange(
                                                                    index,
                                                                    "option_text",
                                                                    e.target
                                                                        .value
                                                                )
                                                            }
                                                            placeholder={`Masukkan opsi ${option.label}`}
                                                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                        />
                                                    </div>
                                                )
                                            )}
                                        </div>
                                        <p className="mt-2 text-xs text-gray-500">
                                            Centang kotak untuk menandai jawaban
                                            yang benar
                                        </p>
                                    </div>
                                )}
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
                                    className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? "Menyimpan..." : "Update"}
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </SuperAdminLayout>
    );
}
