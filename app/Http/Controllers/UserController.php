<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\User;

class UserController extends Controller
{
    public function  prueba($name){
     
     return  "el nombre es: ".$name;
  }
  
  public function register(Request $request){
     //recoger los datos por post
      
      $json = $request->input('json', null);
      $params = json_decode($json);
      $params_array = json_decode($json,true);
      if(!empty($params) && !empty($params_array)){
          
          //limpiar los datos
           $params_array = array_map('trim', $params_array);
           
           //validar los datas
           $validate = \Validator::make($params_array,[
               'name'=> "required|alpha",
               'surname' => "required|alpha",
               'email' => "required|email|unique:users",
               'password'=> "required"
           ]);
           if($validate->fails()){
               $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha creado',
                'errors' => $validate->errors()
                );
           }else{
               
               //cifrar la contraseña
               $pwd = hash('sha256', $params->password);
               
               //creo el objeto users
               
              $user = new User();
              $user->name = $params_array['name'];
              $user->surname = $params_array['surname'];
              $user->email = $params_array['email'];
              $user->password = $pwd;
              $user->description = $params_array['description'];
              $user->role = $params_array['role'];
               $user->save();
               $data = array(
                   'status' => 'success',
                   'code' =>200,
                   'message' => 'el usuario se ha creado correctamente',
                   'user' =>$user
                   
               );
           }
      }else{
          $data= array(
             'status'=>'error',
             'code'=>404,
             'message' => 'El usuario no se ha creado correctamente'
          );
      }
          
      return response()->json($data, $data['code']);
  }
  
  public function login(Request $request){
      $jwtAuth = new \JwtAuth(); 
     //recoger datos por post
      $json = $request->input('json', null);
      $params = json_decode($json);
      $params_array = json_decode($json, true);
      
      
      
      if (!empty($params) && !empty($params_array)) {
            $validate = \Validator::make($params_array, [
                        'email' => "required|email",
                        'password' => "required"
            ]);
            if ($validate->fails()) {
                //la validacion falló
                $signup = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no está logueado',
                    'error' => $validate->errors()
                );
            } else {
                    
                $pwd = hash('sha256', $params->password);
                
                $signup = $jwtAuth->signup($params->email, $pwd);
                if (!empty($params->gettoken)) {
                    $signup = $jwtAuth->signup($params->email, $pwd, true);
                }
            }
        }
        return response()->json($signup,200);
  }
  
  public function update(Request $request){
      //comprobar si el usuario está identificado
      $token = $request->header('Authorization');
      $jwtAuth = new \JwtAuth();
      $checkToken = $jwtAuth->checkToken($token);
      //recoger los datos por post
      $json= $request->input('json',null);
      $params = json_decode($json);
      $params_array = json_decode($json,true);
      
      if($checkToken && !empty($params_array)){
          
        //cacar el usuario identificado
          $user = $jwtAuth->checkToken($token,true);
        //validar los datos
          $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        'email' => ['required', 'email', 'unique:users,email,'.$user->sub],
            ]);
          if($validate->fails()){
              $data= array(
                  'status' => 'error',
                  'code' => 400,
                  'message' => 'los datos no son correctoctos',
                  'fallo' => $validate->errors()
              );
              
          }else{
           //quitar los datos que no quiero actualizar
              unset($params_array['id']);
              unset($params_array['role']);
              unset($params_array['password']);
              unset($params_array['created_at']);
              //actualizar datos en la db
              
              $user_update = User::where('id',$user->sub)->update($params_array);
              $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array
              );
          }
      }else{
           $data= array(
                  'status' => 'error',
                  'code' => 400,
                  'message' => 'los datos no son correctoctos'
              );
      }
       
        //devolver array
      
      return response()->json($data, $data['code']);
     
  }
  public function upload(Request $request){
      //recoger datos de la peticion
      $image = $request->file('file0');
      
      //validar la imagen
      $validate = \Validator::make($request->all(),[
          'file0'=>'required|image|mimes:jpg,jpeg,png,gif'
      ]);
      //Guardar la imagen
      if(!$image && $validate->fails()){
          $data=array(
              'code' => 400,
              'status' =>'error',
              'message' => 'Error al subir imagen'
          );
      }else{
          $image_name = time().$image->getClientOriginalName();
          \Storage::disk('users')->put($image_name, \File::get($image));
          $data=array(
              'code' => 200,
              'status' =>'success',
              'image' => $image_name
          );
      }
      return response()->json($data,$data['code']);
  }
  public function getImage($filename){
      //ver si está la imagen en disco
      $isset=\Storage::disk('users')->exists($filename);
      if($isset){
          $file = \Storage::disk('users')->get($filename);
          return new Response($file,200);
      }else{
          $data = array(
              'code' => 400,
              'status' => 'error',
              'message' => 'la imagen no existe'
          );
      }
      return response()->json($data,$data['code']);
  }
  public function detail($id){
      //buscar usuario
      $user = User::find($id);
      if(is_object($user)){
          $data = array(
              'code' =>200,
              'status' => 'success',
              'user' =>$user
          );
      }else{
          $data = array(
              'code' =>400,
              'status' => 'error',
              'message' =>'El usuario no existe'
          );
      }
      
      return response()->json($data,$data['code']);
  }
  
}
