<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Category;
use App\Film;

class PruebaController extends Controller
{
    public function prueba(){
        
//        $users = User::all();
//        foreach ($users as $user){
//            echo "<h1>Nombre: {$user->name}</h1>";
//            echo "<h1>Nombre: {$user->film->title}</h1>";
//            echo "<hr></hr>";
//        }
//        die();
//    }
    
//        $categories = Category::all();
//        foreach ($categories as $category){
//            
//            echo "<h1>{$category->name}</h1>";
//            $films = $category->films;
//            
//            foreach ($films as $film){
//                echo "<h3>{$film->title}</h3>";
//                $user = $film->user;
//                echo "{$user->name}";
//            }
//        }
        
        $films=Film::all();
        foreach ($films as $film){
            echo "<h1>{$film->title}</h1>";
            echo "<h3>{$film->category->name}</h2>";
            echo "<h3>{$film->user->name}</h2>";
            echo "<hr>";
                        
        }
        die();
    }
    
}
