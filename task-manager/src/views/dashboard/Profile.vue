<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">My Profile</h2>
        <p class="text-gray-600 mb-6">View your account details.</p>

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

        <!-- Profile Info -->
        <div v-else class="bg-white p-6 rounded shadow-md w-full">
            <div class="flex items-center mb-6">
                <div
                    class="w-14 h-14 bg-blue-100 text-blue-700 font-bold rounded-full flex items-center justify-center text-2xl uppercase">
                    {{ user.name.charAt(0) }}
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-semibold text-gray-800">{{ user.name }}</h3>
                    <p class="text-sm text-gray-600">{{ user.email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-700">
                <div>
                    <p class="font-medium text-gray-800">Role</p>
                    <p>{{ user.role }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Created At</p>
                    <p>{{ formatDate(user.created_at) }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Last Updated</p>
                    <p>{{ formatDate(user.updated_at) }}</p>
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
const user = ref({})
const loading = ref(true)

const fetchProfile = async () => {
    try {
        const res = await axios.get(`${import.meta.env.VITE_BACKEND_URL}/user`, {
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        user.value = res.data.user
    } catch (err) {
        console.error('Error fetching profile:', err)
    } finally {
        loading.value = false
    }
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString()
}

onMounted(fetchProfile)
</script>
