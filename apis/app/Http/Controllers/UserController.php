<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getall(){
        $data = User::all();
        return response($data);
    }
    public function getbyid($id){
        $data = User::where('id',$id)->withCount('posts', 'followers', 'following', 'followed')->first();
        return response ($data);
    }
    public function save(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $model = new User();
        $model->name = $request->input('name');
        $model->email = $request->input('email');
        $model->password = md5($request->input('password'));
        $model->avatar = Null;
        $model->save();
    
        $data = array(
            'status' => 'success',
            'message' => 'Berhasil Menambah Data',
            'data' => $model
        );
        return response($data);
    }

    public function login(Request $request){

        $data = User::select('id', 'name', 'email', 'avatar')->where('email', $request->input('email'))->where('password', md5($request->input('password')))->get();

        if($data->count() > 0){
            $r = array(
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => $data->first()
            );
        }else{
            $r = array(
                'status' => 'failed',
                'message' => 'Login gagal, username/password invalid',
                'data' => false
            );
        }

        return response ($r);
    }

    public function update(Request $request, $id){
        $model = User::where('id', $id)->first();

        if(!empty($request->input('name'))){
            $model->name = $request->input('name');
        }
        
        if(!empty($request->input('email'))){
            $model->email = $request->input('email');
        }
        
        if(!empty($request->input('password'))){
            $model->password = md5($request->input('password'));
        }

        if(!empty($request->input('file'))){
            $image_base64 = base64_decode($request->input('file'));
            $path = "uploads/" . $request->input('file_name');
            file_put_contents($path, $image_base64);
            $model->avatar = $path;
        }

        $model->save();
    
        $data = array(
            'status' => 'success',
            'message' => 'Berhasil Merubah Data',
            'data' => $model
        );
        return response($data);
    }
    
    public function delete($id){
        $data = User::where('id',$id)->first();
        $data->delete();
    
        return response('Berhasil Menghapus Data');
    }
}