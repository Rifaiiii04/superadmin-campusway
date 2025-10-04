import React from "react";
import { Edit, Trash2, CheckCircle } from "lucide-react";

export default function QuestionTable({
    questions,
    onEdit,
    onDelete,
    searchTerm,
}) {
    const filteredQuestions = questions.filter(
        (question) =>
            question.subject.toLowerCase().includes(searchTerm.toLowerCase()) ||
            question.content.toLowerCase().includes(searchTerm.toLowerCase())
    );

    return (
        <div className="bg-white shadow-sm rounded-lg border">
            <div className="px-4 sm:px-6 py-4 border-b">
                <h3 className="text-base sm:text-lg font-semibold text-gray-900">
                    Daftar Soal
                </h3>
            </div>
            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mata Pelajaran
                            </th>
                            <th className="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipe
                            </th>
                            <th className="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Soal
                            </th>
                            <th className="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Media
                            </th>
                            <th className="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jawaban Benar
                            </th>
                            <th className="px-3 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {filteredQuestions.map((question) => (
                            <tr key={question.id} className="hover:bg-gray-50">
                                <td className="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                                    {question.subject}
                                </td>
                                <td className="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <span
                                        className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                            question.type === "Pilihan Ganda"
                                                ? "bg-maroon-100 text-maroon-800"
                                                : "bg-green-100 text-green-800"
                                        }`}
                                    >
                                        {question.type}
                                    </span>
                                </td>
                                <td className="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-900">
                                    <div className="max-w-xs truncate">
                                        {question.content}
                                    </div>
                                </td>
                                <td className="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-900">
                                    {question.media_url ? (
                                        <div className="max-w-xs">
                                            {question.media_url.match(
                                                /\.(jpg|jpeg|png|gif|webp)$/i
                                            ) ? (
                                                <img
                                                    src={question.media_url}
                                                    alt="Media"
                                                    className="h-12 w-12 sm:h-16 sm:w-16 object-cover rounded"
                                                />
                                            ) : question.media_url.match(
                                                  /\.(mp3|wav|ogg|m4a)$/i
                                              ) ? (
                                                <div className="text-maroon-600">
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
                                        <span className="text-gray-400">-</span>
                                    )}
                                </td>
                                <td className="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-900">
                                    {question.type === "Pilihan Ganda" &&
                                    question.question_options ? (
                                        <div className="space-y-1">
                                            {question.question_options
                                                .filter(
                                                    (option) =>
                                                        option.is_correct
                                                )
                                                .map((option, index) => (
                                                    <div
                                                        key={index}
                                                        className="flex items-center space-x-2"
                                                    >
                                                        <span className="inline-flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                            {String.fromCharCode(
                                                                65 +
                                                                    question.question_options.findIndex(
                                                                        (opt) =>
                                                                            opt.id ===
                                                                            option.id
                                                                    )
                                                            )}
                                                        </span>
                                                        <span className="text-xs text-green-700 font-medium truncate max-w-20 sm:max-w-32">
                                                            {option.option_text}
                                                        </span>
                                                        <CheckCircle className="h-3 w-3 text-green-600 flex-shrink-0" />
                                                    </div>
                                                ))}
                                            {question.question_options.filter(
                                                (option) => option.is_correct
                                            ).length === 0 && (
                                                <span className="text-xs text-red-600 italic">
                                                    Belum ada jawaban benar
                                                </span>
                                            )}
                                            {question.question_options.filter(
                                                (option) => option.is_correct
                                            ).length > 0 && (
                                                <div className="mt-1 pt-1 border-t border-gray-100">
                                                    <span className="text-xs text-gray-500">
                                                        Total:{" "}
                                                        {
                                                            question
                                                                .question_options
                                                                .length
                                                        }{" "}
                                                        opsi
                                                    </span>
                                                </div>
                                            )}
                                        </div>
                                    ) : (
                                        <span className="text-gray-400">-</span>
                                    )}
                                </td>
                                <td className="px-3 sm:px-6 py-4 whitespace-nowrap text-right text-xs sm:text-sm font-medium">
                                    <div className="flex items-center justify-end space-x-2 sm:space-x-3">
                                        <button
                                            onClick={() => onEdit(question)}
                                            className="text-maroon-600 hover:text-maroon-900 p-1 sm:p-2 rounded-md hover:bg-maroon-50 transition-colors"
                                            title="Edit Soal"
                                        >
                                            <Edit className="h-4 w-4 sm:h-5 sm:w-5" />
                                        </button>
                                        <button
                                            onClick={() => onDelete(question)}
                                            className="text-red-600 hover:text-red-900 p-1 sm:p-2 rounded-md hover:bg-red-50 transition-colors"
                                            title="Hapus Soal"
                                        >
                                            <Trash2 className="h-4 w-4 sm:h-5 sm:w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
