<template>
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold ">Manage Users</h2>
                <p class="text-gray-600">View, create, update or delete users.</p>
            </div>
            <button @click="showAddModal = true"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Add User
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

        <!-- Success Alert -->
        <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ successMessage }}
        </div>

        <!-- User Table -->
        <div v-else class="overflow-x-auto bg-white rounded ">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Created At</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in users.data" :key="user.id"
                        :class="index % 2 === 0 ? 'bg-gray-50' : 'bg-white'" class="hover:bg-gray-100">
                        <td class="px-4 py-4 font-medium text-gray-800">{{ user.name }}</td>
                        <td class="px-4 py-4 text-gray-700">{{ user.email }}</td>
                        <td class="px-4 py-4 text-gray-700">{{ user.role }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ formatDate(user.created_at) }}</td>
                        <td class="px-4 py-4 text-right">
                            <button @click="openEditModal(user)">
                                <PencilSquareIcon class="w-5 h-5 text-blue-600 hover:text-blue-800 transition" />
                            </button>
                            <button @click="deleteUser(user.id)">
                                <TrashIcon class="w-5 h-5 text-red-600 hover:text-red-800 transition ml-2" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div v-if="users.total > users.per_page" class="mt-6 flex justify-center items-center gap-2">
            <button :disabled="!users.prev_page_url" @click="fetchUsers(users.current_page - 1)"
                class="px-3 py-1 text-sm rounded border bg-white hover:bg-gray-100 disabled:opacity-50">
                &laquo; Prev
            </button>

            <span class="text-sm font-medium text-gray-700">
                Page {{ users.current_page }} of {{ users.last_page }}
            </span>

            <button :disabled="!users.next_page_url" @click="fetchUsers(users.current_page + 1)"
                class="px-3 py-1 text-sm rounded border bg-white hover:bg-gray-100 disabled:opacity-50">
                Next &raquo;
            </button>
        </div>

        <!-- Edit User Modal -->
        <div v-if="showEditModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Edit User</h3>

                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input v-model="editUserData.name" type="text" class="w-full px-3 py-2 border rounded mb-3" />

                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input v-model="editUserData.email" type="email" class="w-full px-3 py-2 border rounded mb-3" />

                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select v-model="editUserData.role" class="w-full px-3 py-2 border rounded mb-4">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button @click="showEditModal = false"
                        class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button @click="updateUser"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div v-if="showAddModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Add New User</h3>

                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input v-model="newUser.name" type="text" class="w-full px-3 py-2 border rounded mb-1" />
                <p v-if="formErrors.name" class="text-red-500 text-sm mb-2">{{ formErrors.name }}</p>

                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input v-model="newUser.email" type="email" class="w-full px-3 py-2 border rounded mb-1" />
                <p v-if="formErrors.email" class="text-red-500 text-sm mb-2">{{ formErrors.email }}</p>

                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input v-model="newUser.password" type="password" class="w-full px-3 py-2 border rounded mb-1" />
                <p v-if="formErrors.password" class="text-red-500 text-sm mb-2">{{ formErrors.password }}</p>

                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input v-model="newUser.password_confirmation" type="password"
                    class="w-full px-3 py-2 border rounded mb-3" />

                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select v-model="newUser.role" class="w-full px-3 py-2 border rounded mb-1">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <p v-if="formErrors.role" class="text-red-500 text-sm mb-2">{{ formErrors.role }}</p>

                <div class="flex justify-end gap-2">
                    <button @click="showAddModal = false"
                        class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button @click="addUser" :disabled="modalLoading"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-2">
                        <svg v-if="modalLoading" class="animate-spin h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        <span>Create</span>
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
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'

const auth = useAuthStore()
const loading = ref(true)
const modalLoading = ref(false)
const successMessage = ref('')

const users = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    total: 0,
    per_page: 10,
    next_page_url: null,
    prev_page_url: null
})

const showEditModal = ref(false)
const showAddModal = ref(false)

const formErrors = ref({
    name: '',
    email: '',
    password: '',
    role: ''
})

const editUserData = ref({ id: null, name: '', email: '', role: '' })
const newUser = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user'
})

const fetchUsers = async (page = 1) => {
    loading.value = true
    try {
        const res = await axios.get(`${import.meta.env.VITE_BACKEND_URL}/users?page=${page}`, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        users.value = res.data.users
    } catch (err) {
        console.error('Error fetching users:', err)
    } finally {
        loading.value = false
    }
}

const deleteUser = async (id) => {
    if (!confirm('Are you sure you want to delete this user?')) return
    try {
        await axios.delete(`${import.meta.env.VITE_BACKEND_URL}/users/${id}`, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        fetchUsers(users.value.current_page)
    } catch (err) {
        console.error('Error deleting user:', err)
    }
}

const openEditModal = (user) => {
    editUserData.value = { ...user }
    showEditModal.value = true
}

const updateUser = async () => {
    try {
        await axios.put(`${import.meta.env.VITE_BACKEND_URL}/users/${editUserData.value.id}`, {
            name: editUserData.value.name,
            email: editUserData.value.email,
            role: editUserData.value.role
        }, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        showEditModal.value = false
        fetchUsers(users.value.current_page)
    } catch (err) {
        console.error('Error updating user:', err)
    }
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString()
}

const addUser = async () => {
    modalLoading.value = true
    formErrors.value = { name: '', email: '', password: '', role: '' }
    successMessage.value = ''
    try {
        await axios.post(`${import.meta.env.VITE_BACKEND_URL}/users/create`, newUser.value, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        showAddModal.value = false
        newUser.value = { name: '', email: '', password: '', password_confirmation: '', role: 'user' }
        successMessage.value = 'User created successfully.'
        fetchUsers(users.value.current_page)
    } catch (err) {
        if (err.response && err.response.status === 422) {
            const errors = err.response.data.errors
            formErrors.value = {
                name: errors.name?.[0] || '',
                email: errors.email?.[0] || '',
                password: errors.password?.[0] || '',
                role: errors.role?.[0] || ''
            }
        } else {
            console.error('Error creating user:', err)
        }
    } finally {
        modalLoading.value = false
    }
}

onMounted(() => fetchUsers())
</script>
