<?php
    namespace App\Services;

    use Exception;
    use App\Models\Drug;
    use App\Http\Requests\DrugRequest;
    use App\Http\Resources\BaseResource;
    use App\Http\Resources\Drug\DrugCollection;

    class DrugService {

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

                $drugs = Drug::select('id', 'name', 'description', 'stock')->paginate(5);
                
                return new DrugCollection($drugs);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function show($id)
        {
            try {

                $drug = Drug::where('id', $id)->first();
                if(!$drug) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
                
                return new BaseResource($drug);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function store(DrugRequest $request)
        {
            try {

                $data = Drug::create([
                    'name'        => $request->name,
                    'description' => $request->description,
                    'stock'       => $request->stock
                ]);
                
                return $this->returnCondition(true, 200, 'Successfully create data ' .  $data->name);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function update($id, DrugRequest $request)
        {
            try {

                $data = Drug::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
                
                $data->update([
                    'name'        => $request->name,
                    'description' => $request->description,
                    'stock'       => $request->stock
                ]);

                return $this->returnCondition(true, 200, 'Successfully create data ' .  $data->name);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function destroy($id)
        {
            try {
                $data = Drug::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
                
                $data->delete();

                return $this->returnCondition(true, 200, 'Successfully delete data ' .  $data->name);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }
    }
?>