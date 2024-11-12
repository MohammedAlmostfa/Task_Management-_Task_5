<?php

namespace App\Service;

use App\Models\User;

use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserService
{
    //**________________________________________________________________________________________________
    /**
     **This function is created to show all the user.
     ** @param$ dat(email, password,name,role)
     ** @return array(message,data[name],status)
     */
    public function showUsers()
    {
        try {
            //get the data
            $data = User::byRole('user')->get(['id', 'name']);
            //return data of user
            return [
                'message' => 'بيانات المستخدمين',
                'status' => 200,
                'data' => $data,
            ];
        } catch (Exception $e) {
            Log::error('Error in show all users  : ' . $e->getMessage());
            return [
                'message' => 'حدث خطاء اثنا عرض المستخدمسن',
                'status' => 500,
                'data' => 'لم يتم عرض البيانات'
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     **This function is created to store a new User.
     ** @param$ data email, password,name,role)
     ** @return array (message,data[name,email,role],status)
     */
    public function createUser($credentials)
    {
        try {
            $user = new User([
                'name' => $credentials['name'],
                'email' => $credentials['email'],
            ]);
            $user->password = Hash::make($credentials['password']);
            $user->role = 'user';
            $user->save();

            //return $user data
            return [
                'message' => 'نم انشاء الحساب',
                'status' => 200,
                'data' => [
                    'name' => $credentials['name'],
                    'email' => $credentials['email'],
                ],
            ];
        } catch (Exception $e) {
            Log::error('Error in creat user  : ' . $e->getMessage());
            return [
                'message' => 'حدث خطاء اثنا انشاء الحساب',
                'status' => 500,
                'data' => 'لم يتم عرض البيانات'
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * *This function is creat to update a  user.
     **  @param$ data (email, password,name,role)
     **  @param $id (id of user)
     * *@return array (message,data[name,email,role],status)
     */
    public function updateUser($data, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return [
                    'message' => 'المستخدم غير موجود',
                    'status' => 404,
                    'data' => 'لا يوجد بيانات'
                ];
            } else {
                //update user
                $user->update([
                    'name' => $data['name'] ?? $user->name,
                    'email' => $data['email'] ?? $user->email,
                ]);
                $user->password = $data['password'] ?? $user->password;
                $user->role = $data['role'] ?? $user->role;
                $user->save();

                return [
                    'message' => 'تمت عملية التحديث',
                    'status' => 200,
                    'data' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in updating user: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء التحديث',
                'status' => 500,
                'data' => 'لم يتم تحديث البيانات'
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * *This function is creat to delet  a user.
     **@param $id (id of user)
     * *@return  (status, data,message)
     */
    public function deletUser($id)
    {
        try {
            // find the user
            $user = User::find($id);
            if (!$user) {
                //if the user not exist
                return [
                    'message' => 'المستخدم غير موجود',
                    'status' => 404,
                ];
            } else {
                //delete the user
                $user->delete();
                return [
                    'message' => 'تمت عملية الحذف',
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in delet user: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء الحذف',
                'status' => 500,
            ];
        }
    }

    //**________________________________________________________________________________________________
    /**
     * *This function is creat to return task
     * *@param $id(id of user)
     **@return array(data,message,status)
     */

    public function returnUser($id)
    {
        try {
            $user = User::withTrashed()->find($id);
            if ($user) {
                $user->restore();
                return [
                    'message' => 'تم اعاد المستخدم بنجاح',
                    'data' => $user,
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => ' لايوحد مستخدم',
                    'data' => 'لايوجد بيانات',
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in returning user: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناءاعادة المستخدم',
                'status' => 500,
                'data' => 'لا يوجد بيانات'
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * *This function is creat to show  a user.
     **@param $id(id of user)
     * *@return ($message,status,data)
     */
    public function showUser($id)
    {

        try {
            //find the user
            $user = User::find($id);

            if (empty($user)) {
                //if the user not exist
                return [
                    'message' => 'المستخدم غير موجود',
                    'status' => 404,
                    'data' => 'لا يوجد بيانات'
                ];
            } else {
                // rturn uuser data
                return [
                    'message' => 'بيانات المستخدم',
                    'data' => $user,
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in show user s task: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء العرض',
                'status' => 500,
                'data' => 'لا يوجد بيانات'
            ];
        }
    }


    //**________________________________________________________________________________________________
    /**
     * *This function is creat to show  deleted  user.
     **@param nothing
     * *@return ($message,status,data)
     */

    public function showdeletedUser()
    {
        try {
            $users = User::onlyTrashed()->get();

            return [
                'message' => 'بيانات المستخدمين المحذوفين',
                'data' => $users,
                'status' => 200,
            ];
        } catch (\Exception $e) {
            Log::error('Error in show user: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء العرض',
                'status' => 500,
                'data' => 'لا يوجد بيانات',
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * *This function is creat to   deleted  user finally .
     **@param id
     * *@return ($message,status,)
     */


    public function finallyDelete($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->forceDelete();
                return [
                    'message' => 'تم الحذف نهائيا',
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'المستخدم غير موجود',
                    'status' => 404,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error in deleting user: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء الحذف',
                'status' => 500,
                'data' => 'لا يوجد بيانات',
            ];
        }
    }

}
