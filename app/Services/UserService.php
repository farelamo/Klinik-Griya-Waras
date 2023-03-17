<?php
    namespace App\Services;

    use Exception;
    use App\Models\User;
    use Illuminate\Http\Request;
    use App\Http\Requests\UserRequest;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Validator;
    use App\Http\Resources\User\UserResource;
    use App\Http\Resources\User\UserCollection;
    use Illuminate\Validation\ValidationException;

    class UserService {

        public function returnCondition($condition, $errorCode, $message)
        {
            return response()->json([
                'success' => $condition,
                'message' => $message,
            ], $errorCode);
        }

        public function checkAccess($request)
        {
            if(auth()->user()->role != 'superadmin' && auth()->user()->role != 'admin')
                return $this->returnCondition(false, 401, 'Invalid role access');

            if(auth()->user()->role == 'superadmin')
                if($request->role != 'admin')
                    return $this->returnCondition(false, 401, 'Invalid role access');

            if(auth()->user()->role == 'admin')
                if($request->role == 'admin')
                    return $this->returnCondition(false, 401, 'Invalid role access');
        }

        public function checkEmailAndPass($request, $action = true)
        {
            $emailRule    = $action ? 'required|' : '';
            $passwordRule = $action ? 'required|' : '';

            $rules = [
                'email'    => $emailRule . 'email|unique:users,email',
                'password' => $passwordRule . 'max:8'
            ];

            Validator::make($request->all(), $rules, $messages = 
            [
                'email.required'    => 'email must be filled',
                'email.email'       => 'invalid email format',
                'email.unique'      => 'email has already been taken',
                'password.required' => 'password must be filled',
                'password.max'      => 'maximal password is 8 character',
            ])->validate();
        }

        public function checkRole($request)
        {
            $rules = [
                'role' => 'required|in:admin,doctor,patient,pharmacist',
            ];

            Validator::make($request->all(), $rules, $messages = 
            [
                'role.required' => 'role must be filled',
                'role.in'       => "role doesn't exist",
            ])->validate();
        }

        public function checkPatientIdentifier($request)
        {
            $rules = [
                'identifier' => 'required|min:13|max:16',
            ];

            Validator::make($request->all(), $rules, $messages = 
            [
                'identifier.required' => 'identifier must be filled',
                'identifier.min'      => 'minimal identifier is 13 character',
                'identifier.max'      => 'maximal identifier is 16 character',
            ])->validate();
        }

        public function index(Request $request)
        {
            try {
                if(!$request->role)
                    return $this->returnCondition(false, 400, 'role params must be filled');

                $roles = ['admin', 'doctor', 'patient', 'pharmacist'];
                if(!in_array($request->role, $roles))
                    return $this->returnCondition(false, 404, 'Data with role ' . $request->role . ' not found');

                $users = User::select('id', 'name', 'gender', 'birth', 'address', 'phone', 'role')
                                ->where('role', $request->role)
                                ->paginate(5);

                return new UserCollection($users);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function show($id)
        {
            try {
                
                $user = User::select('id', 'name', 'gender', 'birth', 'address', 'phone', 'email', 'role')
                            ->where('id', $id)
                            ->first();

                if(!$user) return $this->returnCondition(false, 404, 'Data with id ' . $id . ' not found');
                
                if($user->role == 'patient') :
                    $user->makeHidden(['email']);
                endif;

                return new UserResource($user);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function store(UserRequest $request)
        {
            try {

                $this->checkRole($request);
                if ($this->checkAccess($request))
                    return $this->checkAccess($request);

                $createData = [
                    'name'       => $request->name,
                    'role'       => $request->role,
                    'gender'     => $request->gender,
                    'birth'      => $request->birth,
                    'address'    => $request->address,
                    'phone'      => $request->phone,
                ];

                if($request->role != 'patient') :
                    $this->checkEmailAndPass($request);

                    $createData['email']    = $request->email;
                    $createData['password'] = bcrypt($request->password);

                    $data = User::create($createData);

                endif;

                if($request->role == 'patient') :
                    $this->checkPatientIdentifier($request);

                    $createData['identifier'] = $request->identifier;

                    User::updateOrCreate(['identifier' => $request->identifier], $createData);

                endif;

                return $this->returnCondition(true, 200, 'Successfully create data ' .  $request->role);
            }catch (ValidationException $th) {
                return $th->validator->errors();
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function update($id, UserRequest $request)
        {
            try {

                $data = User::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'Data with id ' . $id . ' not found');

                if ($this->checkAccess($data))
                    return $this->checkAccess($data);

                if($data->role == 'patient')
                    return $this->returnCondition(false, 400, 'invalid url update patient data');

                if($request->email || $request->password)
                    $this->checkEmailAndPass($request, false);
                
                $updateData = [
                    'name'       => $request->name,
                    'gender'     => $request->gender,
                    'birth'      => $request->birth,
                    'address'    => $request->address,
                    'phone'      => $request->phone,
                ];

                if($request->email) $updateData['email'] = $request->email;
                if($request->password) $updateData['password'] = bcrypt($request->password);

                $data->update($updateData);

                return $this->returnCondition(true, 200, 'Successfully update data ' .  $data->role);
            }catch (ValidationException $th) {
                return $th->validator->errors();
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function destroy($id)
        {
            try {
                if (Auth::user()->id == $id)
                    return $this->returnCondition(false, 400, "Invalid, user can't remove them self");

                $data = User::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 400, 'data with id ' . $id . ' not found');

                if ($this->checkAccess($data))
                    return $this->checkAccess($data);

                $data->delete();

                return $this->returnCondition(true, 200, 'Successfully delete data ' .  $data->name);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }
    }
?>