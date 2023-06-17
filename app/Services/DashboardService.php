<?php
    namespace App\Services;

    use Exception;
    use App\Models\Drug;
    use App\Models\User;
    use App\Models\Patient;
    use App\Models\MedicalRecord;

    class DashboardService {

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

                $countPatient     = Patient::count();
                $countDoctor      = User::where('role', 'doctor')->count();
                $countAdmin       = User::where('role', 'admin')->count();
                
                $patients         = Patient::select('id', 'name', 'gender')
                                            ->orderBy('id', 'desc')
                                            ->limit(5)
                                            ->get();

                $drugs            = Drug::select('id', 'name', 'stock')
                                            ->orderBy('id', 'desc')
                                            ->limit(5)
                                            ->get();

                $medical_records  = MedicalRecord::select('id', 'patient_id', 'complaint', 'doctor_id', 'diagnose')
                                            ->orderBy('id', 'desc')
                                            ->limit(5)
                                            ->get();

                $medical_records = $medical_records->map(function($m){
                    return [
                        'id'        => $m->id,
                        'patient'   => $m->patient->name,
                        'doctor'    => $m->doctor->name,
                        'complaint' => $m->complaint,
                        'diagnose'  => $m->diagnose,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data'    => [
                        'count_patient'    => $countPatient,
                        'count_doctor'     => $countDoctor,
                        'count_admin'      => $countAdmin,
                        'patients'         => $patients,
                        'drugs'            => $drugs,
                        'medical_records'  => $medical_records,
                    ],
                ], 200);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }
    }
?>