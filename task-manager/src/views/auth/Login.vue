<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
    <form class="bg-white p-6 md:p-8 rounded-lg shadow border w-full max-w-md space-y-6" @submit.prevent="handleLogin"
      novalidate>
      <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Task Manager</h2>
      <p class="text-sm text-center text-gray-500 mb-4">Sign in to your account</p>

      <!-- General Error Message -->
      <p v-if="generalError" class="text-red-600 text-sm text-center -mt-3">{{ generalError }}</p>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" v-model.trim="email"
          class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
          :class="{ 'border-red-500': errors.email }" placeholder="Enter your email" required />
        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" v-model.trim="password"
          class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
          :class="{ 'border-red-500': errors.password }" placeholder="Enter your password" required />
        <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
      </div>

      <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-md transition-colors flex justify-center items-center"
        :disabled="loading">
        <span v-if="loading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
        {{ loading ? 'Logging in...' : 'Login' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const email = ref('')
const password = ref('')
const errors = reactive({ email: '', password: '' })
const generalError = ref('')
const loading = ref(false)

const router = useRouter()
const auth = useAuthStore()

function validateEmail (value) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(value)
}

async function handleLogin () {
  // Reset states
  errors.email = ''
  errors.password = ''
  generalError.value = ''
  loading.value = true

  // Local validation
  if (!email.value) {
    errors.email = 'Email is required.'
  } else if (!validateEmail(email.value)) {
    errors.email = 'Please enter a valid email.'
  }

  if (!password.value) {
    errors.password = 'Password is required.'
  } else if (password.value.length < 6) {
    errors.password = 'Password must be at least 6 characters.'
  }

  if (!errors.email && !errors.password) {
    try {
      const success = await auth.login(email.value, password.value)
      if (success) {
        router.push('/') // Redirect to dashboard
      }
    } catch (err) {
      const res = err.response
      if (res?.status === 422 && res.data?.errors) {
        // Validation errors from backend
        Object.entries(res.data.errors).forEach(([field, messages]) => {
          if (errors[field] !== undefined) {
            errors[field] = messages[0]
          }
        })
        generalError.value = res.data.message
      } else if (res?.status === 401 && res.data?.message) {
        generalError.value = res.data.message // Invalid credentials
      } else {
        generalError.value = 'Something went wrong. Try again.'
        console.error(err)
      }
    }
  }

  loading.value = false
}
</script>
