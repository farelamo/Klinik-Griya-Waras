<?php
    namespace App\Services;

    use DB;
    use Exception;
    use Carbon\Carbon;
    use App\Models\MedicalRecord;
    use App\Services\UserService;
    use App\Services\DrugService;
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

        public function checkNormalDrug($request)
        {
            $rules = [
                'normal_drugs'            => 'required|array',
                'normal_drugs.*.id'       => 'required|exists:drugs,id',
                'normal_drugs.*.amount'   => 'required|numeric',
                'normal_drugs.*.times'    => 'required|numeric',
                'normal_drugs.*.dd'       => 'required|numeric',
            ];

            Validator::make($request->all(), $rules, $messages = 
            [
                'normal_drugs.required'         => 'normal drugs must be filled',
                'normal_drugs.array'            => 'normal drugs must be type of array',
                'normal_drugs.*.id.required'    => 'normal drugs id must be filled',
                'normal_drugs.*.id.exists'      => "normal drugs id doesn't exist",
                'normal_drugs.*.times.required' => 'normal drugs times must be filled',
                'normal_drugs.*.times.numeric'  => 'normal drugs times must be numeric',
                'normal_drugs.*.dd.required'    => 'normal drugs dd must be filled',
                'normal_drugs.*.dd.exists'      => "normal drugs dd must be numeric",
            ])->validate();
        }

        public function checkMixDrug($request)
        {
            $rules = [
                'mix_drugs'                       => 'required|array',
                'mix_drugs.*.id'                  => 'required|exists:drugs,id',
                'mix_drugs.*.amount'              => 'required|numeric',
                'mix_drugs.*.times'               => 'required|numeric',
                'mix_drugs.*.dd'                  => 'required|numeric',
                'mix_drugs.*.type_concoction_id'  => 'required|exists:type_concoctions,id',
            ];

            Validator::make($request->all(), $rules, $messages = 
            [
                'mix_drugs.array'                         => 'mix drugs must be type of array',
                'mix_drugs.required'                      => 'mix drugs must be filled',
                'mix_drugs.*.id.required'                 => 'mix drugs id must be filled',
                'mix_drugs.*.id.exists'                   => "mix drugs id doesn't exist",
                'mix_drugs.*.times.required'              => 'mix drugs times must be filled',
                'mix_drugs.*.times.numeric'               => 'mix drugs times must be numeric',
                'mix_drugs.*.dd.required'                 => 'mix drugs dd must be filled',
                'mix_drugs.*.dd.exists'                   => "mix drugs dd must be numeric",
                'mix_drugs.*.type_concoction_id.required' => 'mix drugs type concoction must be filled',
                'mix_drugs.*.type_concoction_id.exists'   => "mix drugs type concoction doesn't exist",
            ])->validate();
        }

        public function index()
        {
            try {

                $medical_records = MedicalRecord::select(
                        'id', 'patient_id', 'complaint', 'doctor_id', 
                        'diagnose', 'created_at',
                    ) ->paginate(5);

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

        public function handleQueryDrug($drug, $oldAmount, $recentStock, &$cases, &$params, &$ids)
        {
            $cases[]   = "WHEN {$drug['id']} then ?";
            $params[]  = ($recentStock + $oldAmount) - $drug['amount'];
            $ids[]     = $drug['id'];
        }

        public function handleResultDrug(&$result, $drug)
        {
            $id = $drug['id'];
            array_shift($drug);
            
            return $result['attach'][$id] = $drug;
        }

        public function handleDrugs($request, $data = null, $updateNormal = false)
        {
            $result          = [];
            
            $cases           = [];
            $params          = [];
            $ids             = [];
            
            $oldNormal = $updateNormal ? $data->normal_drugs->map(function ($n){
                            return $n->id;
                        })->toArray()
                        : 0;
                        
            $requestId = $updateNormal ? array_column($request, 'id') : 0;
            $detachOld = $updateNormal ? array_diff($oldNormal, $requestId) : [];
            $attachNew = $updateNormal ? array_diff($requestId, $oldNormal) : [];

            if($updateNormal):
                if(!empty($detachOld)):
                        
                    $result['detach'] = [];
                    
                    foreach ($detachOld as $id) {
                        
                        $recentStock = $this->drugService->show($id)->stock;
                        
                        $drug      = ['id' => $id,'amount' => 0];
                        $oldAmount = $data->normal_drugs()->wherePivot('drug_id', $id)->first()->pivot->amount;
                        
                        $this->handleQueryDrug($drug, $oldAmount, $recentStock, $cases, $params, $ids);
                        array_push($result['detach'], $id);
                    }
                endif;
            endif;
            
            foreach($request as $drug){
                
                $recentStock      = $this->drugService->show($drug['id'])->stock;
                
                if($updateNormal):

                    if(in_array($drug['id'], $attachNew)) :
                        
                        $this->handleQueryDrug($drug, 0, $recentStock, $cases, $params, $ids);
                        $this->handleResultDrug($result, $drug);

                        continue;
                    endif;
                    
                    $oldAmount = $data->normal_drugs()->wherePivot('drug_id', $drug['id'])->first()->pivot->amount;
                    $this->handleQueryDrug($drug, $oldAmount, $recentStock, $cases, $params, $ids);
                    $data->normal_drugs()->updateExistingPivot($drug['id'], ['amount' => $drug['amount']]);
                endif;

                if(!$updateNormal):
                    
                    $this->handleQueryDrug($drug, 0, $recentStock, $cases, $params, $ids);
                    $this->handleResultDrug($result, $drug);
                endif;
            }

            $ids   = implode(',', $ids);
            $cases = implode(' ', $cases);
            
            if (!empty($ids)) {
                \DB::update("UPDATE drugs SET `stock` = CASE `id` {$cases} END WHERE `id` in ({$ids})", $params);
            }
            
            return $result;
        }

        public function store(MedicalRecordRequest $request)
        {
            try {

                DB::beginTransaction();
                
                $this->checkRole($request);
                
                if($request->normal_drugs)
                    $this->checkNormalDrug($request);
                
                if($request->mix_drugs)
                    $this->checkMixDrug($request);
                
                $data = MedicalRecord::create([
                    'patient_id' => $request->patient_id,
                    'doctor_id'  => auth()->user()->id,
                    'complaint'  => $request->complaint,
                    'diagnose'   => $request->diagnose,
                    'pharmacist' => 0
                ]);

                if($request->mix_drugs)
                    $data->mix_drugs()->attach($this->handleDrugs($request->mix_drugs, null)['attach']);
                
                if($request->normal_drugs)
                    $data->normal_drugs()->attach($this->handleDrugs($request->normal_drugs, null)['attach']);
                
                DB::commit();
                return $this->returnCondition(true, 200, 'Successfully create data');
            }catch (ValidationException $th) {
                DB::rollback();
                return $th->validator->errors();
            }catch(Exception $e){
                DB::rollback();
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function update($id, MedicalRecordRequest $request)
        {
            try {
                
                DB::beginTransaction();
                
                if($request->normal_drugs)
                    $this->checkNormalDrug($request);
                
                if($request->mix_drugs)
                    $this->checkMixDrug($request);
                    
                $data = MedicalRecord::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
                
                $data->update([
                    'complaint' => $request->complaint,
                    'diagnose'  => $request->diagnose,
                ]);

                
                if($request->normal_drugs) :
                    
                    $result = $this->handleDrugs($request->normal_drugs, $data, true);       
                    if(array_key_exists('attach', $result)) $data->normal_drugs()->attach($result['attach']);  
                    if(array_key_exists('detach', $result)) $data->normal_drugs()->detach($result['detach']);        
                endif;
                
                if($request->mix_drugs) :
                
                    $result = $this->handleDrugs($request->mix_drugs, null);
                    if(array_key_exists('attach', $result)) $data->mix_drugs()->sync($result['attach']);        
                endif;
                
                DB::commit();
                return $this->returnCondition(true, 200, 'Successfully update data ' .  $data->id);
            }catch (ValidationException $th) {
                DB::rollback();
                return $th->validator->errors();
            }catch(Exception $e){
                DB::rollback();
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function destroy($id)
        {
            try {

                DB::beginTransaction();
                
                $data = MedicalRecord::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');

                $data->delete();

                DB::commit();
                return $this->returnCondition(true, 200, 'Successfully delete data ' .  $data->id);
            }catch(Exception $e){
                DB::rollback();
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