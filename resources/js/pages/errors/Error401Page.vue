<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex items-center justify-center px-4">
        <div class="text-center">
            <p class="text-8xl font-bold text-indigo-100 select-none">401</p>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Unauthenticated</h1>
            <p class="text-sm text-gray-400 mt-2">Your session has expired or you are not logged in.</p>
            <RouterLink to="/login" class="inline-block mt-6 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                Go to login
            </RouterLink>
        </div>
    </div>
</template>