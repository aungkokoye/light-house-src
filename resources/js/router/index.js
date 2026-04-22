import { createRouter, createWebHistory } from 'vue-router';
import IndexPage from '../pages/IndexPage.vue';
import ProfilePage from '../pages/ProfilePage.vue';
import DashboardPage from '../pages/DashboardPage.vue';

// Auth
import LoginPage from '../pages/auth/LoginPage.vue';
import GoogleCallbackPage from '../pages/auth/GoogleCallbackPage.vue';
import RegisterPage from '../pages/auth/RegisterPage.vue';
import EmailVerificationPage from '../pages/auth/EmailVerificationPage.vue';
import ForgotPasswordPage from '../pages/auth/ForgotPasswordPage.vue';
import ResetPasswordPage from '../pages/auth/ResetPasswordPage.vue';
import CompleteProfilePage from '../pages/auth/CompleteProfilePage.vue';

// Admin — Users
import AdminUsersPage from '../pages/admin/users/AdminUsersPage.vue';
import AdminCreateUserPage from '../pages/admin/users/AdminCreateUserPage.vue';
import AdminEditUserPage from '../pages/admin/users/AdminEditUserPage.vue';
import AdminViewUserPage from '../pages/admin/users/AdminViewUserPage.vue';
import AdminUserStaffRolesPage from '../pages/admin/users/AdminUserStaffRolesPage.vue';
import AdminUserViewStaffRolePage from '../pages/admin/users/AdminUserViewStaffRolePage.vue';
import AdminUserCreateStaffRolePage from '../pages/admin/users/AdminUserCreateStaffRolePage.vue';
import AdminUserEditStaffRolePage from '../pages/admin/users/AdminUserEditStaffRolePage.vue';

// Admin — Roles
import AdminRolesPage from '../pages/admin/roles/AdminRolesPage.vue';
import AdminCreateRolePage from '../pages/admin/roles/AdminCreateRolePage.vue';
import AdminEditRolePage from '../pages/admin/roles/AdminEditRolePage.vue';
import AdminViewRolePage from '../pages/admin/roles/AdminViewRolePage.vue';

// Admin — Sites
import AdminSitesPage from '../pages/admin/sites/AdminSitesPage.vue';
import AdminCreateSitePage from '../pages/admin/sites/AdminCreateSitePage.vue';
import AdminEditSitePage from '../pages/admin/sites/AdminEditSitePage.vue';
import AdminViewSitePage from '../pages/admin/sites/AdminViewSitePage.vue';

// Admin — Staff Positions
import AdminStaffPositionsPage from '../pages/admin/staff-positions/AdminStaffPositionsPage.vue';
import AdminCreateStaffPositionPage from '../pages/admin/staff-positions/AdminCreateStaffPositionPage.vue';
import AdminEditStaffPositionPage from '../pages/admin/staff-positions/AdminEditStaffPositionPage.vue';
import AdminViewStaffPositionPage from '../pages/admin/staff-positions/AdminViewStaffPositionPage.vue';

// Admin — Audit Logs
import AdminAuditLogsPage from '../pages/admin/audit-logs/AdminAuditLogsPage.vue';
import AdminViewAuditLogPage from '../pages/admin/audit-logs/AdminViewAuditLogPage.vue';

// Admin — Permissions
import AdminPermissionsPage from '../pages/admin/permissions/AdminPermissionsPage.vue';
import AdminCreatePermissionPage from '../pages/admin/permissions/AdminCreatePermissionPage.vue';
import AdminEditPermissionPage from '../pages/admin/permissions/AdminEditPermissionPage.vue';
import AdminViewPermissionPage from '../pages/admin/permissions/AdminViewPermissionPage.vue';

// Errors
import Error401Page from '../pages/errors/Error401Page.vue';
import Error403Page from '../pages/errors/Error403Page.vue';
import Error500Page from '../pages/errors/Error500Page.vue';
import NotFoundPage from '../pages/errors/NotFoundPage.vue';

const routes = [
    { path: '/', component: IndexPage },
    { path: '/login', component: LoginPage },
    { path: '/auth/callback', component: GoogleCallbackPage },
    { path: '/register', component: RegisterPage },
    { path: '/verify-email', component: EmailVerificationPage },
    { path: '/profile', component: ProfilePage },
    { path: '/forgot-password', component: ForgotPasswordPage },
    { path: '/reset-password', component: ResetPasswordPage },
    { path: '/complete-profile', component: CompleteProfilePage },
    { path: '/dashboard', component: DashboardPage },
    { path: '/admin/roles', component: AdminRolesPage },
    { path: '/admin/roles/create', component: AdminCreateRolePage },
    { path: '/admin/roles/:id', component: AdminViewRolePage },
    { path: '/admin/roles/:id/edit', component: AdminEditRolePage },
    { path: '/admin/permissions', component: AdminPermissionsPage },
    { path: '/admin/permissions/create', component: AdminCreatePermissionPage },
    { path: '/admin/permissions/:id', component: AdminViewPermissionPage },
    { path: '/admin/permissions/:id/edit', component: AdminEditPermissionPage },
    { path: '/admin/sites', component: AdminSitesPage },
    { path: '/admin/sites/create', component: AdminCreateSitePage },
    { path: '/admin/sites/:id', component: AdminViewSitePage },
    { path: '/admin/sites/:id/edit', component: AdminEditSitePage },
    { path: '/admin/staff-positions', component: AdminStaffPositionsPage },
    { path: '/admin/staff-positions/create', component: AdminCreateStaffPositionPage },
    { path: '/admin/staff-positions/:id', component: AdminViewStaffPositionPage },
    { path: '/admin/staff-positions/:id/edit', component: AdminEditStaffPositionPage },
    { path: '/admin/users', component: AdminUsersPage },
    { path: '/admin/users/create', component: AdminCreateUserPage },
    { path: '/admin/users/:id', component: AdminViewUserPage },
    { path: '/admin/users/:id/edit', component: AdminEditUserPage },
    { path: '/admin/users/:id/staff-roles',                    component: AdminUserStaffRolesPage },
    { path: '/admin/users/:id/staff-roles/create',             component: AdminUserCreateStaffRolePage },
    { path: '/admin/users/:id/staff-roles/:roleId',            component: AdminUserViewStaffRolePage },
    { path: '/admin/users/:id/staff-roles/:roleId/edit',       component: AdminUserEditStaffRolePage },
    { path: '/admin/audit-logs',      component: AdminAuditLogsPage },
    { path: '/admin/audit-logs/:id',  component: AdminViewAuditLogPage },
    { path: '/401', component: Error401Page },
    { path: '/403', component: Error403Page },
    { path: '/404', component: NotFoundPage },
    { path: '/500', component: Error500Page },
    { path: '/:pathMatch(.*)*', component: NotFoundPage },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});