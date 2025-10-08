import React from "react";
import { Head, Link } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import { ArrowLeft, Building2, Users, GraduationCap } from "lucide-react";

export default function SchoolDetail({ school, studentsCount, studentsWithChoices, studentsWithoutChoices, error }) {
    // Debug data
    console.log("School Detail - School data:", school);
    console.log("School Detail - Students count:", studentsCount);
    console.log("School Detail - Students with choices:", studentsWithChoices);
    console.log("School Detail - Students without choices:", studentsWithoutChoices);
    console.log("School Detail - Error:", error);
    console.log("School Detail - Students array:", school?.students);

    return (
        <SuperAdminLayout>
            <Head title={`Detail Sekolah - ${school.name}`} />

            <div className="p-4 sm:p-6">
                {/* Header */}
                <div className="mb-6">
                    <Link
                        href="/schools"
                        className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                    >
                        <ArrowLeft className="h-4 w-4 mr-2" />
                        Kembali ke Daftar Sekolah
                    </Link>

                    <div className="bg-white shadow-sm border rounded-lg p-6">
                        <div className="flex items-center mb-4">
                            <div className="p-3 rounded-lg bg-maroon-500">
                                <Building2 className="h-8 w-8 text-white" />
                            </div>
                            <div className="ml-4">
                                <h1 className="text-2xl font-bold text-gray-900">
                                    {school.name}
                                </h1>
                                <p className="text-gray-600">
                                    NPSN: {school.npsn}
                                </p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div className="bg-gray-50 rounded-lg p-4">
                                <div className="flex items-center">
                                    <Users className="h-5 w-5 text-gray-400 mr-2" />
                                    <span className="text-sm font-medium text-gray-600">
                                        Total Siswa
                                    </span>
                                </div>
                                <p className="text-2xl font-bold text-gray-900 mt-1">
                                    {school.students_count || 0}
                                </p>
                            </div>
                            <div className="bg-gray-50 rounded-lg p-4">
                                <div className="flex items-center">
                                    <GraduationCap className="h-5 w-5 text-gray-400 mr-2" />
                                    <span className="text-sm font-medium text-gray-600">
                                        Jurusan Dipilih
                                    </span>
                                </div>
                                <p className="text-2xl font-bold text-gray-900 mt-1">
                                    {school.students?.filter(
                                        (s) => s.major_choices?.length > 0
                                    ).length || 0}
                                </p>
                            </div>
                            <div className="bg-gray-50 rounded-lg p-4">
                                <div className="flex items-center">
                                    <span className="text-sm font-medium text-gray-600">
                                        Tanggal Daftar
                                    </span>
                                </div>
                                <p className="text-sm text-gray-900 mt-1">
                                    {new Date(
                                        school.created_at
                                    ).toLocaleDateString("id-ID")}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Students Table */}
                <div className="bg-white shadow-sm border rounded-lg">
                    <div className="px-6 py-4 border-b">
                        <h2 className="text-lg font-semibold text-gray-900">
                            Daftar Siswa
                        </h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Siswa
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kelas
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kontak
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No. Orang Tua
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jurusan Pilihan
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Daftar
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {school.students &&
                                school.students.length > 0 ? (
                                    school.students.map((student) => (
                                        <tr key={student.id}>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {student.name}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        NISN: {student.nisn}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900">
                                                    {student.kelas}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900">
                                                    {student.email || "N/A"}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {student.phone || "N/A"}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900">
                                                    {student.parent_phone ||
                                                        "N/A"}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                        student.status ===
                                                        "active"
                                                            ? "bg-green-100 text-green-800"
                                                            : "bg-gray-100 text-gray-800"
                                                    }`}
                                                >
                                                    {student.status || "N/A"}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {student.major_choices &&
                                                student.major_choices.length >
                                                    0 ? (
                                                    <div className="space-y-1">
                                                        {student.major_choices.map(
                                                            (choice, index) => (
                                                                <span
                                                                    key={index}
                                                                    className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-maroon-100 text-maroon-800 mr-1"
                                                                >
                                                                    {choice
                                                                        .major
                                                                        ?.major_name ||
                                                                        "Unknown"}
                                                                </span>
                                                            )
                                                        )}
                                                    </div>
                                                ) : (
                                                    <span className="text-sm text-gray-500">
                                                        Belum memilih
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {new Date(
                                                    student.created_at
                                                ).toLocaleDateString("id-ID")}
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td
                                            colSpan="7"
                                            className="px-6 py-4 text-center text-gray-500"
                                        >
                                            Belum ada siswa terdaftar di sekolah
                                            ini
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
