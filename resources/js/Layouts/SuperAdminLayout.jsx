import React from "react";
import { Link, usePage } from "@inertiajs/react";
import {
    Building2,
    BookOpen,
    TrendingUp,
    FileText,
    LogOut,
    User,
} from "lucide-react";

// Sidebar Component
function Sidebar() {
    const { auth } = usePage().props;

    const navigation = [
        { name: "Dashboard", href: "/super-admin", icon: Building2 },
        {
            name: "Kelola Sekolah",
            href: "/super-admin/schools",
            icon: Building2,
        },
        { name: "Bank Soal", href: "/super-admin/questions", icon: BookOpen },
        {
            name: "Monitoring",
            href: "/super-admin/monitoring",
            icon: TrendingUp,
        },
        { name: "Laporan", href: "/super-admin/reports", icon: FileText },
    ];

    return (
        <div className="w-64 bg-white border-r border-gray-200 flex-shrink-0 sticky top-0 h-screen overflow-y-auto">
            <div className="flex flex-col h-full">
                {/* Logo/Header */}
                <div className="flex h-16 items-center px-6 border-b border-gray-200 bg-white sticky top-0 z-10">
                    <h2 className="text-lg font-bold text-gray-900">
                        Super Admin
                    </h2>
                </div>

                {/* Navigation */}
                <nav className="flex-1 space-y-1 px-4 py-4">
                    {navigation.map((item) => (
                        <Link
                            key={item.name}
                            href={item.href}
                            className="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors"
                        >
                            <item.icon className="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" />
                            {item.name}
                        </Link>
                    ))}
                </nav>

                {/* User Info & Logout */}
                <div className="border-t border-gray-200 px-4 py-4 bg-white sticky bottom-0">
                    <div className="flex items-center mb-3">
                        <div className="flex-shrink-0">
                            <User className="h-8 w-8 text-gray-400" />
                        </div>
                        <div className="ml-3">
                            <p className="text-sm font-medium text-gray-700">
                                {auth?.user?.username || "Super Admin"}
                            </p>
                            <p className="text-xs text-gray-500">
                                Super Administrator
                            </p>
                        </div>
                    </div>
                    <Link
                        href="/super-admin/logout"
                        method="post"
                        as="button"
                        className="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <LogOut className="mr-2 h-4 w-4" />
                        Logout
                    </Link>
                </div>
            </div>
        </div>
    );
}

export default function SuperAdminLayout({ children }) {
    const { auth } = usePage().props;

    return (
        <div className="min-h-screen bg-gray-50 flex">
            {/* Sidebar - Fixed Height & Sticky */}
            <Sidebar />

            {/* Main Content */}
            <div className="flex-1 flex flex-col">
                {/* Top Bar */}
                <div className="flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-6 shadow-sm sticky top-0 z-10">
                    <div className="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <div className="flex flex-1"></div>

                        {/* Top Bar User Info */}
                        <div className="flex items-center gap-x-4">
                            <div className="flex items-center gap-x-2">
                                <User className="h-5 w-5 text-gray-400" />
                                <span className="text-sm text-gray-700">
                                    {auth?.user?.username || "Super Admin"}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Page Content - Scrollable */}
                <main className="flex-1 overflow-auto">{children}</main>
            </div>
        </div>
    );
}
