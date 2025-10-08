import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";

// Fetch questions with pagination and search
export function useQuestions(page = 1, search = "", subject = "") {
    return useQuery({
        queryKey: ["questions", page, search, subject],
        queryFn: async () => {
            const params = new URLSearchParams({
                page: page.toString(),
                ...(search && { search }),
                ...(subject && { subject }),
            });

            const response = await fetch(`/questions?${params}`);
            if (!response.ok) {
                throw new Error("Failed to fetch questions");
            }
            return response.json();
        },
        staleTime: 5 * 60 * 1000, // 5 minutes
        keepPreviousData: true,
    });
}

// Create question mutation
export function useCreateQuestion() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (questionData) => {
            const response = await fetch("/questions", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                },
                body: JSON.stringify(questionData),
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || "Failed to create question");
            }

            return response.json();
        },
        onSuccess: () => {
            queryClient.invalidateQueries(["questions"]);
        },
    });
}

// Update question mutation
export function useUpdateQuestion() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async ({ id, ...questionData }) => {
            const response = await fetch(`/questions/${id}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                },
                body: JSON.stringify(questionData),
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || "Failed to update question");
            }

            return response.json();
        },
        onSuccess: () => {
            queryClient.invalidateQueries(["questions"]);
        },
    });
}

// Delete question mutation
export function useDeleteQuestion() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (id) => {
            const response = await fetch(`/questions/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                },
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || "Failed to delete question");
            }

            return response.json();
        },
        onSuccess: () => {
            queryClient.invalidateQueries(["questions"]);
        },
    });
}

// Fetch question statistics
export function useQuestionStats() {
    return useQuery({
        queryKey: ["question-stats"],
        queryFn: async () => {
            const response = await fetch("/questions/stats");
            if (!response.ok) {
                throw new Error("Failed to fetch question stats");
            }
            return response.json();
        },
        staleTime: 10 * 60 * 1000, // 10 minutes
    });
}
