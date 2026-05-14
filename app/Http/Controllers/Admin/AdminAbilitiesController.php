<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\ChatKnowledgeCategoryPolicy;
use App\Policies\ChatKnowledgePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SitePolicy;
use App\Policies\StaffPositionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Http\JsonResponse;
use Modules\Orders\Policies\BankPolicy;
use Modules\Orders\Policies\InvoicePolicy;
use Modules\Orders\Policies\JobServicePolicy;
use Modules\Orders\Policies\ProductPolicy;

class AdminAbilitiesController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        return response()->json([
            'can' => [
                'profile'                   => true,
                'users'                     => (new UserPolicy)->viewAny($user),
                'sites'                     => (new SitePolicy)->viewAny($user),
                'staff_positions'           => (new StaffPositionPolicy)->viewAny($user),
                'roles'                     => (new RolePolicy)->viewAny($user),
                'permissions'               => (new PermissionPolicy)->viewAny($user),
                'chat_knowledge'            => (new ChatKnowledgePolicy)->viewAny($user),
                'chat_knowledge_categories' => (new ChatKnowledgeCategoryPolicy)->viewAny($user),
                'audit_logs'                => (new AuditLogPolicy)->viewAny($user),
                'invoices'                  => (new InvoicePolicy)->viewAny($user),
                'banks'                     => (new BankPolicy)->viewAny($user),
                'products'                  => (new ProductPolicy)->viewAny($user),
                'services'                  => (new JobServicePolicy)->viewAny($user),
            ],
        ]);
    }
}
