import React from "react";
import { Link, useLocation } from "react-router-dom";
import {
    Home,
    Building2,
    GraduationCap,
    BookOpen,
    FileText,
    Calendar,
} from "lucide-react";

const Navigation = () => {
    const location = useLocation();

    const menuItems = [
        {
            name: "Dashboard",
            href: "/dashboard",
            icon: Home,
            current: location.pathname === "/dashboard",
        },
        {
            name: "Sekolah",
            href: "/sekolah",
            icon: Building2,
            current: location.pathname === "/sekolah",
        },
        {
            name: "Jurusan",
            href: "/jurusan",
            icon: GraduationCap,
            current: location.pathname === "/jurusan",
        },
        {
            name: "Bank Soal",
            href: "/bank-soal",
            icon: BookOpen,
            current: location.pathname === "/bank-soal",
        },
        {
            name: "Hasil Tes",
            href: "/hasil-tes",
            icon: FileText,
            current: location.pathname === "/hasil-tes",
        },
        {
            name: "Jadwal TKA",
            href: "/jadwal-tka",
            icon: Calendar,
            current: location.pathname === "/jadwal-tka",
        },
    ];

    return (
        <div className="flex flex-col h-full bg-white shadow-lg">
            {/* Logo/Title */}
            <div className="flex items-center px-6 py-4 border-b border-gray-200">
                <Building2 className="h-8 w-8 text-blue-600 mr-3" />
                <h1 className="text-xl font-bold text-gray-900">Super Admin</h1>
            </div>

            {/* Navigation Menu */}
            <nav className="flex-1 px-4 py-6 space-y-2">
                {menuItems.map((item) => {
                    const Icon = item.icon;
                    return (
                        <Link
                            key={item.name}
                            to={item.href}
                            className={`flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 ${
                                item.current
                                    ? "bg-blue-50 text-blue-700 border-r-2 border-blue-700"
                                    : "text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                            }`}
                        >
                            <Icon className="h-5 w-5 mr-3" />
                            {item.name}
                        </Link>
                    );
                })}
            </nav>

            {/* User Profile */}
            <div className="px-4 py-4 border-t border-gray-200">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <div className="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                            <span className="text-white text-sm font-medium">
                                A
                            </span>
                        </div>
                    </div>
                    <div className="ml-3 flex-1">
                        <p className="text-sm font-medium text-gray-900">
                            Admin
                        </p>
                        <p className="text-xs text-gray-500">Super Admin</p>
                    </div>
                    <button className="ml-2 p-1 text-gray-400 hover:text-gray-600">
                        <svg
                            className="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Navigation;
