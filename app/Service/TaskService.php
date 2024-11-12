<?php

namespace App\Service;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TaskService
{

    /**
     ** This function is created to show all tasks.
     ** @param  TaskRequest $taskRequest
     ** @return array(data,status,message)
     */
    public function showAllTasks($data)
    {
        try {
            $tasks = Task::query();

            if (isset($data['priority'])) {
                $tasks = $tasks->byPriority($data['priority']);
            }
            if (isset($data['status'])) {
                $tasks = $tasks->byStatus($data['status']);
            }

            if (Auth::user()->role == 'user') {
                $tasks = $tasks->byuser(Auth::user()->id);
            }

            $tasks = $tasks->get(); // Execute the query

            return [
                'message' => 'تمت عملية العرض بنجاح',
                'data' => $tasks,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('Error in showing tasks: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء العرض',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________

    /**
     **This function is created to store a new task.
     * *@param array $data
     ** @return array(data,status,message)
     */
    public function createTask($data)
    {
        try {
            // Create a new task
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'assigned_to' => $data['assigned_to'] ?? 1,
                'assigned_by' => Auth::user()->id,
            ]);
            // Return data
            return [
                'message' => 'تم إنشاء المهمة',
                'data' => $task,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('Error in creat task: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء التحديث',
                'status' => 500,
                'data' => 'لم يتم تحديث البيانات'
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
 * This function is created to user  update task.
 * @param array $data
 * @param int $id
 * @return array(data,status,message)
 */
    public function userupdateTask($data, $id)
    {
        try {
            $task = Task::find($id);
            // Check if the task exists
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'status' => 404,
                    'data' => 'لم يتم عرض البيانات'
                ];
            }
            // Check if the role of user to allow him to change the status of the task and the task is assigned to him
            if (Auth::user()->id == $task->assigned_to) {
                // Check if he wants to update the status of the task
                if (isset($data['status'])) {
                    // Update the status
                    $task->update(['status' => $data['status'] ?? $task->status]);
                    return [
                        'message' => 'تم تغيير حالة المهمة',
                        'data' => $task,
                        'status' => 201,
                    ];
                } else {
                    return [
                        'message' => 'لا يحق لك تغيير إلا حالة المهمة',
                        'status' => 403,
                        'data' => 'لم يتم عرض البيانات'
                    ];
                }
            } else {
                return [
                    'message' => 'لا يحق لك القيام بهذه العملية',
                    'status' => 403,
                    'data' => 'لم يتم عرض البيانات'
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in updating task: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء التحديث',
                'status' => 500,
                'data' => 'لم يتم تحديث البيانات'
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * This function is created to  manger update task.
     * @param array $data
     * @param int $id
     * @return array(data,status,message)
     */

    public function managerUpdateTask($data, $id)
    {
        try {
            $task = Task::find($id);
            // Check if the task exists
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'status' => 404,
                    'data' => 'لم يتم عرض البيانات'
                ];
            }
            // Check if the role of user to allow him to change the information of task and he added the task
            if (Auth::user()->role == 'admin' || Auth::user()->id == $task->assigned_by) {
                if (isset($data['status']) || isset($data['rating'])) {
                    return [
                        'message' => 'لا يحق لك هذا التغيير',
                        'status' => 403,
                        'data' => 'لم يتم عرض البيانات'
                    ];
                } else {
                    // Update the data
                    $task->update([
                        'title' => $data['title'] ?? $task->title,
                        'description' => $data['description'] ?? $task->description,
                        'priority' => $data['priority'] ?? $task->priority,
                        'due_date' => $data['due_date'] ?? $task->due_date,
                        'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
                    ]);

                    return [
                        'message' => 'تمت عملية التحديث بنجاح',
                        'status' => 200,
                        'data' => $task
                    ];
                }
            } else {
                return [
                    'message' => 'لا يحق لك القيام بهذه العملية',
                    'status' => 403,
                    'data' => 'لم يتم عرض البيانات'
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in updating task: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء التحديث',
                'status' => 500,
                'data' => 'لم يتم تحديث البيانات'
            ];
        }
    }

    //**________________________________________________________________________________________________
    /**
     **This function is created to delete an existing task.
     * *@param int $id
     ** @return array(status,message)
     */
    public function deleteTask($id)
    {
        try {
            $task = Task::find($id);
            // Check if the task exists
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'status' => 404,
                ];
            }
            // Check if the task was added by the same person who wants to delete it or if the user is an admin
            if (Auth::user()->id == $task->assigned_by || Auth::user()->role == 'admin') {
                // Delete the task
                $task->delete();

                return [
                    'message' => 'تمت عملية الحذف بنجاح',
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'لا يحق لك حذف هذه المهمة',
                    'status' => 403,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in deleting task: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء الحذف',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________

    /**
     * *This function is created to show an existing task.
     * *@param int $id
     ** @return array(data,status,message)
     */
    public function showTask($id)
    {
        try {
            $task = Task::find($id);

            // Check if the task exists
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'data' => 'لا يوجد بيانات',
                    'status' => 404,
                ];
            }
            return [
                'message' => 'تمت عملية العرض بنجاح',
                'data' => $task,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('Error in showing task: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء العرض',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * *This function is created to task to user
     * *@param int $id(id of task)
     * *@param int $assign(id of user)
     * *@return array(data,status,message)
     */
    public function assignTask($id, $assign)
    {
        try {
            $task = Task::find($id);
            //check if task exists
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'data' => 'لايوجد بيانات',
                    'status' => 402,
                ];
                //check if the person who creat tas; he want to assign the task
            } else {
                if (Auth::user()->id == $task->assigned_by || Auth::user()->role == 'admin') {
                    $task->update([
                        'assign_to' => $assign
                    ]);
                    return [
                        'message' => 'تم تعين المهمة',
                        'data' => $task,
                        'status' => 402,
                    ];
                } else {
                    return [
                        'message' => '  لم تقم بانشاء المهمة',
                        'data' => ' لا يوحد بيانات',
                        'status' => 402,
                    ];
                }
            }
        } catch (Exception $e) {
            Log::error('Error in assige tasks: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء اسناد المهمة',
                'status' => 500,
            ];
        }
    }

    //**________________________________________________________________________________________________
    /**
     * *This function is creat to return task
     * *@param $id
     **@return array(data,message,status)
     */
    public function returnTask($id)
    {
        try {
            $task = Task::withTrashed()->find($id);
            if ($task) {
                if ($task->assigned_by == Auth::user()->id || Auth::user()->role == 'admin') {
                    $task->restore();
                    return
                        [
                            'message' => 'تم اعاد المهمة بنجاح',
                            'data' => $task,
                            'status' => 200,
                        ];
                } else {
                    return [
                        'message' => '   لم تقم انت بانشاء المهمة لتعيدها',
                        'data' => ' لا يوحد بيانات',
                        'status' => 402,
                    ];
                }
            } else {
                return [
                    'message' => ' لايوحد مهمة',
                    'data' => 'لايوجد بيانات',
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in return task tasks: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء اسناد المهمة',
                'status' => 500,
            ];
        }
    }

    //**________________________________________________________________________________________________

    public function RatingUserTask($id, $rating)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'status' => 402,
                ];
            } else {
                // check if who creat tase he want to rating the task
                if ($task->assigned_by == Auth::user()->id) {
                    // check if the task finsh or failed
                    if ($task->status == 'done' || $task->status == 'failed') {
                        $task->Rating = $rating['rating'];
                        $task->save(); // Don't forget to save the task
                        return [
                            'message' => 'تم التقييم',
                            'status' => 200,
                        ];
                    } else {
                        return [
                            'message' => 'لا يمكنك التقييم الآن',
                            'status' => 402,
                        ];
                    }
                } else {
                    return [
                        'message' => '   لم تقم انت بانشاء المهمة لتقيمها ',
                        'data' => ' لا يوحد بيانات',
                        'status' => 402,
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'message' => 'حدث خطأ ما',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________


    public function show_his_task()
    {
        try {
            $user = User::find(Auth::user()->id);
            $tasks = $user->assignedTasks()->get();

            return [
                'message' => 'تم عرض التاسكات الخاصة بك',
                'data' => $tasks,
                'status' => 200,
            ];

        } catch (\Exception $e) {
            return [
                'message' => 'حدث خطأ ما أثناء عرض التاسكات',
                'data'=>[],
                'status' => 500,
            ];
        }
    }


    //**________________________________________________________________________________________________
    /**
 * This function is created to show deleted tasks.
 *
 * @param none
 * @return array ($message, $status, $data)
 */
    public function showdeletedTask()
    {
        try {
            $tasks = Task::onlyTrashed()->get();

            return [
                'message' => 'بيانات المهام المحذوفة',
                'data' => $tasks,
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Error in show task: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء العرض: ' . $e->getMessage(),
                'status' => 500,
                'data' => 'لا يوجد بيانات',
            ];
        }
    }
    /**
         /**
 * This function is created to permanently delete a task.
 *
 * @param int $id
 * @return array
 */
    public function finallyDeleteTask($id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'status' => 404,
                ];
            }

            $task->forceDelete();

            return [
                'message' => 'تم الحذف نهائيا',
                'status' => 200,
            ];
        } catch (\Exception $e) {
            Log::error('Error in finallyDeleteTask: ' . $e->getMessage());

            return [
                'message' => 'حدث خطأ أثناء الحذف',
                'status' => 500,
                'data' => 'لا يوجد بيانات',
            ];
        }
    }


}
