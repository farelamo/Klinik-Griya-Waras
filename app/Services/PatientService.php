<?php
    namespace App\Services;

    use Exception;
    use App\Models\Patient;
    use Illuminate\Http\Request;
    use App\Http\Requests\PatientRequest;
    use Illuminate\Support\Facades\Auth;
    use App\Http\Resources\BaseResource;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\ValidationException;
    use App\Http\Resources\Patient\PatientCollection;

    class PatientService {

        public function returnCondition($condition, $errorCode, $message)
        {
            return response()->json([
                'success' => $condition,
                'message' => $message,
            ], $errorCode);
        }

        public function index()
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

                $this->checkIdentifier($request);

                Patient::create([
                    'name'       => $request->name,
                    'gender'     => $request->gender,
                    'birth'      => $request->birth,
                    'address'    => $request->address,
                    'phone'      => $request->phone,
                    'identifier' => $request->identifier,
                ]);

                return $this->returnCondition(true, 200, 'Successfully create data ' .  $request->name);
            }catch (ValidationException $th) {
                return $th->validator->errors();
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

                if($request->identifier) : 
                    
                    $this->checkIdentifier($request);
                    $updateData['identifier'] = $request->identifier;
                endif;

                $data->update($updateData);

                return $this->returnCondition(true, 200, 'Successfully update data ' .  $data->name);
            }catch (ValidationException $th) {
                return $th->validator->errors();
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }

        public function destroy($id)
        {
            try {

                $data = Patient::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 400, 'data with id ' . $id . ' not found');

                $data->delete();

                return $this->returnCondition(true, 200, 'Successfully delete data ' .  $data->name);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Service Error');
            }
        }
    }
?>