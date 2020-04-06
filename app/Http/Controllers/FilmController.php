<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Film;

class FilmController extends Controller
{
    public function __construct() {
        $this->middleware('api.auth',['except'=>[
            'index',
            'show',
            'getImage',
            'getFilmByCategory',
            'getFilmByUser'
            ]]);
    }
    public function index(){
        $films = Film::all();
        
        return response()->json([
                'status' => 'success',
                'code' => 200,
                'film'=> $films
           ]);
    }
    public function show($id){
        $film = Film::find($id)->load('category')
                               ->load('user');
        if($film){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'film' => $film
            );
        }else{
             $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'La pelicula no se encuentra'
            );
        }
        return response()->json($data, $data['code']);
    }
   private function getIdentity($request){
        $jwtAuth = new \JwtAuth();
        $token = $request->header('Authorization',null);
        $user = $jwtAuth->checkToken($token,true);
        
        return $user;
    }
    public function store(Request $request){
        //recoger datos por post
        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        if(!empty($params_array) && !empty($params)){
             //validar datos
             $validate = \Validator::make($params_array,[
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
             ]);
             if($validate->fails()){
                  $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La pelicula no se ha creado',
                    'errors' => $validate->errors()
            );
             }else{
                 $user_indentificado = $this->getIdentity($request);
                 //guardar datos en la bd
                 $film = new Film();
                 $film->user_id = $user_indentificado->sub;
                 $film->category_id = $params->category_id;
                 $film->title = $params->title;
                 $film->content = $params->content;
                 $film->image = $params->image;
                 $film->save();
                 //mostrar json
                  $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La pelicula se ha creado',
                    'film' => $params_array
                   );
             }
            
            
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No se enviaron los datos correctamente'
            );
        }
       
       return response()->json($data, $data['code']);
    }
    
    public function update($id,Request $request){
        //recoger datos por post
        $json= $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true);
        if(!empty($params) && !empty($params_array)){
            //validar los datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' =>'required',
                'category_id' => 'required'
            ]);
            if($validate->fails()){
                 $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'La pelicula no se ha actualizado',
                    'errors' => $validate->errors()
            );
            }else{
                
            //quitar los datos que no voy a actualizar
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_id']);
            unset($params_array['user']);
            //actualizar db
                //buscar usuario 
                $user = $this->getIdentity($request);
                $film = Film::where('id',$id)
                        ->where('user_id', $user->sub)
                        ->first();
                if(!empty($film) && is_object($film)){
                   
                    $update = $film->update($params_array);
                     $data = array(
                        'code'=> 200,
                        'status' => 'success',
                        'film' => $film,
                        'changes' => $params_array
                      );
                }else{
                     $data = array(
                         'code'=> 400,
                        'status' => 'error',
                        'message' => 'El la categoria no existe o el usuario no puede borrar'
                    );
                }
                 
            }
           
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No se enviaron los datos correctamente'
            );
        }
       //mostrar json 
        return response()->json($data, $data['code']);
    }
    public function destroy($id, Request $request){
        //sacar usuario identiicado
        $user = $this->getIdentity($request);
       
        //buscar pelicula a borrar
        $film = Film::where('id',$id)
                      ->where('user_id',$user->sub)
                      ->first();
        
         if(is_object($film)){
             $film->delete();
            $data = array(
                'code'=> 200,
                'status' => 'success',
                'film' => $film
            );
         }else{
             $data = array(
                'code'=> 400,
                'status' => 'error',
                'message' => 'El usuario no ha sido encontrado'
            );
         }
         return response()->json($data,$data['code']);
    }
    
    public function upload(Request $request){
        //recoger datos por post
        $image = $request->file('file0');
        
         $validate = \Validator::make($request->all(),[
           'file0' => 'required|image|mimes:jpg,jpeg,png,gif' 
        ]);
         if($validate->fails()){
             $data = array(
                 'status' => 'success',
                 'code' => 400,
                 'message' => 'El archivo no es valida'
             );
         }else{
             $image_name = time().$image->getClientOriginalName();
             \Storage::disk('images_films')->put($image_name, \File::get($image));
             $data= array(
                 'status' => 'error',
                 'code' => 200,
                 'image_name' => $image_name
            );        
         }
         return response()->json($data, $data['code']);
    }
    
    public function getImage($filename){
        //ver si la imagen está en el disco
        $isset =  \Storage::disk('images_films')->exists($filename);
        if($isset){
            $file = \Storage::disk('images_films')->get($filename);
            return new response($file, 200);
        }else{
            $data = array(
                'status'=>'error',
                'code' => 400,
                'message' =>'La imagen no se encuentra'
            );
        }
        return $data = response()->json($data,$data['code']);
    }
    
    public function getFilmByCategory($id){
        $films = Film::where('category_id', $id)->get();
       return response()->json([
            'status' => 'success',
            'films' =>$films
        ], 200);

    }
    public function getFilmByUser($id){
         $films = Film::where('user_id', $id)->get();
        
        return response()->json([
            'status' => 'success',
            'films' =>$films
        ], 200);
    }
}
