<?php
    namespace App\Services;

    use Log;
    use Exception;
    use App\Models\MedicalRecord;
    use App\Services\UserService;
    use Illuminate\Support\Facades\Validator;
    use App\Http\Requests\MedicalRecordRequest;
    use Illuminate\Validation\ValidationException;
    use App\Http\Resources\MedicalRecord\MedicalRecordResource;
    use App\Http\Resources\MedicalRecord\MedicalRecordCollection;

    class MedicalRecordService {

        public function __construct(DrugService $drugService)
        {
            $this->drugService = $drugService;
        }

        public function returnCondition($condition, $errorCode, $message)
        {
            return response()->json([
                'success' => $condition,
                'message' => $message,
            ], $errorCode);
        }

        public function checkRole($request)
        {
            $rules = [
                'patient_id' => 'required|exists:patients,id',
            ];

            Validator::make($request->all(), $rules, $messages = 
            [
                'patient_id.required' => 'patient must be filled',
                'patient_id.exists'   => "patient doesn't exist",
            ])->validate();
        }

        public function index()
        {
            try {

                $medical_records = MedicalRecord::select(
                        'id', 'patient_id', 'complaint', 'doctor_id', 
                        'diagnose', 'drugs', 'created_at',
                    )->paginate(5);

                return new MedicalRecordCollection($medical_records);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function show($id)
        {
            try {

                $medical_record = MedicalRecord::where('id', $id)->first();
                if(!$medical_record) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');

                return new MedicalRecordResource($medical_record);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function store(MedicalRecordRequest $request)
        {
            try {
                
                $this->checkRole($request);
                
                if($request->normal_drugs)
                    $this->checkNormalDrug($request);
                
                if($request->mix_drugs)
                    $this->checkMixDrug($request);
                
                $request['drugs'] = json_encode($request->drugs);
                $data = MedicalRecord::create([
                    'patient_id' => $request->patient_id,
                    'doctor_id'  => auth()->user()->id,
                    'complaint'  => $request->complaint,
                    'diagnose'   => $request->diagnose,
                    'pharmacist' => 0
                ]);

                return $this->returnCondition(true, 200, 'Successfully create data');
            }catch (ValidationException $th) {
                return $th->validator->errors();
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function update($id, MedicalRecordRequest $request)
        {
            try {

                $data = MedicalRecord::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');

                $data->update([
                    'complaint' => $request->complaint,
                    'diagnose'  => $request->diagnose,
                    'drugs'     => $request->drugs,
                ]);

                return $this->returnCondition(true, 200, 'Successfully update data ' .  $data->id);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function destroy($id)
        {
            try {

                $data = MedicalRecord::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');

                $data->delete();

                return $this->returnCondition(true, 200, 'Successfully delete data ' .  $data->id);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function receipt()
        {
            try {
                
                $medical_records = MedicalRecord::select(
                    'id', 'patient_id', 'complaint', 'doctor_id', 
                    'diagnose', 'drugs', 'created_at',
                )
                ->where('pharmacist', false)
                ->whereRaw('date(created_at) = ?', [Carbon::now()->format('Y-m-d')])
                ->paginate(5);

                return new MedicalRecordCollection($medical_records);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function approvePharmacist($id)
        {
            try {

                $data = MedicalRecord::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');

                $data->update(['pharmacist' => true]);
                return $this->returnCondition(true, 200, 'Successfully approve data ' .  $data->id);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }
    }
?>