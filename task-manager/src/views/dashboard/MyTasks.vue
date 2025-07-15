<template>
    <div>
        <h2 class="text-2xl font-bold  mb-4">My Tasks</h2>
        <p class="text-gray-600 mb-6">View and update your assigned tasks.</p>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center h-48">
            <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor" />
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill" />
            </svg>
        </div>

        <!-- Tasks -->
        <!-- Tasks -->
        <div v-else class="space-y-4">
            <div v-for="task in tasks" :key="task.id"
                class="flex flex-col md:flex-row items-start md:items-center justify-between bg-blue-50 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-blue-800">{{ task.title }}</h3>
                        <span :class="statusColor(task.status)">
                            {{ task.status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 mb-2">{{ task.description }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600">
                        <div><strong>Deadline:</strong> {{ formatDate(task.deadline) }}</div>
                        <div><strong>Created At:</strong> {{ formatDate(task.created_at) }}</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 md:mt-0 md:ml-4 flex gap-2">
                    <button v-if="task.status === 'pending'" @click="updateStatus(task.id, 'in_progress')"
                        class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded">
                        Start
                    </button>
                    <button v-if="task.status === 'in_progress'" @click="updateStatus(task.id, 'completed')"
                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded">
                        Complete
                    </button>
                </div>
            </div>
        </div>


        <!-- Pagination -->
        <div v-if="pagination.total > pagination.per_page" class="mt-6 flex justify-center items-center gap-2">
            <button :disabled="!pagination.prev_page_url" @click="fetchTasks(pagination.current_page - 1)"
                class="px-3 py-1 text-sm rounded border bg-white hover:bg-gray-100 disabled:opacity-50">
                &laquo; Prev
            </button>

            <span class="text-sm font-medium text-gray-700">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>

            <button :disabled="!pagination.next_page_url" @click="fetchTasks(pagination.current_page + 1)"
                class="px-3 py-1 text-sm rounded border bg-white hover:bg-gray-100 disabled:opacity-50">
                Next &raquo;
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const tasks = ref([])
const loading = ref(true)

const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
    per_page: 10,
    next_page_url: null,
    prev_page_url: null,
})

const fetchTasks = async (page = 1) => {
    loading.value = true
    try {
        const res = await axios.get(`${import.meta.env.VITE_BACKEND_URL}/tasks/user-tasks?page=${page}`, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })

        tasks.value = res.data.tasks.data
        pagination.value = {
            ...res.data.tasks,
            current_page: res.data.tasks.current_page,
            last_page: res.data.tasks.last_page,
            total: res.data.tasks.total,
            per_page: res.data.tasks.per_page,
            next_page_url: res.data.tasks.next_page_url,
            prev_page_url: res.data.tasks.prev_page_url
        }
    } catch (err) {
        console.error('Failed to fetch user tasks:', err)
    } finally {
        loading.value = false
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}
const updateStatus = async (taskId, newStatus) => {
    try {
        await axios.put(`${import.meta.env.VITE_BACKEND_URL}/tasks/status-update/${taskId}`, {
            status: newStatus,
        }, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        fetchTasks(pagination.value.current_page) // Refresh list
    } catch (err) {
        console.error(`Failed to update status for task ${taskId}:`, err)
    }
}

const statusColor = (status) => {
    switch (status) {
        case 'pending':
            return 'text-yellow-600 font-medium'
        case 'completed':
            return 'text-green-600 font-medium'
        case 'in_progress':
            return 'text-blue-600 font-medium'
        default:
            return 'text-gray-500'
    }
}

onMounted(() => fetchTasks())
</script>
