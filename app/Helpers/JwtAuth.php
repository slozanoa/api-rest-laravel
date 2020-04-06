<?php

namespace App\Helpers;
use Firebase\JWT\JWT;
//consultar a la base dedatos
use Illuminate\Support\Facades\DB;
use App\User;
class JwtAuth{
    
    public $key;
    public function __construct() {
        $this->key= 'clave secreta-2020';
    }

        public function signup($email, $password, $getToken=null){
      //Buscar en la base de datos
      $user = User::where([
          'email' => $email,
          'password'=>$password
      ])->first();      
       //comprobar si son correctos
      $signup=false;
       if(is_object($user)){
          $signup=true;
     }
       if($signup){
           $token = array(
               'sub'=>$user->id,
               'email'=>$user->email,
               'name'=>$user->name,
               'surname'=>$user->surname,
               'description'=>$user->description,
               'role' => $user->role,
               'image' => $user->image,
               'iat' => time(),
               'exp' => time()+(7*24*60*60)
               );
             //general el token 
           $jwt = JWT::encode($token, $this->key, 'HS256');
           $decode = JWT::decode($jwt, $this->key, ['HS256']);
           
           if(is_null($getToken)){
               $data=$jwt;
           }else{
               $data=$decode;
           }
           
       }else{
           $data = array(
               'status' => 'error',
               'message' => 'login erroneo'
           );
       }
       //devolver el token decodificado o el token, en funcion del parametro
       return $data;
    }
    public function checkToken($jwt, $getIdentity=false){
        $auth=false;
        try {
            //limpiar las comillas
            $jwt = str_replace('"', '', $jwt);
            $decode = JWT::decode($jwt, $this->key, ['HS256']);
            
        } catch (\UnexpectedValueException $e) {
            $auth =false;
        } catch (\DomainException $e){
            $auth=false;
        }
        if(!empty($decode) && is_object($decode) && isset($decode->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        if($getIdentity){
            return $decode;
        }
        return $auth;
        
    }
}

