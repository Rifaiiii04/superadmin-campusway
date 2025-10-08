import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { router } from "@inertiajs/react";

// Fetch schools with pagination
export function useSchools(page = 1, search = "") {
    return useQuery({
        queryKey: ["schools", page, search],
        queryFn: async () => {
            const response = await fetch(
                `/schools?page=${page}&search=${search}`
            );
            if (!response.ok) {
                throw new Error("Failed to fetch schools");
            }
            return response.json();
        },
        staleTime: 5 * 60 * 1000, // 5 minutes
        keepPreviousData: true,
    });
}

// Create school mutation
export function useCreateSchool() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (schoolData) => {
            const response = await fetch("/schools", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                },
                body: JSON.stringify(schoolData),
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || "Failed to create school");
            }

            return response.json();
        },
        onSuccess: () => {
            // Invalidate and refetch schools
            queryClient.invalidateQueries(["schools"]);
        },
    });
}

// Update school mutation
export function useUpdateSchool() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async ({ id, ...schoolData }) => {
            const response = await fetch(`/schools/${id}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                },
                body: JSON.stringify(schoolData),
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || "Failed to update school");
            }

            return response.json();
        },
        onSuccess: () => {
            queryClient.invalidateQueries(["schools"]);
        },
    });
}

// Delete school mutation
export function useDeleteSchool() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (id) => {
            const response = await fetch(`/schools/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                },
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || "Failed to delete school");
            }

            return response.json();
        },
        onSuccess: () => {
            queryClient.invalidateQueries(["schools"]);
        },
    });
}
