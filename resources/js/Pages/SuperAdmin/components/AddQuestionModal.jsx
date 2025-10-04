import React from "react";
import { X } from "lucide-react";

export default function AddQuestionModal({
    isOpen,
    onClose,
    data,
    setData,
    errors,
    processing,
    onSubmit,
    handleFileUpload,
    handleOptionChange,
    resetForm,
}) {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div className="relative top-4 sm:top-10 mx-auto p-4 sm:p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-base sm:text-lg font-medium text-gray-900">
                        Tambah Soal Baru
                    </h3>
                    <button
                        onClick={onClose}
                        className="text-gray-400 hover:text-gray-600 transition-colors p-1"
                    >
                        <X className="h-5 w-5 sm:h-6 sm:w-6" />
                    </button>
                </div>

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
                                    setData("subject", e.target.value)
                                }
                                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 text-sm"
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
                            <input
                                type="text"
                                value="Pilihan Ganda"
                                disabled
                                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed text-sm"
                            />
                            <p className="mt-1 text-xs text-gray-500">
                                Saat ini hanya mendukung soal Pilihan Ganda
                            </p>
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Soal
                        </label>
                        <textarea
                            value={data.content}
                            onChange={(e) => setData("content", e.target.value)}
                            rows={4}
                            className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 text-sm"
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
                            <label className="block text-xs sm:text-sm text-gray-600 mb-1">
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
                                className="block w-full text-xs sm:text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:bg-maroon-50 file:text-maroon-700 hover:file:bg-maroon-100"
                            />
                            <p className="text-xs text-gray-500 mt-1">
                                Format: JPG, PNG, MP3, WAV (Max: 5MB)
                            </p>
                        </div>

                        {/* Manual URL */}
                        <div>
                            <label className="block text-xs sm:text-sm text-gray-600 mb-1">
                                Atau Masukkan URL Manual
                            </label>
                            <input
                                type="text"
                                value={data.media_url}
                                onChange={(e) =>
                                    setData("media_url", e.target.value)
                                }
                                placeholder="https://example.com/image.jpg atau audio.mp3"
                                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 text-sm"
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
                                <p className="text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                    Preview Media:
                                </p>
                                {data.media_url.match(
                                    /\.(jpg|jpeg|png|gif|webp)$/i
                                ) ? (
                                    <div>
                                        <img
                                            src={data.media_url}
                                            alt="Preview"
                                            className="max-w-full h-24 sm:h-32 object-cover rounded"
                                            onError={(e) => {
                                                e.target.style.display = "none";
                                                e.target.nextSibling.style.display =
                                                    "block";
                                            }}
                                        />
                                        <p
                                            className="text-xs text-gray-500 mt-1"
                                            style={{ display: "none" }}
                                        >
                                            Gambar tidak dapat ditampilkan
                                        </p>
                                    </div>
                                ) : data.media_url.match(
                                      /\.(mp3|wav|ogg|m4a)$/i
                                  ) ? (
                                    <audio controls className="w-full">
                                        <source
                                            src={data.media_url}
                                            type="audio/mpeg"
                                        />
                                        Browser tidak mendukung audio
                                    </audio>
                                ) : (
                                    <p className="text-xs sm:text-sm text-gray-600">
                                        URL: {data.media_url}
                                    </p>
                                )}
                            </div>
                        )}
                    </div>

                    {/* Opsi Jawaban */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-3">
                            Opsi Jawaban (Pilih minimal satu yang benar)
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
                                            checked={option.is_correct}
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
                                        className="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-maroon-500 focus:border-maroon-500 text-sm"
                                    />
                                </div>
                            ))}
                        </div>
                        <p className="mt-2 text-xs text-gray-500">
                            Centang kotak untuk menandai jawaban yang benar
                        </p>
                    </div>
                </div>

                <div className="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
                    <button
                        onClick={onClose}
                        className="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Batal
                    </button>
                    <button
                        onClick={onSubmit}
                        disabled={processing}
                        className="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-maroon-600 hover:bg-maroon-700 disabled:opacity-50 transition-colors"
                    >
                        {processing ? "Menyimpan..." : "Simpan"}
                    </button>
                </div>
            </div>
        </div>
    );
}
