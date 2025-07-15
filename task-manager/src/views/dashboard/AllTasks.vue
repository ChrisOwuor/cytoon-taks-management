<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold ">All Tasks</h2>
                <p class="text-gray-500">Manage all user-assigned tasks below.</p>
            </div>
            <button @click="showModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow">
                + Add Task
            </button>
        </div>

        <!-- Loading Spinner -->
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

        <!-- Task Cards -->
        <div v-else class="grid grid-cols-1 gap-4">
            <div v-for="task in tasks" :key="task.id"
                class="bg-white p-5 rounded-lg shadow hover:shadow-md transition border-l-4" :class="{
                    'border-yellow-500': task.status === 'pending',
                    'border-blue-500': task.status === 'in_progress',
                    'border-green-500': task.status === 'completed'
                }">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">{{ task.title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ task.description }}</p>
                    </div>
                    <span :class="statusColor(task.status)" class="capitalize text-sm font-semibold">
                        {{ task.status.replace('_', ' ') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 mt-4 text-sm text-gray-700">
                    <div><strong>Deadline:</strong> {{ formatDate(task.deadline) }}</div>
                    <div><strong>Assigned To:</strong> {{ task.assigned_to }}</div>
                    <div><strong>Assigned By:</strong> {{ task.assigned_by }}</div>
                    <div><strong>Created By:</strong> {{ task.created_by }}</div>
                    <div><strong>Created At:</strong> {{ formatDate(task.created_at) }}</div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > pagination.per_page"
            class="flex justify-center items-center gap-4 pt-6 border-t mt-4">
            <button :disabled="!pagination.prev_page_url" @click="fetchTasks(pagination.current_page - 1)"
                class="px-4 py-1.5 rounded border text-sm bg-white hover:bg-gray-100 disabled:opacity-50">
                « Prev
            </button>
            <span class="text-sm text-gray-700 font-medium">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>
            <button :disabled="!pagination.next_page_url" @click="fetchTasks(pagination.current_page + 1)"
                class="px-4 py-1.5 rounded border text-sm bg-white hover:bg-gray-100 disabled:opacity-50">
                Next »
            </button>
        </div>

        <!-- Add Task Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">Add New Task</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input v-model="newTask.title" type="text" class="w-full mt-1 px-3 py-2 border rounded" />
                        <p v-if="formErrors.title" class="text-red-500 text-sm mt-1">{{ formErrors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="newTask.description" class="w-full mt-1 px-3 py-2 border rounded"></textarea>
                        <p v-if="formErrors.description" class="text-red-500 text-sm mt-1">{{ formErrors.description }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select v-model="newTask.status" class="w-full mt-1 px-3 py-2 border rounded">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        <p v-if="formErrors.status" class="text-red-500 text-sm mt-1">{{ formErrors.status }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Assigned To</label>
                        <select v-model="newTask.assigned_to" class="w-full mt-1 px-3 py-2 border rounded">
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <p v-if="formErrors.assigned_to" class="text-red-500 text-sm mt-1">{{ formErrors.assigned_to }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deadline</label>
                        <input v-model="newTask.deadline" type="datetime-local"
                            class="w-full mt-1 px-3 py-2 border rounded" />
                        <p v-if="formErrors.deadline" class="text-red-500 text-sm mt-1">{{ formErrors.deadline }}</p>
                    </div>
                </div>

                <div class="flex justify-end mt-6 gap-2">
                    <button @click="showModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded">
                        Cancel
                    </button>
                    <button @click="addTask" :disabled="modalLoading"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded flex items-center gap-2">
                        <svg v-if="modalLoading" class="animate-spin h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        <span>Create Task</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const tasks = ref([])
const users = ref([])
const loading = ref(true)
const showModal = ref(false)
const modalLoading = ref(false)

const currentPage = ref(1)
const pagination = ref({})

const formErrors = ref({
    title: '',
    description: '',
    status: '',
    assigned_to: '',
    deadline: ''
})

const newTask = ref({
    title: '',
    status: 'pending',
    description: '',
    assigned_to: null,
    deadline: ''
})

const fetchTasks = async (page = 1) => {
    loading.value = true
    try {
        const res = await axios.get(`${import.meta.env.VITE_BACKEND_URL}/tasks?page=${page}`, {
            headers: { Authorization: `Bearer ${auth.token}` }
        })
        tasks.value = res.data.tasks.data
        pagination.value = res.data.tasks
        currentPage.value = res.data.tasks.current_page
    } catch (error) {
        console.error('Failed to fetch tasks:', error)
    } finally {
        loading.value = false
    }
}

const fetchUsers = async () => {
    try {
        const res = await axios.get(`${import.meta.env.VITE_BACKEND_URL}/users`, {
            headers: { Authorization: `Bearer ${auth.token}` }
        })
        users.value = res.data.users.data
    } catch (error) {
        console.error('Failed to fetch users:', error)
    }
}

const addTask = async () => {
    modalLoading.value = true
    formErrors.value = { title: '', description: '', status: '', assigned_to: '', deadline: '' }
    try {
        await axios.post(`${import.meta.env.VITE_BACKEND_URL}/tasks/add`, newTask.value, {
            headers: { Authorization: `Bearer ${auth.token}` }
        })
        showModal.value = false
        newTask.value = { title: '', status: 'pending', description: '', assigned_to: null, deadline: '' }
        fetchTasks(currentPage.value)
    } catch (err) {
        if (err.response && err.response.status === 422) {
            const errors = err.response.data.errors
            formErrors.value = {
                title: errors.title?.[0] || '',
                description: errors.description?.[0] || '',
                status: errors.status?.[0] || '',
                assigned_to: errors.assigned_to?.[0] || '',
                deadline: errors.deadline?.[0] || ''
            }
        } else {
            console.error('Error adding task:', err)
        }
    } finally {
        modalLoading.value = false
    }
}

const formatDate = (date) => new Date(date).toLocaleString()

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

onMounted(() => {
    fetchTasks()
    fetchUsers()
})
</script>
