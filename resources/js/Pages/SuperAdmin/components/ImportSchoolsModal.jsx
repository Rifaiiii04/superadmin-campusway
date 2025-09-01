import React, { useState } from "react";
import { useForm } from "@inertiajs/react";
import { X, Upload, Download, FileText, AlertCircle } from "lucide-react";

export default function ImportSchoolsModal({ isOpen, onClose, onSuccess }) {
    const [dragActive, setDragActive] = useState(false);
    const [selectedFile, setSelectedFile] = useState(null);
    const [previewData, setPreviewData] = useState([]);
    const [errors, setErrors] = useState({});
    const [isImporting, setIsImporting] = useState(false);

    const { post, processing } = useForm();

    const handleDrag = (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.type === "dragenter" || e.type === "dragover") {
            setDragActive(true);
        } else if (e.type === "dragleave") {
            setDragActive(false);
        }
    };

    const handleDrop = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);

        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    };

    const handleFile = (file) => {
        if (file.type !== "text/csv" && !file.name.endsWith(".csv")) {
            setErrors({ file: "File harus berformat CSV (.csv)" });
            return;
        }

        setSelectedFile(file);
        setErrors({});
        previewCSV(file);
    };

    const previewCSV = (file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const text = e.target.result;
            const lines = text.split("\n");
            const headers = lines[0].split(";");

            // Skip header dan preview 5 baris pertama
            const preview = [];
            for (let i = 1; i < Math.min(6, lines.length); i++) {
                if (lines[i].trim()) {
                    const values = lines[i].split(";");
                    const row = {};
                    headers.forEach((header, index) => {
                        row[header.trim()] = values[index]
                            ? values[index].trim()
                            : "";
                    });
                    preview.push(row);
                }
            }
            setPreviewData(preview);
        };
        reader.readAsText(file);
    };

    const handleSubmit = () => {
        if (!selectedFile) {
            setErrors({ file: "Pilih file CSV terlebih dahulu" });
            return;
        }

        // Set loading state
        setIsImporting(true);
        setErrors({});

        console.log("🚀 Mulai import sekolah:", selectedFile.name);
        console.log("📁 File size:", selectedFile.size);
        console.log("📄 File type:", selectedFile.type);

        const formData = new FormData();
        formData.append("file", selectedFile);

        // Debug: log form data
        console.log("📋 FormData entries:");
        for (let [key, value] of formData.entries()) {
            console.log("  -", key, ":", value);
        }

        // Debug: check CSRF token
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        console.log("🔐 CSRF Token:", csrfToken ? "Ada" : "Tidak ada");

        // Debug: check current URL
        console.log("🌐 Current URL:", window.location.href);
        console.log("🎯 Target URL:", "/super-admin/schools/import");

        // Gunakan fetch langsung untuk menghindari masalah redirect
        fetch("/super-admin/schools/import", {
            method: "POST",
            headers: {
                Accept: "application/json",
            },
            body: formData,
        })
            .then((response) => {
                console.log("📡 Response status:", response.status);
                console.log("📡 Response headers:", response.headers);

                if (response.redirected) {
                    console.log("🔄 Response redirected to:", response.url);
                    // Jika redirect, coba parse response sebagai HTML untuk cari flash message
                    return response.text().then((html) => {
                        console.log(
                            "📄 HTML Response:",
                            html.substring(0, 500)
                        );

                        // Cek apakah ada flash message success
                        if (
                            html.includes("success") ||
                            html.includes("berhasil")
                        ) {
                            alert(
                                "Import sekolah berhasil! Halaman akan di-refresh."
                            );
                            onSuccess();
                            onClose();
                            setSelectedFile(null);
                            setPreviewData([]);
                        } else {
                            // Coba cari error message
                            const errorMatch = html.match(/error.*?>(.*?)</i);
                            if (errorMatch) {
                                alert("Error: " + errorMatch[1]);
                            } else {
                                alert(
                                    "Import selesai. Silakan refresh halaman untuk melihat hasil."
                                );
                                onSuccess();
                                onClose();
                            }
                        }
                        setIsImporting(false);
                    });
                }

                if (response.ok) {
                    return response.json().then((data) => {
                        console.log("✅ Import berhasil:", data);
                        alert(
                            "Import sekolah berhasil! Halaman akan di-refresh."
                        );
                        onSuccess();
                        onClose();
                        setSelectedFile(null);
                        setPreviewData([]);
                        setIsImporting(false);
                    });
                }

                // Handle error response
                return response.text().then((errorText) => {
                    console.error("❌ Import error:", errorText);
                    let errorMessage = "Terjadi kesalahan saat import";

                    try {
                        const errorData = JSON.parse(errorText);
                        if (errorData.errors) {
                            errorMessage +=
                                ":\n" +
                                Object.values(errorData.errors).join("\n");
                        } else if (errorData.message) {
                            errorMessage += ": " + errorData.message;
                        }
                    } catch (e) {
                        errorMessage += ": " + errorText.substring(0, 200);
                    }

                    alert(errorMessage);
                    setErrors({ file: errorMessage });
                    setIsImporting(false);
                });
            })
            .catch((error) => {
                console.error("❌ Network error:", error);
                alert("Gagal mengirim request: " + error.message);
                setErrors({ file: "Network error: " + error.message });
                setIsImporting(false);
            });
    };

    const downloadTemplate = () => {
        const template = [
            "NPSN;Nama Sekolah;Password",
            "12345678;SMA Negeri 1 Jakarta;password123",
            "87654321;SMA Negeri 2 Bandung;password456",
            "11223344;SMA Negeri 3 Surabaya;password789",
        ].join("\n");

        const blob = new Blob([template], { type: "text/csv;charset=utf-8;" });
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "template_import_sekolah.csv");
        link.style.visibility = "hidden";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                {/* Header */}
                <div className="flex items-center justify-between p-4 sm:p-6 border-b">
                    <h2 className="text-lg sm:text-xl font-semibold text-gray-900">
                        Import Sekolah dari File CSV
                    </h2>
                    <button
                        onClick={onClose}
                        className="text-gray-400 hover:text-gray-600 p-1"
                    >
                        <X className="h-5 w-5 sm:h-6 sm:w-6" />
                    </button>
                </div>

                <div className="p-4 sm:p-6">
                    {/* Template Download */}
                    <div className="mb-4 sm:mb-6 p-3 sm:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div className="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0">
                            <FileText className="h-5 w-5 text-blue-500 mr-2 flex-shrink-0" />
                            <div className="flex-1">
                                <h3 className="font-medium text-blue-900 text-sm sm:text-base">
                                    Download Template CSV
                                </h3>
                                <p className="text-xs sm:text-sm text-blue-700 mt-1">
                                    Gunakan template ini sebagai panduan format
                                    yang benar untuk import sekolah
                                </p>
                            </div>
                            <button
                                onClick={downloadTemplate}
                                className="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 border border-blue-300 rounded-md text-xs sm:text-sm font-medium text-blue-700 bg-white hover:bg-blue-50"
                            >
                                <Download className="h-4 w-4 mr-2" />
                                Download Template
                            </button>
                        </div>
                    </div>

                    {/* Format Requirements */}
                    <div className="mb-4 sm:mb-6 p-3 sm:p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div className="flex items-start">
                            <AlertCircle className="h-5 w-5 text-yellow-500 mr-2 mt-0.5 flex-shrink-0" />
                            <div>
                                <h3 className="font-medium text-yellow-900 text-sm sm:text-base">
                                    Format yang Diperlukan
                                </h3>
                                <ul className="text-xs sm:text-sm text-yellow-700 mt-2 space-y-1">
                                    <li>
                                        • File harus berformat CSV dengan
                                        delimiter semicolon (;)
                                    </li>
                                    <li>
                                        • <strong>Kolom wajib:</strong> NPSN,
                                        Nama Sekolah, Password
                                    </li>
                                    <li>
                                        • <strong>Kolom opsional:</strong>{" "}
                                        Alamat, Email, Telepon (bisa
                                        dikosongkan)
                                    </li>
                                    <li>• NPSN harus unik (8 digit angka)</li>
                                    <li>
                                        • Password akan di-hash secara otomatis
                                    </li>
                                    <li>
                                        • Email akan dibuat otomatis jika kosong
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {/* File Upload */}
                    <div className="mb-4 sm:mb-6">
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File CSV
                        </label>
                        <div
                            className={`border-2 border-dashed rounded-lg p-4 sm:p-6 text-center ${
                                dragActive
                                    ? "border-blue-500 bg-blue-50"
                                    : "border-gray-300 hover:border-gray-400"
                            }`}
                            onDragEnter={handleDrag}
                            onDragLeave={handleDrag}
                            onDragOver={handleDrag}
                            onDrop={handleDrop}
                        >
                            <Upload className="mx-auto h-8 w-8 sm:h-12 sm:w-12 text-gray-400" />
                            <div className="mt-3 sm:mt-4">
                                <p className="text-xs sm:text-sm text-gray-600">
                                    <span className="font-medium text-blue-600 hover:text-blue-500">
                                        Klik untuk upload
                                    </span>{" "}
                                    atau drag and drop
                                </p>
                                <p className="text-xs text-gray-500 mt-1">
                                    File CSV dengan delimiter semicolon (;)
                                </p>
                            </div>
                            <input
                                type="file"
                                accept=".csv"
                                name="csv_file"
                                onChange={(e) => handleFile(e.target.files[0])}
                                className="hidden"
                                id="file-upload"
                            />
                            <label
                                htmlFor="file-upload"
                                className="mt-3 sm:mt-4 inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 cursor-pointer"
                            >
                                Pilih File
                            </label>
                        </div>
                        {errors.file && (
                            <p className="mt-2 text-sm text-red-600">
                                {errors.file}
                            </p>
                        )}
                    </div>

                    {/* File Info */}
                    {selectedFile && (
                        <div className="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div className="flex items-center">
                                <FileText className="h-5 w-5 text-green-500 mr-2 flex-shrink-0" />
                                <div>
                                    <p className="font-medium text-green-900 text-sm sm:text-base">
                                        File dipilih: {selectedFile.name}
                                    </p>
                                    <p className="text-xs sm:text-sm text-green-700">
                                        Ukuran:{" "}
                                        {(selectedFile.size / 1024).toFixed(2)}{" "}
                                        KB
                                    </p>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Preview Data */}
                    {previewData.length > 0 && (
                        <div className="mb-4 sm:mb-6">
                            <h3 className="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">
                                Preview Data (5 baris pertama)
                            </h3>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 border border-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            {Object.keys(
                                                previewData[0] || {}
                                            ).map((header) => (
                                                <th
                                                    key={header}
                                                    className="px-2 sm:px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200"
                                                >
                                                    {header}
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {previewData.map((row, index) => (
                                            <tr key={index}>
                                                {Object.values(row).map(
                                                    (value, cellIndex) => (
                                                        <td
                                                            key={cellIndex}
                                                            className="px-2 sm:px-3 py-2 text-xs sm:text-sm text-gray-900 border border-gray-200"
                                                        >
                                                            {value || "-"}
                                                        </td>
                                                    )
                                                )}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}

                    {/* Action Buttons */}
                    <div className="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button
                            onClick={onClose}
                            className="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            onClick={handleSubmit}
                            disabled={!selectedFile || isImporting}
                            className="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            {isImporting ? (
                                <>
                                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                    Mengimport...
                                </>
                            ) : (
                                <>
                                    <Upload className="h-4 w-4 mr-2" />
                                    Import Sekolah
                                </>
                            )}
                        </button>
                    </div>
                </div>
            </div>

            {/* Loading Overlay */}
            {isImporting && (
                <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-60">
                    <div className="bg-white rounded-lg p-4 sm:p-6 shadow-xl mx-4">
                        <div className="flex items-center space-x-3">
                            <div className="animate-spin rounded-full h-6 w-6 sm:h-8 sm:w-8 border-b-2 border-blue-600"></div>
                            <div>
                                <p className="text-base sm:text-lg font-medium text-gray-900">
                                    Mengimport Sekolah...
                                </p>
                                <p className="text-sm text-gray-500">
                                    Mohon tunggu sebentar
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
