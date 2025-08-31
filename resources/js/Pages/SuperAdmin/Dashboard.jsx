import React from "react";
import { Head, Link } from "@inertiajs/react";
import SuperAdminLayout from "@/Layouts/SuperAdminLayout";
import {
    Building2,
    Users,
    BookOpen,
    BarChart3,
    ArrowRight,
    Calendar,
} from "lucide-react";

export default function Dashboard({ stats, recent_schools, recent_students }) {
    const statCards = [
        {
            title: "Total Sekolah",
            value: stats.total_schools,
            icon: Building2,
            color: "bg-gradient-to-r from-blue-500 to-blue-600",
            textColor: "text-blue-600",
            link: "/super-admin/schools",
        },
        {
            title: "Total Siswa",
            value: stats.total_students,
            icon: Users,
            color: "bg-gradient-to-r from-green-500 to-green-600",
            textColor: "text-green-600",
            link: "/super-admin/monitoring",
        },
        {
            title: "Total Soal",
            value: stats.total_questions,
            icon: BookOpen,
            color: "bg-gradient-to-r from-purple-500 to-purple-600",
            textColor: "text-purple-600",
            link: "/super-admin/questions",
        },
        {
            title: "Total Hasil",
            value: stats.total_results,
            icon: BarChart3,
            color: "bg-gradient-to-r from-orange-500 to-orange-600",
            textColor: "text-orange-600",
            link: "/super-admin/monitoring",
        },
    ];

    return (
        <SuperAdminLayout>
            <Head title="Super Admin Dashboard" />

            <div className="p-4 sm:p-6">
                {/* Welcome Header */}
                <div className="mb-6 sm:mb-8">
                    <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl sm:rounded-2xl p-6 sm:p-8 text-white">
                        <h1 className="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">
                            Selamat Datang! 👋
                        </h1>
                        <p className="text-lg sm:text-xl opacity-90">
                            Kelola sistem pendidikan nasional dengan mudah dan
                            efisien
                        </p>
                        <div className="mt-4 flex items-center text-sm opacity-75">
                            <Calendar className="h-4 w-4 mr-2" />
                            <span className="hidden sm:inline">
                                {new Date().toLocaleDateString("id-ID", {
                                    weekday: "long",
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                })}
                            </span>
                            <span className="sm:hidden">
                                {new Date().toLocaleDateString("id-ID", {
                                    day: "numeric",
                                    month: "short",
                                    year: "numeric",
                                })}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                    {statCards.map((stat, index) => (
                        <Link
                            key={index}
                            href={stat.link}
                            className="group bg-white rounded-lg sm:rounded-xl shadow-lg border border-gray-100 p-4 sm:p-6 hover:shadow-xl hover:scale-105 transition-all duration-300"
                        >
                            <div className="flex items-center">
                                <div
                                    className={`p-3 sm:p-4 rounded-lg sm:rounded-xl ${stat.color} shadow-lg`}
                                >
                                    <stat.icon className="h-6 w-6 sm:h-8 sm:w-8 text-white" />
                                </div>
                                <div className="ml-3 sm:ml-4">
                                    <p className="text-xs sm:text-sm font-medium text-gray-600 group-hover:text-gray-800 transition-colors">
                                        {stat.title}
                                    </p>
                                    <p
                                        className={`text-2xl sm:text-3xl font-bold ${stat.textColor} group-hover:scale-110 transition-transform`}
                                    >
                                        {stat.value}
                                    </p>
                                </div>
                            </div>
                            <div className="mt-3 sm:mt-4 flex items-center text-xs sm:text-sm text-gray-500 group-hover:text-blue-600 transition-colors">
                                <span>Lihat detail</span>
                                <ArrowRight className="h-3 w-3 sm:h-4 sm:w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                            </div>
                        </Link>
                    ))}
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                    {/* Recent Schools */}
                    <div className="bg-white rounded-lg sm:rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div className="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                            <h3 className="text-base sm:text-lg font-bold text-gray-900 flex items-center">
                                <Building2 className="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-blue-600" />
                                Sekolah Terbaru
                            </h3>
                        </div>
                        <div className="p-4 sm:p-6">
                            {recent_schools.length > 0 ? (
                                <div className="space-y-3 sm:space-y-4">
                                    {recent_schools.map((school) => (
                                        <div
                                            key={school.id}
                                            className="flex items-center justify-between p-3 sm:p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100 hover:border-blue-200 transition-colors"
                                        >
                                            <div className="min-w-0 flex-1">
                                                <p className="text-sm sm:text-base font-semibold text-gray-900 truncate">
                                                    {school.name}
                                                </p>
                                                <p className="text-xs sm:text-sm text-gray-600">
                                                    NPSN: {school.npsn}
                                                </p>
                                            </div>
                                            <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full ml-2 flex-shrink-0">
                                                {new Date(
                                                    school.created_at
                                                ).toLocaleDateString("id-ID")}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-6 sm:py-8">
                                    <Building2 className="h-10 w-10 sm:h-12 sm:w-12 text-gray-300 mx-auto mb-3" />
                                    <p className="text-sm sm:text-base text-gray-500">
                                        Belum ada sekolah terdaftar
                                    </p>
                                </div>
                            )}
                            <div className="mt-4 sm:mt-6">
                                <Link
                                    href="/super-admin/schools"
                                    className="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold text-sm sm:text-base transition-colors"
                                >
                                    Lihat semua sekolah
                                    <ArrowRight className="h-3 w-3 sm:h-4 sm:w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Recent Students */}
                    <div className="bg-white rounded-lg sm:rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div className="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                            <h3 className="text-base sm:text-lg font-bold text-gray-900 flex items-center">
                                <Users className="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-green-600" />
                                Siswa Terbaru
                            </h3>
                        </div>
                        <div className="p-4 sm:p-6">
                            {recent_students.length > 0 ? (
                                <div className="space-y-3 sm:space-y-4">
                                    {recent_students.map((student) => (
                                        <div
                                            key={student.id}
                                            className="flex items-center justify-between p-3 sm:p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100 hover:border-green-200 transition-colors"
                                        >
                                            <div className="min-w-0 flex-1">
                                                <p className="text-sm sm:text-base font-semibold text-gray-900 truncate">
                                                    {student.name}
                                                </p>
                                                <p className="text-xs sm:text-sm text-gray-600 truncate">
                                                    {student.school?.name} •
                                                    Kelas {student.kelas}
                                                </p>
                                            </div>
                                            <span className="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full ml-2 flex-shrink-0">
                                                {new Date(
                                                    student.created_at
                                                ).toLocaleDateString("id-ID")}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-6 sm:py-8">
                                    <Users className="h-10 w-10 sm:h-12 sm:w-12 text-gray-300 mx-auto mb-3" />
                                    <p className="text-sm sm:text-base text-gray-500">
                                        Belum ada siswa terdaftar
                                    </p>
                                </div>
                            )}
                            <div className="mt-4 sm:mt-6">
                                <Link
                                    href="/super-admin/monitoring"
                                    className="inline-flex items-center text-green-600 hover:text-green-800 font-semibold text-sm sm:text-base transition-colors"
                                >
                                    Lihat semua siswa
                                    <ArrowRight className="h-3 w-3 sm:h-4 sm:w-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
