<?php
    namespace App\Services;

    use Exception;
    use App\Models\Drug;
    use App\Http\Requests\DrugRequest;
    use App\Http\Resources\BaseResource;
    use App\Http\Resources\Drug\DrugCollection;

    class DrugService {

        public function internalServerError()
        {
            return response()->json([
                'success' => false,
                'message' => 'Internal Service Error'
            ], 500);
        }

        public function notFound($id)
        {
            return response()->json([
                'success' => false,
                'message' => 'Data with id ' . $id . ' not found' 
            ], 404);
        }

        public function success($message)
        {
            return response()->json([
                'success' => true,
                'message' => $message
            ], 200);
        }

        public function index()
        {
            try {

                $drugs = Drug::select('id', 'name', 'description', 'stock')->paginate(5);
                
                return new DrugCollection($drugs);
            }catch(Exception $e){
                return $this->internalServerError();
            }
        }

        public function show($id)
        {
            try {

                $drug = Drug::where('id', $id)->first();
                if(!$drug) return $this->notFound($id);
                
                return new BaseResource($drug);
            }catch(Exception $e){
                return $this->internalServerError();
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
                
                return $this->success('Successfully create data ' .  $data->name);
            }catch(Exception $e){
                return $this->internalServerError();
            }
        }

        public function update($id, DrugRequest $request)
        {
            try {

                $data = Drug::where('id', $id)->first();
                if(!$data) return $this->notFound($id);
                
                $data->update([
                    'name'        => $request->name,
                    'description' => $request->description,
                    'stock'       => $request->stock
                ]);

                return $this->success('Successfully update data ' .  $data->name);
            }catch(Exception $e){
                return $this->internalServerError();
            }
        }

        public function destroy($id)
        {
            try {
                $data = Drug::where('id', $id)->first();
                if(!$data) return $this->notFound($id);
                
                $data->delete();

                return $this->success('Successfully delete data ' .  $data->name);
            }catch(Exception $e){
                return $this->internalServerError();
            }
        }
    }
?>