<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{
    public function __construct() {
        $this->middleware('api.auth', ['except' =>['index', 'show']]);
    }
    
    public function index(){
        $categories = Category::all();
        
        return response()->json([
            'code' =>200,
            'status' =>'success',
            'categories'=>$categories
        ]);
    }
    
    public function show($id){
        $category = Category::find($id);
        if(is_object($category)){
            $data=array(
                'status' => 'success',
                'code'=> 200,
                'category' => $category
            );
        }else{
            $data=array(
                'status' => 'error',
                'code'=> 400,
                'message' => 'la categoria no existe'
            );
        }
        return response()->json($data,$data['code']);
    }
    public function store(Request $request){
        //recoger datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            //validar datos
            $validate = \Validator::make($params_array,[
                'name' => 'required|alpha'
            ]);
            if($validate->fails()){
                 $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'La categoria no se ha creado',
                 'errors' => $validate->errors()
            );
            }else{
                
                 //guardar datos en la db
                 $category = new Category();
                 $category->name = $params_array['name'];
                 $category->save();
                 
                //mostrar json
                 $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La categoria se ha creado correctamente',
                    'category' => $category
                );
            }
           
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'los datos no han sido enviados'
            );
        }
        return response()->json($data, $data['code']);
    }
    public function update(Request $request,$id){
        //Recoger datos por post
        $json = $request->input('json',null);
        $params= json_decode($json);
        $params_array= json_decode($json,true);
        
        
        if(!empty($params) && !empty($params_array)){
             //validar los datos
             $validate = \Validator::make($params_array,[
                 'name' => 'required|alpha'
             ]);
             if($validate->fails()){
                  $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La categoria no se ha creado',
                    'errors' => $validate->errors()
                  );
             }else{
                 //borrar datos que no quiero actualizar
                 unset($params_array['id']);
                 unset($params_array['created_at']);
                 
                //actualizar datos en la db
                $category_update = Category::where('id', $id)->update($params_array);
                //mostrar json
                 $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La categoria se ha creado correctamente',
                    'changes' => $params_array
                );
             }
            
        }else{
             $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'los datos no han sido enviados'
            );
        }
       
        //mostrar datos por en json
        return response()->json($data, $data['code']);        
    }
}
