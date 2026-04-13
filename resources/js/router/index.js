import { createRouter, createWebHistory } from 'vue-router';
import IndexPage from '../pages/IndexPage.vue';
import LoginPage from '../pages/LoginPage.vue';
import RegisterPage from '../pages/RegisterPage.vue';
import EmailVerificationPage from '../pages/EmailVerificationPage.vue';
import ProfilePage from '../pages/ProfilePage.vue';
import ForgotPasswordPage from '../pages/ForgotPasswordPage.vue';
import ResetPasswordPage from '../pages/ResetPasswordPage.vue';
import DashboardPage from '../pages/DashboardPage.vue';
import AdminUsersPage from '../pages/AdminUsersPage.vue';
import AdminCreateUserPage from '../pages/AdminCreateUserPage.vue';
import AdminEditUserPage from '../pages/AdminEditUserPage.vue';
import AdminViewUserPage from '../pages/AdminViewUserPage.vue';
import AdminRolesPage from '../pages/AdminRolesPage.vue';
import AdminCreateRolePage from '../pages/AdminCreateRolePage.vue';
import AdminEditRolePage from '../pages/AdminEditRolePage.vue';
import AdminViewRolePage from '../pages/AdminViewRolePage.vue';
import UnauthorizedPage from '../pages/UnauthorizedPage.vue';
import NotFoundPage from '../pages/NotFoundPage.vue';

const routes = [
    { path: '/', component: IndexPage },
    { path: '/login', component: LoginPage },
    { path: '/register', component: RegisterPage },
    { path: '/verify-email', component: EmailVerificationPage },
    { path: '/profile', component: ProfilePage },
    { path: '/forgot-password', component: ForgotPasswordPage },
    { path: '/reset-password', component: ResetPasswordPage },
    { path: '/dashboard', component: DashboardPage },
    { path: '/admin/roles', component: AdminRolesPage },
    { path: '/admin/roles/create', component: AdminCreateRolePage },
    { path: '/admin/roles/:id', component: AdminViewRolePage },
    { path: '/admin/roles/:id/edit', component: AdminEditRolePage },
    { path: '/admin/users', component: AdminUsersPage },
    { path: '/admin/users/create', component: AdminCreateUserPage },
    { path: '/admin/users/:id', component: AdminViewUserPage },
    { path: '/admin/users/:id/edit', component: AdminEditUserPage },
    { path: '/unauthorized', component: UnauthorizedPage },
    { path: '/:pathMatch(.*)*', component: NotFoundPage },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});