<template>
    <div class="min-h-screen bg-gray-100">
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="border-4 border-dashed border-gray-200 rounded-lg h-96 flex items-center justify-center">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Welcome to SuperAdmin Dashboard</h2>
                        <p class="text-gray-600 mb-4">User: {{ user ? user.name : 'Guest' }}</p>
                        <form @submit.prevent="logout" class="inline">
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        user: Object,
    },
    methods: {
        logout() {
            this.$inertia.post('/super-admin/logout')
        }
    }
}
</script>
