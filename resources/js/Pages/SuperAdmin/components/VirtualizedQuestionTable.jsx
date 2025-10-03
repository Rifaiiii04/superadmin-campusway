import React, { useMemo } from "react";
import { Edit, Trash2, CheckCircle } from "lucide-react";

// Import react-window using require for compatibility
const ReactWindow = require("react-window");
const List = ReactWindow.FixedSizeList;

const ITEM_HEIGHT = 120; // Height of each row

export default function VirtualizedQuestionTable({
    questions,
    onEdit,
    onDelete,
    searchTerm,
}) {
    const filteredQuestions = useMemo(() => {
        return questions.filter(
            (question) =>
                question.subject
                    .toLowerCase()
                    .includes(searchTerm.toLowerCase()) ||
                question.content
                    .toLowerCase()
                    .includes(searchTerm.toLowerCase())
        );
    }, [questions, searchTerm]);

    const Row = ({ index, style }) => {
        const question = filteredQuestions[index];

        return (
            <div
                style={style}
                className="border-b border-gray-200 hover:bg-gray-50"
            >
                <div className="px-3 sm:px-6 py-4">
                    <div className="flex items-center justify-between">
                        <div className="flex-1 min-w-0">
                            <div className="flex items-center space-x-4">
                                <div className="flex-shrink-0 w-32">
                                    <p className="text-xs sm:text-sm font-medium text-gray-900 truncate">
                                        {question.subject}
                                    </p>
                                </div>
                                <div className="flex-shrink-0 w-24">
                                    <span
                                        className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                            question.type === "Pilihan Ganda"
                                                ? "bg-maroon-100 text-maroon-800"
                                                : "bg-green-100 text-green-800"
                                        }`}
                                    >
                                        {question.type}
                                    </span>
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-xs sm:text-sm text-gray-900 truncate">
                                        {question.content}
                                    </p>
                                </div>
                                <div className="flex-shrink-0 w-16">
                                    {question.media_url ? (
                                        <div className="max-w-xs">
                                            {question.media_url.match(
                                                /\.(jpg|jpeg|png|gif|webp)$/i
                                            ) ? (
                                                <img
                                                    src={question.media_url}
                                                    alt="Media"
                                                    className="h-8 w-8 object-cover rounded"
                                                />
                                            ) : question.media_url.match(
                                                  /\.(mp3|wav|ogg|m4a)$/i
                                              ) ? (
                                                <div className="text-maroon-600">
                                                    <span className="text-xs">
                                                        🎵
                                                    </span>
                                                </div>
                                            ) : (
                                                <span className="text-xs text-gray-500">
                                                    📎
                                                </span>
                                            )}
                                        </div>
                                    ) : (
                                        <span className="text-gray-400">-</span>
                                    )}
                                </div>
                                <div className="flex-shrink-0 w-32">
                                    {question.type === "Pilihan Ganda" &&
                                    question.question_options ? (
                                        <div className="space-y-1">
                                            {question.question_options
                                                .filter(
                                                    (option) =>
                                                        option.is_correct
                                                )
                                                .slice(0, 2) // Show only first 2 correct answers
                                                .map((option, idx) => (
                                                    <div
                                                        key={idx}
                                                        className="flex items-center space-x-1"
                                                    >
                                                        <span className="inline-flex items-center justify-center w-4 h-4 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                            {String.fromCharCode(
                                                                65 +
                                                                    question.question_options.findIndex(
                                                                        (opt) =>
                                                                            opt.id ===
                                                                            option.id
                                                                    )
                                                            )}
                                                        </span>
                                                        <span className="text-xs text-green-700 font-medium truncate max-w-16">
                                                            {option.option_text}
                                                        </span>
                                                        <CheckCircle className="h-3 w-3 text-green-600 flex-shrink-0" />
                                                    </div>
                                                ))}
                                            {question.question_options.filter(
                                                (option) => option.is_correct
                                            ).length > 2 && (
                                                <span className="text-xs text-gray-500">
                                                    +
                                                    {question.question_options.filter(
                                                        (option) =>
                                                            option.is_correct
                                                    ).length - 2}{" "}
                                                    more
                                                </span>
                                            )}
                                        </div>
                                    ) : (
                                        <span className="text-gray-400">-</span>
                                    )}
                                </div>
                            </div>
                        </div>
                        <div className="flex items-center space-x-2 ml-4">
                            <button
                                onClick={() => onEdit(question)}
                                className="text-maroon-600 hover:text-maroon-900 p-1 rounded-md hover:bg-maroon-50 transition-colors"
                                title="Edit Soal"
                            >
                                <Edit className="h-4 w-4" />
                            </button>
                            <button
                                onClick={() => onDelete(question)}
                                className="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50 transition-colors"
                                title="Hapus Soal"
                            >
                                <Trash2 className="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    };

    return (
        <div className="bg-white shadow-sm rounded-lg border">
            <div className="px-4 sm:px-6 py-4 border-b">
                <h3 className="text-base sm:text-lg font-semibold text-gray-900">
                    Daftar Soal ({filteredQuestions.length} items)
                </h3>
            </div>
            <div className="overflow-hidden">
                <div className="px-3 sm:px-6 py-3 bg-gray-50 border-b">
                    <div className="flex items-center space-x-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div className="w-32">Mata Pelajaran</div>
                        <div className="w-24">Tipe</div>
                        <div className="flex-1">Soal</div>
                        <div className="w-16">Media</div>
                        <div className="w-32">Jawaban Benar</div>
                        <div className="w-20">Aksi</div>
                    </div>
                </div>
                {filteredQuestions.length > 0 ? (
                    <List
                        height={Math.min(
                            600,
                            filteredQuestions.length * ITEM_HEIGHT
                        )}
                        itemCount={filteredQuestions.length}
                        itemSize={ITEM_HEIGHT}
                        overscanCount={5}
                    >
                        {Row}
                    </List>
                ) : (
                    <div className="px-6 py-8 text-center text-gray-500">
                        Tidak ada soal yang ditemukan
                    </div>
                )}
            </div>
        </div>
    );
}
