<?php
    namespace App\Services;

    use Exception;
    use App\Models\TypeConcoction;
    use App\Http\Resources\BaseResource;
    use App\Http\Requests\TypeConcoctionRequest;
    use App\Http\Resources\TypeConcoction\TypeConcoctionCollection;

    class TypeConcoctionService {

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

                $typeConcoctions = TypeConcoction::select('id', 'name')->paginate(5);
                
                return new TypeConcoctionCollection($typeConcoctions);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function show($id)
        {
            try {

                $typeConcoction = TypeConcoction::where('id', $id)->first();
                if(!$typeConcoction) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
                
                return new BaseResource($typeConcoction);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function store(TypeConcoctionRequest $request)
        {
            try {

                $data = TypeConcoction::create(['name' => $request->name]);
                
                return $this->returnCondition(true, 200, 'Successfully create data ' .  $data->name);
            }catch(Exception $e){
               return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function update($id, TypeConcoctionRequest $request)
        {
            try {

                $data = TypeConcoction::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
                
                $data->update(['name' => $request->name]);

                return $this->returnCondition(true, 200, 'Successfully update data ' .  $data->name);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }

        public function destroy($id)
        {
            try {
                $data = TypeConcoction::where('id', $id)->first();
                if(!$data) return $this->returnCondition(false, 404, 'data with id ' . $id . ' not found');
                
                $data->delete();

                return $this->returnCondition(true, 200, 'Successfully delete data ' .  $data->name);
            }catch(Exception $e){
                return $this->returnCondition(false, 500, 'Internal Server Error');
            }
        }
    }
?>