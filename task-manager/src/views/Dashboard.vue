<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div :class="[
            'fixed md:relative z-30 md:z-0 h-full bg-white border-r shadow-md transition-transform duration-200',
            sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64 md:translate-x-0'
        ]">
            <div class="p-4 border-b font-bold text-lg flex items-center gap-1 justify-between">Task Manager
                <span @click="sidebarOpen = false">
                    <Bars3Icon class="w-6 h-6" />
                </span>

            </div>

            <nav class="flex flex-col p-4 gap-2">
                <RouterLink to="/" :class="[
                    'flex items-center px-3 py-2 text-sm font-medium rounded hover:bg-gray-200 transition',
                    route.name === 'Home' ? 'bg-blue-100' : ''
                ]">
                    <HomeIcon class="w-5 h-5 mr-2" />
                    Dashboard
                </RouterLink>

                <RouterLink v-if="auth.role === 'admin'" to="/tasks" :class="[
                    'flex items-center px-3 py-2 text-sm font-medium rounded hover:bg-gray-200 transition',
                    route.name === 'AllTasks' ? 'bg-blue-100' : ''
                ]">
                    <ClipboardDocumentListIcon class="w-5 h-5 mr-2" />
                    All Tasks
                </RouterLink>

                <RouterLink v-if="auth.role === 'admin'" to="/users" :class="[
                    'flex items-center px-3 py-2 text-sm font-medium rounded hover:bg-gray-200 transition',
                    route.name === 'UserList' ? 'bg-blue-100' : ''
                ]">
                    <UsersIcon class="w-5 h-5 mr-2" />
                    Manage Users
                </RouterLink>

                <RouterLink v-if="auth.role === 'user'" to="/my-tasks" :class="[
                    'flex items-center px-3 py-2 text-sm font-medium rounded hover:bg-gray-200 transition',
                    route.name === 'MyTasks' ? 'bg-blue-100' : ''
                ]">
                    <CheckCircleIcon class="w-5 h-5 mr-2" />
                    My Tasks
                </RouterLink>

                <RouterLink to="/profile" :class="[
                    'flex items-center px-3 py-2 text-sm font-medium rounded hover:bg-gray-200 transition',
                    route.name === 'Profile' ? 'bg-blue-100' : ''
                ]">
                    <UserCircleIcon class="w-5 h-5 mr-2" />
                    Profile
                </RouterLink>


                <button @click="logout" class="flex items-center mt-4 text-red-600 hover:text-red-800 transition">
                    <ArrowLeftOnRectangleIcon class="w-5 h-5 mr-2" />
                    Logout
                </button>
            </nav>
        </div>

        <!-- Main Area -->
        <div class="flex-1 flex flex-col">
            <!-- Top bar (Mobile toggle) -->
            <div class="md:hidden flex items-center justify-between bg-white shadow p-4">
                <button @click="sidebarOpen = !sidebarOpen">
                    <Bars3Icon class="w-6 h-6" />
                </button>
                <h2 class="font-semibold text-lg">Dashboard</h2>
                <span></span>
            </div>

            <!-- Dynamic Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import {
    Bars3Icon,
    ClipboardDocumentListIcon,
    UsersIcon,
    UserCircleIcon,
    HomeIcon,
    CheckCircleIcon,
    ArrowLeftOnRectangleIcon,
} from '@heroicons/vue/24/outline'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const sidebarOpen = ref(false)

const logout = () => {
    console.log('Logging out...')
    auth.logout()
    router.push('/login')
}
</script>
