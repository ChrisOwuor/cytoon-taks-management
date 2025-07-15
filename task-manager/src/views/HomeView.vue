<template>
    <div class="space-y-6">
        <!-- Welcome Message -->
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-blue-700">Welcome, {{ auth.name }} 👋</h1>
            <p class="text-gray-600 mt-2">
                Here's a quick overview of your task management dashboard.
            </p>
        </div>

        <!-- Quick Stats (optional per role) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-4 rounded shadow flex items-center gap-4">
                <ClipboardDocumentListIcon class="w-8 h-8 text-blue-600" />
                <div>
                    <p class="text-sm text-gray-500">Total Tasks</p>
                    <p class="text-xl font-semibold text-blue-800">{{ stats.total }}</p>
                </div>
            </div>
            <div class="bg-yellow-50 p-4 rounded shadow flex items-center gap-4">
                <ClockIcon class="w-8 h-8 text-yellow-600" />
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-xl font-semibold text-yellow-700">{{ stats.pending }}</p>
                </div>
            </div>
            <div class="bg-green-50 p-4 rounded shadow flex items-center gap-4">
                <CheckCircleIcon class="w-8 h-8 text-green-600" />
                <div>
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-xl font-semibold text-green-700">{{ stats.completed }}</p>
                </div>
            </div>
        </div>

        <!-- Role-specific shortcut -->
        <div v-if="auth.role === 'admin'" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Quick Admin Links</h2>
            <div class="flex flex-wrap gap-4">
                <RouterLink to="/tasks" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Manage Tasks
                </RouterLink>
                <RouterLink to="/users" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                    Manage Users
                </RouterLink>
            </div>
        </div>

        <div v-else class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Need to check your tasks?</h2>
            <RouterLink to="/my-tasks"
                class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                View My Tasks
            </RouterLink>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

import {
    ClipboardDocumentListIcon,
    ClockIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/outline'

const auth = useAuthStore()

const stats = ref({
    total: 0,
    pending: 0,
    completed: 0
})

const fetchStats = async () => {
    try {
        const res = await axios.get(`${import.meta.env.VITE_BACKEND_URL}/stats`, {
            headers: { Authorization: `Bearer ${auth.token}` }
        })
        stats.value = res.data
    } catch (err) {
        console.error('Failed to fetch dashboard stats:', err)
    }
}

onMounted(() => {
    fetchStats()
})
</script>
