<?php
namespace App\Services;

use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserTaskRepository;
use Illuminate\Support\Collection;

class TaskService
{
    protected $taskRepo;
    protected $userRepo;
    protected $userTaskRepo;

    public function __construct(
        TaskRepository $taskRepo,
        UserRepository $userRepo,
        UserTaskRepository $userTaskRepo
    ) {
        $this->taskRepo = $taskRepo;
        $this->userRepo = $userRepo;
        $this->userTaskRepo = $userTaskRepo;
    }

    //the frst correct version
    /*public function assignTask(int $creatorId, array $data): array
    {
        $task = $this->taskRepo->create([
            'CreatorId'    => $creatorId,
            'Description'  => $data['Description'],
            'Deadline'     => $data['Deadline'],
            'Status'       => 'Pending',
            'Completed_at' => null,
        ]);

        $users = $this->getUsersToAssign($data, $creatorId);

        if ($users->isEmpty()) {
            $this->taskRepo->delete($task->id);
            throw new \Exception('No valid users to assign the task to.', 400);
        }

        $this->assignTaskToUsers($task->id, $users);

        return [
            'data' => [
                'message' => 'Task assigned successfully.',
                'task' => $task,
                'assigned_users' => $users->pluck('id'),
            ],
            'status' => 200,
        ];
    }*/

    public function assignTask(int $creatorId, array $data): array
    {
        $task = $this->taskRepo->create([
            'CreatorId'       => $creatorId,
            'Description'     => $data['Description'],
            'Deadline'        => $data['Deadline'],
            'Status'          => 'Pending',
            'Completed_at'    => null,
            'RequiresInvoice' => $data['RequiresInvoice'] ?? false,
        ]);

        $users = $this->getUsersToAssign($data, $creatorId);

        if ($users->isEmpty()) {
            $this->taskRepo->delete($task->id);
            throw new \Exception('No valid users to assign the task to.', 400);
        }

        $this->assignTaskToUsers($task->id, $users, $data['RequiresInvoice'] ?? false);

        return [
            'data' => [
                'message' => 'Task assigned successfully.',
                'task' => $task,
                'assigned_users' => $users->pluck('id'),
            ],
            'status' => 200,
        ];
    }

    protected function getUsersToAssign(array $data, int $creatorId): Collection
    {
     $users = collect();

     if (!empty($data['user_id'])) {
        $user = $this->userRepo->find($data['user_id']);
        if ($user) {
            $users->push($user);
        }
     }

     if (!empty($data['role_id'])) {
        $roleUsers = $this->userRepo->getByRoleId($data['role_id'], $creatorId);
        $users = $users->merge($roleUsers);
     }



     return $users->unique('id');
    }

   /*the first correct version without modar
    protected function assignTaskToUsers(int $taskId, Collection $users): void
    {
        foreach ($users as $user) {
            $this->userTaskRepo->create([
                'UserId' => $user->id,
                'TaskId' => $taskId,
                'Completed' => false,
            ]);
        }
    }*/

   /* protected function assignTaskToUsers(int $taskId, Collection $users, bool $requiresInvoice): void
    {

        foreach ($users as $user) {
            $userRole = $user->roles()->first(); // Spatie Role

            $userRequiresInvoice = false;

            if ($requiresInvoice === true) {
                // تحقق هل لدى المستخدم دور "logistic"
                $userRequiresInvoice = $userRole && strtolower($userRole->name) === 'logistic';
            }

            $this->userTaskRepo->create([
                'UserId'          => $user->id,
                'TaskId'          => $taskId,
                'RequiresInvoice' => $userRequiresInvoice,
                'Completed'       => false,
            ]);
        }
    }*/

    protected function assignTaskToUsers(int $taskId, Collection $users, bool $requiresInvoice): Collection
    {
        $assigned = collect();

        foreach ($users as $user) {
            $userRole = $user->roles()->first();

            $userRequiresInvoice = false;

            if ($requiresInvoice === true) {
                $userRequiresInvoice = $userRole && strtolower($userRole->name) === 'logistic';
            }

            $userTask = $this->userTaskRepo->create([
                'UserId'          => $user->id,
                'TaskId'          => $taskId,
                'RequiresInvoice' => $userRequiresInvoice,
                'Completed'       => false,
            ]);

            $userTask->user = $user;

            $assigned->push($userTask);
        }

        return $assigned;
    }

    public function completeUserTask(int $taskId, int $userId): array
    {
        $userTask = $this->userTaskRepo->findByUserAndTask($userId, $taskId);

        if (!$userTask) {
            throw new \Exception('Task not assigned to you', 404);
        }

        if ($userTask->Completed) {
            throw new \Exception('Task already completed', 400);
        }

        $this->userTaskRepo->markAsComplete($userTask->id);
        $this->updateMainTaskStatusIfAllComplete($taskId);

        return [
            'task_status' => $this->taskRepo->find($taskId)->Status,
            'completion_time' => $userTask->fresh()->updated_at,
        ];
    }

    protected function updateMainTaskStatusIfAllComplete(int $taskId): void
    {
        $incompleteCount = $this->userTaskRepo->countIncomplete($taskId);

        if ($incompleteCount === 0) {
            $this->taskRepo->markAsComplete($taskId);
        }
    }

    public function getTasks(array $filters): array
    {
        $tasks = $this->taskRepo->getWithFilters($filters);

        if (!empty($filters['task_id']) && $tasks->isEmpty()) {
            throw new \Exception('Task not found', 404);
        }

        return [
            'Tasks' => $tasks,
        ];
    }

    public function getUserTasks(array $filters, int $userId): array
    {
        $tasks = $this->taskRepo->getUserRelatedTasks($filters, $userId);

        if (!empty($filters['task_id']) && $tasks->isEmpty()) {
            throw new \Exception('Task not found or not related to user', 404);
        }

        $user = $this->userRepo->find($userId, ['id', 'name', 'email']);

        $createdTasks = $tasks->where('CreatorId', $userId);
        $assignedTasks = $tasks->where('CreatorId', '!=', $userId);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'created_tasks' => $createdTasks,
            'assigned_tasks' => $assignedTasks,
        ];
    }

    protected function formatTasks(Collection $tasks, ?int $userId): Collection
    {
        return $tasks->map(function($task) use ($userId) {
            $creator = $this->userRepo->find($task->CreatorId, ['id', 'name', 'email']);

            $userRole = null;
            if ($userId) {
                $userRole = $task->CreatorId == $userId ? 'creator' : 'assignee';
            }

            return [
                'task_id' => $task->id,
                'description' => $task->Description,
                'status' => $task->Status,
                'deadline' => $task->Deadline,
                'completed_at' => $task->Completed_at,
                'creator' => [
                    'user_id' => $creator->id,
                    'name' => $creator->name,
                    'email' => $creator->email
                ],
                'user_role' => $userRole,
                'assignees' => $this->formatAssignees($task)
            ];
        });
    }

    protected function formatAssignees($task): Collection
    {
        return $task->users->map(function($user) use ($task) {
            $userTask = $this->userTaskRepo->findByUserAndTask($user->id, $task->id);

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'completed' => $userTask->Completed,
                'completed_at' => $userTask->Completed ? $userTask->updated_at : null,
                'completion_order' => $userTask->Completed ? $userTask->updated_at->format('Y-m-d H:i:s') : 'Pending'
            ];
        })->sortByDesc('completed')->values();
    }

    protected function prepareTaskResponse(Collection $formattedTasks, array $filters): array
    {
        if (isset($filters['user_id'])) {
            $user = $this->userRepo->find($filters['user_id'], ['id', 'name', 'email']);
            return [
                'user' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'tasks' => $formattedTasks
            ];
        }

        if (isset($filters['task_id'])) {
            return ['task' => $formattedTasks->first()];
        }

        return ['tasks' => $formattedTasks];
    }

    protected function formatCreatedTasks(Collection $tasks): Collection
    {
        return $tasks->map(function($task) {
            $assignments = $this->userTaskRepo->getForTaskWithUsers($task->id)
                ->sortByDesc(function($assignment) {
                    return $assignment->Completed ? $assignment->updated_at->timestamp : 0;
                });

            return [
                'task_id' => $task->id,
                'description' => $task->Description,
                'task_status' => $task->Status,
                'deadline' => $task->Deadline,
                'task_completed_at' => $task->Completed_at,
                'your_role' => 'creator',
                'assignees' => $this->formatAssignmentDetails($assignments)
            ];
        });
    }

    protected function formatAssignedTasks(Collection $tasks, int $userId): Collection
    {
        return $tasks->map(function($task) use ($userId) {
            $creator = $this->userRepo->find($task->CreatorId, ['id', 'name', 'email']);
            $userAssignment = $this->userTaskRepo->findByUserAndTask($userId, $task->id);

            return [
                'task_id' => $task->id,
                'description' => $task->Description,
                'task_status' => $task->Status,
                'deadline' => $task->Deadline,
                'task_completed_at' => $task->Completed_at,
                'your_role' => 'assignee',
                'your_status' => [
                    'completed' => $userAssignment->Completed,
                    'completed_at' => $userAssignment->Completed ? $userAssignment->updated_at : null
                ],
                'creator' => [
                    'user_id' => $creator->id,
                    'name' => $creator->name,
                    'email' => $creator->email
                ],
                'other_assignees' => $this->getOtherAssignees($task, $userId)
            ];
        });
    }

    protected function formatAssignmentDetails(Collection $assignments): Collection
    {
        return $assignments->map(function($assignment) {
            return [
                'user_id' => $assignment->user->id,
                'name' => $assignment->user->name,
                'email' => $assignment->user->email,
                'completed' => $assignment->Completed,
                'completed_at' => $assignment->Completed ? $assignment->updated_at : null,
                'completion_order' => $assignment->Completed
                    ? $assignment->updated_at->format('Y-m-d H:i:s')
                    : 'Pending'
            ];
        })->values();
    }

    protected function getOtherAssignees($task, int $userId): Collection
    {
        return $task->users->reject(function($user) use ($userId) {
                return $user->id == $userId;
            })->map(function($user) use ($task) {
                $userTask = $this->userTaskRepo->findByUserAndTask($user->id, $task->id);

                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'completed' => $userTask->Completed,
                    'completed_at' => $userTask->Completed ? $userTask->updated_at : null
                ];
            })->values();
    }
}
