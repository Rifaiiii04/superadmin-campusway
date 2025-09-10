import React, { useState } from "react";
import { Link, usePage } from "@inertiajs/react";
import {
    Building2,
    BookOpen,
    BarChart3,
    FileText,
    LogOut,
    Menu,
    X,
    Home,
    GraduationCap,
    Calendar,
} from "lucide-react";

export default function SuperAdminLayout({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { auth } = usePage().props;

    const navigation = [
        { name: "Dashboard", href: "/super-admin", icon: Home },
        {
            name: "Sekolah",
            href: "/super-admin/schools",
            icon: Building2,
        },
        {
            name: "Jurusan",
            href: "/super-admin/major-recommendations",
            icon: GraduationCap,
        },
        { name: "Bank Soal", href: "/super-admin/questions", icon: BookOpen },
        { name: "Hasil Tes", href: "/super-admin/results", icon: FileText },
        {
            name: "Jadwal TKA",
            href: "/super-admin/tka-schedules",
            icon: Calendar,
        },
    ];

    const toggleSidebar = () => {
        setSidebarOpen(!sidebarOpen);
    };

    const closeSidebar = () => {
        setSidebarOpen(false);
    };

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Mobile sidebar overlay */}
            {sidebarOpen && (
                <div
                    className="fixed inset-0 z-40 lg:hidden bg-black bg-opacity-50"
                    onClick={closeSidebar}
                />
            )}

            {/* Sidebar */}
            <div
                className={`fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out ${
                    sidebarOpen
                        ? "translate-x-0"
                        : "-translate-x-full lg:translate-x-0"
                }`}
            >
                <div className="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                    <div className="flex items-center">
                        <Building2 className="h-8 w-8 text-maroon-600" />
                        <span className="ml-3 text-xl font-bold text-gray-900">
                            Super Admin
                        </span>
                    </div>
                    <button
                        onClick={closeSidebar}
                        className="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                    >
                        <X className="h-6 w-6" />
                    </button>
                </div>

                <nav className="mt-6 px-3">
                    <div className="space-y-1">
                        {navigation.map((item) => {
                            const isActive =
                                window.location.pathname === item.href;
                            return (
                                <Link
                                    key={item.name}
                                    href={item.href}
                                    onClick={closeSidebar}
                                    className={`group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${
                                        isActive
                                            ? "bg-maroon-100 text-maroon-700 border-r-2 border-maroon-700"
                                            : "text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                                    }`}
                                >
                                    <item.icon
                                        className={`mr-3 h-5 w-5 ${
                                            isActive
                                                ? "text-maroon-700"
                                                : "text-gray-400 group-hover:text-gray-500"
                                        }`}
                                    />
                                    {item.name}
                                </Link>
                            );
                        })}
                    </div>
                </nav>

                {/* User info and logout */}
                <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center">
                            <div className="h-8 w-8 bg-maroon-100 rounded-full flex items-center justify-center">
                                <span className="text-sm font-medium text-maroon-700">
                                    {auth?.user?.username
                                        ?.charAt(0)
                                        ?.toUpperCase() || "A"}
                                </span>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm font-medium text-gray-700">
                                    {auth?.user?.username || "Admin"}
                                </p>
                                <p className="text-xs text-gray-500">
                                    Super Admin
                                </p>
                            </div>
                        </div>
                        <Link
                            href="/super-admin/logout"
                            method="post"
                            as="button"
                            className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md"
                        >
                            <LogOut className="h-5 w-5" />
                        </Link>
                    </div>
                </div>
            </div>

            {/* Main content */}
            <div className="lg:ml-64">
                {/* Top bar */}
                <div className="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200 top-bar">
                    <div className="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <button
                            onClick={toggleSidebar}
                            className="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                        >
                            <Menu className="h-6 w-6" />
                        </button>

                        <div className="flex-1 lg:hidden" />

                        <div className="flex items-center space-x-4">
                            <div className="hidden sm:block">
                                <p className="text-sm text-gray-500">
                                    {new Date().toLocaleDateString("id-ID", {
                                        weekday: "long",
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    })}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Page content */}
                <main className="min-h-screen">{children}</main>
            </div>
        </div>
    );
}
