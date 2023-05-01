<?php
    namespace App\Services;

    use Exception;
    use App\Models\Patient;
    use Illuminate\Http\Request;
    use App\Http\Requests\PatientRequest;
    use Illuminate\Support\Facades\Auth;
    use App\Http\Resources\BaseResource;
    use Illuminate\Support\Facades\Validator;
    use App\Http\Resources\Patient\PatientCollection;

    class PatientService {

        public function returnCondition($condition, $errorCode, $message)
        {
            return response()->json([
                'success' => $condition,
                'message' => $message,
            ], $errorCode);
        }

        public function index(Request $request)
        {
            try {
                $patients = Patient::select('id', 'name', 'gender', 'birth', 'address', 'phone', 'identifier')
                                    ->paginate(5);

                return new PatientCollection($patients);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function show($id)
        {
            try {
                
                $patient = Patient::select('id', 'name', 'gender', 'birth', 'address', 'phone', 'identifier')
                                    ->where('id', $id)
                                    ->first();

                if(!$patient) return $this->returnCondition(false, 404, 'Data with id ' . $id . ' not found');

                return new BaseResource($patient);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function store(PatientRequest $request)
        {
            try {

                $createData = [
                    'name'       => $request->name,
                    'role'       => $request->role,
                    'gender'     => $request->gender,
                    'birth'      => $request->birth,
                    'address'    => $request->address,
                    'phone'      => $request->phone,
                ];
                
                $createData['identifier'] = $request->identifier;
                
                Patient::updateOrCreate(['identifier' => $request->identifier], $createData);

                return $this->returnCondition(true, 200, 'Successfully create data ' .  $request->role);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function update($id, PatientRequest $request)
        {
            try {

                $data = Patient::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'Data with id ' . $id . ' not found');
                
                $updateData = [
                    'name'       => $request->name,
                    'gender'     => $request->gender,
                    'birth'      => $request->birth,
                    'address'    => $request->address,
                    'phone'      => $request->phone,
                ];

                $data->update($updateData);

                return $this->returnCondition(true, 200, 'Successfully update data ' .  $data->role);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function destroy($id)
        {
            // try {

                $data = Patient::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 400, 'data with id ' . $id . ' not found');

                // $data->medical_records()->get()->each->mix_drugs()->delete();
                $data->delete();

                return $this->returnCondition(true, 200, 'Successfully delete data ' .  $data->name);
            // }catch(Exception $e){
            //     return $this->returnCondition(false, 500, 'Internal Service Error');
            // }
        }
    }
?>