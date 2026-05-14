<?php

namespace Modules\Orders\Http\Controllers;

use App\Concerns\AuditableCrud;
use App\Concerns\HasAbilities;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\Http\Requests\StoreJobServiceRequest;
use Modules\Orders\Http\Requests\UpdateJobServiceRequest;
use Modules\Orders\Models\JobService;
use Modules\Orders\Services\JobServiceManager;

class JobServiceController extends Controller
{
    use AuditableCrud, HasAbilities;

    const int DEFAULT_PER_PAGE = 15;
    const array PER_PAGE_LIST  = [5, 10, 15, 25, 50];

    public function __construct(private readonly JobServiceManager $manager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage   = in_array((int) $request->input('per_page'), self::PER_PAGE_LIST)
            ? (int) $request->input('per_page')
            : self::DEFAULT_PER_PAGE;
        $paginated = $this->manager->list($request, $perPage);

        return response()->json(array_merge($paginated->toArray(), [
            'can' => $this->listAbilities(JobService::class),
        ]));
    }

    public function store(StoreJobServiceRequest $request): JsonResponse
    {
        $service = $this->manager->create($request->validated());
        $this->auditCreated($service, $this->snapshot($service));

        return response()->json($service, 201);
    }

    public function show(JobService $job_service): JsonResponse
    {
        return response()->json(array_merge($this->manager->show($job_service)->toArray(), [
            'can' => $this->resourceAbilities($job_service),
        ]));
    }

    public function update(UpdateJobServiceRequest $request, JobService $job_service): JsonResponse
    {
        $job_service->load('createdBy:id,name');
        $oldValues = $this->snapshot($job_service);

        $updated = $this->manager->update($job_service, $request->validated());
        $this->auditUpdated($updated, $oldValues, $this->snapshot($updated));

        return response()->json($updated);
    }

    public function destroy(JobService $job_service): JsonResponse
    {
        $job_service->load('createdBy:id,name');
        $this->auditDeleted($job_service, $this->snapshot($job_service));
        $this->manager->delete($job_service);

        return response()->json(['message' => 'Service deleted successfully.']);
    }

    private function snapshot(JobService $service): array
    {
        $attrs = $this->filterAuditValues($service->getAttributes());

        unset($attrs['created_by']);

        return array_merge($attrs, [
            'created_by' => ['id' => $service->createdBy?->id, 'name' => $service->createdBy?->name],
        ]);
    }
}
