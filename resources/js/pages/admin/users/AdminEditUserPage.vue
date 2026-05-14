<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <!-- Header -->
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink to="/admin/users" @click.prevent="goBack('/admin/users', 'user-list-back')" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Update account details for <span class="font-medium text-gray-700">{{ form.name }}</span>.</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-5">

                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Name <span class="text-red-400">*</span></label>
                            <input v-model="form.name" type="text"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.name ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email <span class="text-red-400">*</span></label>
                            <input v-model="form.email" type="email"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.email ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email[0] }}</p>
                        </div>

                        <!-- Password (super only) -->
                        <div v-if="hasSuper">
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                New Password
                                <span class="text-gray-300 font-normal">(leave blank to keep current)</span>
                            </label>
                            <input v-model="form.password" type="password" placeholder="Min. 8 chars, A-z & 0-9"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.password ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password[0] }}</p>
                        </div>

                        <!-- Confirm Password (super only) -->
                        <div v-if="hasSuper">
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm New Password</label>
                            <input v-model="form.password_confirmation" type="password"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                        </div>

                        <!-- Access Control -->
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Access Control</p>
                            <div class="space-y-5">

                        <!-- Role -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Role <span class="text-red-400">*</span></label>
                            <select v-model="form.role" :disabled="!hasSuper"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                :class="[errors.role ? 'border-red-300' : 'border-gray-300', !hasSuper ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gray-50']">
                                <option v-for="role in allRoles" :key="role" :value="role">{{ role }}</option>
                            </select>
                            <p v-if="errors.role" class="mt-1 text-xs text-red-500">{{ errors.role[0] }}</p>
                        </div>

                        <!-- Permissions -->
                        <div v-if="groupedPermissions.length">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Permissions
                                <span v-if="!hasSuper" class="text-gray-300 font-normal">(read-only)</span>
                            </label>
                            <p class="text-xs text-gray-400 mb-3">Select one permission per group. Higher-level permissions include access to lower ones.</p>
                            <div class="space-y-3">
                                <div v-for="group in groupedPermissions" :key="group.key">
                                    <p class="text-xs font-medium text-gray-500 mb-1.5">{{ group.label }}</p>
                                    <div class="flex flex-wrap items-center gap-1">
                                        <template v-for="(p, i) in group.permissions" :key="p">
                                            <button type="button" @click="togglePermission(p)"
                                                :disabled="!hasSuper"
                                                class="px-2.5 py-1 text-xs rounded-md border transition-colors"
                                                :class="form.permissions.includes(p)
                                                    ? (hasSuper ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-100 text-indigo-400 border-indigo-200 cursor-not-allowed')
                                                    : (hasSuper ? 'bg-gray-50 text-gray-600 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50' : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed')">
                                                {{ permLabel(p) }}
                                            </button>
                                            <svg v-if="i < group.permissions.length - 1" class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </template>
                                        <button v-if="group.key === 'super' && hasSuper && form.permissions.length" type="button" @click="form.permissions = []"
                                            class="ml-auto text-xs text-gray-400 hover:text-red-500 transition-colors">Clear all</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                            </div>
                        </div>

                        <!-- ── Company Profile ── -->
                        <template v-if="userRole === 'customer'">
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Company Profile</p>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Company Name</label>
                                        <input v-model="form.company_profile.name" type="text"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['company_profile.name'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['company_profile.name']" class="mt-1 text-xs text-red-500">{{ errors['company_profile.name'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Role / Title</label>
                                        <input v-model="form.company_profile.role" type="text"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                                        <textarea v-model="form.company_profile.description" rows="3"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Address</label>
                                        <input v-model="form.company_profile.address" type="text"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone</label>
                                        <input v-model="form.company_profile.phone" type="text" placeholder="+95 9 xxx xxx xxx"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- ── Staff Profile ── -->
                        <template v-if="userRole && userRole !== 'customer'">
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Staff Profile</p>

                                <!-- Photo upload -->
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="relative w-[100px] h-[100px] shrink-0 group cursor-pointer" @click="photoInput?.click()">
                                        <div class="w-full h-full rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200 flex items-center justify-center">
                                            <img v-if="currentPhotoUrl" :src="currentPhotoUrl" class="w-full h-full object-cover" alt="Staff photo" />
                                            <svg v-else class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </div>
                                        <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg v-if="!photoUploading" class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                            </svg>
                                            <svg v-else class="w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                                            </svg>
                                        </div>
                                        <input ref="photoInput" type="file" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden" @change="handlePhotoUpload" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-600">Click photo to upload</p>
                                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG, WEBP · max 20MB</p>
                                        <p v-if="photoError" class="text-xs text-red-500 mt-1">{{ photoError }}</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_profile.full_name" type="text"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.full_name'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_profile.full_name']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.full_name'][0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Father Name <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_profile.father_name" type="text"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.father_name'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_profile.father_name']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.father_name'][0] }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Gender <span class="text-red-400">*</span></label>
                                            <select v-model.number="form.staff_profile.gender"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.gender'] ? 'border-red-300' : 'border-gray-300'">
                                                <option :value="null">— Select —</option>
                                                <option :value="1">Male</option>
                                                <option :value="2">Female</option>
                                            </select>
                                            <p v-if="errors['staff_profile.gender']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.gender'][0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Marital Status <span class="text-red-400">*</span></label>
                                            <select v-model.number="form.staff_profile.marital_status"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.marital_status'] ? 'border-red-300' : 'border-gray-300'">
                                                <option :value="null">— Select —</option>
                                                <option :value="1">Single</option>
                                                <option :value="2">Married</option>
                                            </select>
                                            <p v-if="errors['staff_profile.marital_status']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.marital_status'][0] }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Religion <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_profile.religion" type="text"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.religion'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_profile.religion']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.religion'][0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Ethnic Group <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_profile.ethnic_group" type="text"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.ethnic_group'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_profile.ethnic_group']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.ethnic_group'][0] }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">NRC No. <span class="text-red-400">*</span></label>
                                        <input v-model="form.staff_profile.nrc_no" type="text"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_profile.nrc_no'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['staff_profile.nrc_no']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.nrc_no'][0] }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Date of Birth <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_profile.dob" type="date"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.dob'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_profile.dob']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.dob'][0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Start Date <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_profile.start_date" type="date"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.start_date'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_profile.start_date']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.start_date'][0] }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone <span class="text-red-400">*</span></label>
                                        <input v-model="form.staff_profile.phone" type="text" placeholder="+95 9 xxx xxx xxx"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_profile.phone'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['staff_profile.phone']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.phone'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Current Address <span class="text-red-400">*</span></label>
                                        <input v-model="form.staff_profile.address" type="text"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_profile.address'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['staff_profile.address']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.address'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Home Address</label>
                                        <textarea v-model="form.staff_profile.home_address" rows="2"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"
                                            :class="errors['staff_profile.home_address'] ? 'border-red-300' : 'border-gray-300'"></textarea>
                                        <p v-if="errors['staff_profile.home_address']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.home_address'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Uniform Size <span class="text-red-400">*</span></label>
                                        <select v-model="form.staff_profile.uniform_size"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_profile.uniform_size'] ? 'border-red-300' : 'border-gray-300'">
                                            <option value="">— Select —</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                        </select>
                                        <p v-if="errors['staff_profile.uniform_size']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.uniform_size'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Education Qualification <span class="text-red-400">*</span></label>
                                        <textarea v-model="form.staff_profile.education_qualification" rows="3"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"
                                            :class="errors['staff_profile.education_qualification'] ? 'border-red-300' : 'border-gray-300'"></textarea>
                                        <p v-if="errors['staff_profile.education_qualification']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.education_qualification'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Work Experience <span class="text-red-400">*</span></label>
                                        <textarea v-model="form.staff_profile.work_experience" rows="3"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"
                                            :class="errors['staff_profile.work_experience'] ? 'border-red-300' : 'border-gray-300'"></textarea>
                                        <p v-if="errors['staff_profile.work_experience']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.work_experience'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Note</label>
                                        <textarea v-model="form.staff_profile.note" rows="2"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Staff Role (current active) -->
                            <div class="border-t border-gray-100 pt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        Current Staff Role
                                        <span class="font-normal text-gray-400 normal-case">(active assignment)</span>
                                    </p>
                                    <RouterLink :to="{ path: `/admin/users/${route.params.id}/staff-roles`, query: { back: route.fullPath } }"
                                        class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                                        View all history →
                                    </RouterLink>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Position <span class="text-red-400">*</span></label>
                                        <select v-model="form.staff_role.staff_position_id"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_role.staff_position_id'] ? 'border-red-300' : 'border-gray-300'">
                                            <option :value="null">— None —</option>
                                            <option v-for="pos in allPositions" :key="pos.id" :value="pos.id">{{ pos.name }}</option>
                                        </select>
                                        <p v-if="errors['staff_role.staff_position_id']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.staff_position_id'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Site <span class="text-red-400">*</span></label>
                                        <select v-model="form.staff_role.site_id"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_role.site_id'] ? 'border-red-300' : 'border-gray-300'">
                                            <option :value="null">— None —</option>
                                            <option v-for="site in allSites" :key="site.id" :value="site.id">{{ site.name }}</option>
                                        </select>
                                        <p v-if="errors['staff_role.site_id']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.site_id'][0] }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Salary <span class="text-red-400">*</span></label>
                                            <input v-model.number="form.staff_role.salary" type="number" min="0"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_role.salary'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_role.salary']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.salary'][0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Overtime Hourly Rate <span class="text-red-400">*</span></label>
                                            <input v-model.number="form.staff_role.overtime_hourly_rate" type="number" min="0"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_role.overtime_hourly_rate'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_role.overtime_hourly_rate']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.overtime_hourly_rate'][0] }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Start Date <span class="text-red-400">*</span></label>
                                        <input v-model="form.staff_role.start_date" type="date"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_role.start_date'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['staff_role.start_date']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.start_date'][0] }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Toggles -->
                        <div class="grid grid-cols-2 gap-4 pt-1">
                            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 bg-gray-100 cursor-pointer">
                                <span class="text-sm text-gray-600">Activated</span>
                                <button type="button" @click="form.activated = !form.activated"
                                    class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors overflow-hidden"
                                    :class="form.activated ? 'bg-indigo-600' : 'bg-gray-400'">
                                    <span class="inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform mt-0.5"
                                        :class="form.activated ? 'translate-x-4' : 'translate-x-0.5'"></span>
                                </button>
                            </label>

                            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 bg-gray-100 cursor-pointer">
                                <span class="text-sm text-gray-600">Email Verified</span>
                                <button type="button" @click="form.email_verified = !form.email_verified"
                                    class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors overflow-hidden"
                                    :class="form.email_verified ? 'bg-indigo-600' : 'bg-gray-400'">
                                    <span class="inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform mt-0.5"
                                        :class="form.email_verified ? 'translate-x-4' : 'translate-x-0.5'"></span>
                                </button>
                            </label>
                        </div>

                        <!-- General error -->
                        <p v-if="generalError" class="text-xs text-red-500">{{ generalError }}</p>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="goBack('/admin/users', 'user-list-back')"
                                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" :disabled="submitting"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                {{ submitting ? 'Saving…' : 'Save Changes' }}
                            </button>
                        </div>

                    </form>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'
import { useGoBack } from '../../../composables/useGoBack'

const router = useRouter()
const route = useRoute()
const { goBack } = useGoBack()
const { requireAdmin } = useAdminGuard()
const loading = ref(true)
const submitting = ref(false)
const errors = ref({})
const generalError = ref('')
const allPermissions = ref([])
const allRoles = ref([])
const allPositions = ref([])
const allSites = ref([])
const hasSuper = ref(false)
const userRole = ref(null)
let initialLoad = true
const photoInput = ref(null)
const photoUploading = ref(false)
const photoError = ref('')
const currentPhotoUrl = ref(null)

async function handlePhotoUpload(event) {
    const file = event.target.files?.[0]
    if (!file) return
    photoUploading.value = true
    photoError.value = ''
    try {
        const formData = new FormData()
        formData.append('photo', file)
        const { data } = await axios.post(`/api/admin/users/${route.params.id}/photo`, formData)
        currentPhotoUrl.value = data.photo_url
    } catch (e) {
        photoError.value = e.response?.data?.errors?.photo?.[0] ?? 'Upload failed. Please try again.'
    } finally {
        photoUploading.value = false
        event.target.value = ''
    }
}

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
    activated: true,
    email_verified: false,
    permissions: [],
    company_profile: {
        name: '',
        role: '',
        description: '',
        address: '',
        phone: '',
    },
    staff_profile: {
        full_name: '',
        father_name: '',
        gender: null,
        marital_status: null,
        religion: '',
        ethnic_group: '',
        nrc_no: '',
        dob: '',
        address: '',
        home_address: '',
        phone: '',
        uniform_size: '',
        education_qualification: '',
        work_experience: '',
        note: '',
        start_date: '',
    },
    staff_role: {
        staff_position_id: null,
        site_id: null,
        salary: 0,
        overtime_hourly_rate: 0,
        start_date: '',
    },
})

const PERM_ORDER = ['list', 'view', 'create', 'edit', 'update', 'delete', 'super']

function permGroupKey(p) {
    if (p === 'super') return 'super'
    if (PERM_ORDER.includes(p)) return 'general'
    const i = p.lastIndexOf('_')
    return i > 0 ? p.slice(0, i) : 'general'
}

function permBase(p) {
    if (PERM_ORDER.includes(p)) return p
    const i = p.lastIndexOf('_')
    return i > 0 ? p.slice(i + 1) : p
}

function permLabel(p) {
    const b = permBase(p)
    return b.charAt(0).toUpperCase() + b.slice(1)
}

const groupedPermissions = computed(() => {
    const map = {}
    for (const p of allPermissions.value) {
        const key = permGroupKey(p)
        if (!map[key]) map[key] = []
        map[key].push(p)
    }
    return Object.entries(map).map(([key, perms]) => ({
        key,
        label: key === 'general' ? 'Admin' : key === 'super' ? 'All Permissions' : key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()),
        permissions: [...perms].sort((a, b) => {
            const ai = PERM_ORDER.indexOf(permBase(a))
            const bi = PERM_ORDER.indexOf(permBase(b))
            return (ai < 0 ? 999 : ai) - (bi < 0 ? 999 : bi)
        }),
    })).sort((a, b) => (a.key === 'super' ? 1 : b.key === 'super' ? -1 : 0))
})

function togglePermission(p) {
    if (!hasSuper.value) return
    if (p === 'super') {
        form.value.permissions = form.value.permissions.includes('super') ? [] : ['super']
        return
    }
    const key = permGroupKey(p)
    const group = groupedPermissions.value.find(g => g.key === key)
    if (!group) return
    const current = new Set(form.value.permissions)
    if (current.has(p)) {
        current.delete(p)
    } else {
        group.permissions.forEach(gp => current.delete(gp))
        current.delete('super')
        current.add(p)
    }
    form.value.permissions = [...current]
}

watch(() => form.value.role, (role) => {
    if (initialLoad) return
    form.value.permissions = role === 'admin' ? [...allPermissions.value] : []
})

function buildPayload() {
    const base = {
        name: form.value.name,
        email: form.value.email,
        password: form.value.password,
        password_confirmation: form.value.password_confirmation,
        role: form.value.role,
        activated: form.value.activated,
        email_verified: form.value.email_verified,
        permissions: form.value.permissions,
    }

    if (userRole.value === 'customer') {
        base.company_profile = { ...form.value.company_profile }
    } else if (userRole.value && userRole.value !== 'customer') {
        base.staff_profile = { ...form.value.staff_profile }
        base.staff_role = { ...form.value.staff_role }
    }

    return base
}

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        await axios.put(`/api/admin/users/${route.params.id}`, buildPayload())
        router.push(`/admin/users/${route.params.id}`)
    } catch (e) {
        if (e?.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
        } else {
            generalError.value = 'Something went wrong. Please try again.'
        }
    } finally {
        submitting.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return

    const myPermissions = me.permissions?.map(p => p.name) ?? []
    hasSuper.value = myPermissions.includes('super')

    try {
        const [
            { data: perms },
            { data: roles },
            { data: positions },
            { data: sites },
            { data },
        ] = await Promise.all([
            axios.get('/api/admin/permissions/all'),
            axios.get('/api/admin/roles/all'),
            axios.get('/api/admin/staff-positions/all'),
            axios.get('/api/admin/sites/all'),
            axios.get(`/api/admin/users/${route.params.id}`),
        ])

        const hasSuperVal = hasSuper.value
        allPermissions.value = perms.map(p => p.name).filter(p => p !== 'super' || hasSuperVal)
        allRoles.value       = roles.map(r => r.name)
        allPositions.value   = positions
        allSites.value       = sites

        form.value.name           = data.name
        form.value.email          = data.email
        form.value.role           = data.roles?.[0]?.name ?? 'user'
        userRole.value            = form.value.role
        form.value.activated      = data.activated
        form.value.email_verified = !!data.email_verified_at
        form.value.permissions    = data.permissions?.map(p => p.name) ?? []

        if (form.value.role === 'customer' && data.company_profile) {
            const cp = data.company_profile
            form.value.company_profile = {
                name:        cp.name        ?? '',
                role:        cp.role        ?? '',
                description: cp.description ?? '',
                address:     cp.address     ?? '',
                phone:       cp.phone       ?? '',
            }
        }

        if (form.value.role !== 'customer' && data.staff_profile) {
            const sp = data.staff_profile
            currentPhotoUrl.value = sp.photo_url ?? null
            form.value.staff_profile = {
                full_name:               sp.full_name               ?? '',
                father_name:             sp.father_name             ?? '',
                gender:                  sp.gender                  ?? null,
                marital_status:          sp.marital_status          ?? null,
                religion:                sp.religion                ?? '',
                ethnic_group:            sp.ethnic_group            ?? '',
                nrc_no:                  sp.nrc_no                  ?? '',
                dob:                     sp.dob        ? sp.dob.substring(0, 10) : '',
                address:                 sp.address                 ?? '',
                home_address:            sp.home_address            ?? '',
                phone:                   sp.phone                   ?? '',
                uniform_size:            sp.uniform_size            ?? '',
                education_qualification: sp.education_qualification ?? '',
                work_experience:         sp.work_experience         ?? '',
                note:                    sp.note                    ?? '',
                start_date:              sp.start_date ? sp.start_date.substring(0, 10) : '',
            }

            const activeRole = sp.staff_roles?.find(r => !r.end_date) ?? sp.staff_roles?.[0] ?? null
            if (activeRole) {
                form.value.staff_role = {
                    staff_position_id:    activeRole.staff_position_id    ?? null,
                    site_id:             activeRole.site_id             ?? null,
                    salary:               activeRole.salary               ?? 0,
                    overtime_hourly_rate: activeRole.overtime_hourly_rate ?? 0,
                    start_date:          activeRole.start_date ? activeRole.start_date.substring(0, 10) : '',
                }
            }
        }

        await nextTick()
        initialLoad = false
        loading.value = false
    } catch (e) {
        if (!e?.response) router.push('/admin/users')
    }
})
</script>
