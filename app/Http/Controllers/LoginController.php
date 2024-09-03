<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\alert;

class LoginController extends Controller
{
    public function index(){
        return view ('login');
    }

    public function register(){
        return view('register');
    }

    public function authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
            
        ]);

        if($validator->passes()){

            if(Auth::attempt([
                'email' => $request->email, 
                'password'=> $request->password ])){
                    return redirect()->route('account.dashboard');
                }else{
                    return redirect()->route('account.login')->with('error','Either email or password is incorrect.');
                }

        }else{
            return redirect()->route('account.login')->withInput()->withErrors($validator);
        }

    }

    public function processRegister(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:3',
            'password_confirmation' => 'required'
        
        ]);

        if($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 'employee';
            $user->save();
            
            return redirect()->route('account.login')->with('succes', 'You have registered successfully');

        }else{
            return redirect()->route('account.register')
            ->withInput()
            ->withErrors($validator);
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }

}
