<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    # function to create user
    public function createUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "name" => "required | string",
            "phone" => "required | numeric",
            "email" => "required | string | unique:users",
            "password" => "required | string | min:6",
        ]);

        if ($validator->fails()) {
            $result = array(
                "status" => "false",
                "message" => "Validation error occured!",
                'error_message' => $validator->errors()
            );
            return response()->json($result, 400); // Bad request
        }

        $user = User::create([
            "name" => $request->name,
            "phone" => $request->phone,
            "email" => $request->email,
            "password" => bcrypt($request->password),

        ]);

        if ($user->id) {
            $result = array(
                "status" => "true",
                "message" => "User Created!",
                "data" => $user
            );
            $responseCode = 200;
        } else {

            $result = array(
                "status" => "false",
                "message" => "Something went wrong!"
            );
            $responseCode = 400;
        }

        return response()->json($result, $responseCode);
    }

    # function to return all users
    public function getUsers()
    {
        try {
            $users = User::all();

            $result = array(
                'status' => 'true',
                'message' => count($users) . " user(s) fetched!",
                'data' => $users
            );
            $responseCode = 200; //Success

            return response()->json($result, $responseCode);
        } catch (Exception $e) {
            $result = array(
                'status' => 'false',
                'message' => "API failed due to an errors!",
                'error' => $e->getMessage()
            );
            return response()->json($result, 500);
        }
    }

    # function to get user detail
    public function getUserDetail($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'false',
                'message' =>  "User not Found!",
            ], 404);
        }

        $result = array(
            'status' => 'true',
            'message' =>  "User Found!",
            'data' => $user
        );
        $responseCode = 200; //Success

        return response()->json($result, $responseCode);
    }

    # function to update a user
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'false',
                'message' =>  "User not Found!",
            ], 404);
        }

        //Validation
        $validator = Validator::make($request->all(), [
            "name" => "required | string",
            "phone" => "required | numeric | digits:10",
            "email" => "required | string | unique:users,email,.$id",
        ]);

        if ($validator->fails()) {
            $result = array(
                "status" => "false",
                "message" => "Validation error occured!",
                'error_message' => $validator->errors()
            );
            return response()->json($result, 400); // Bad request
        }


        //Update Code
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->save();

        $result = array(
            "status" => "true",
            "message" => "User updated Successfully!",
            'data' => $user
        );

        return response()->json($result, 200);
    }

    # function to delete a user
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'false',
                'message' =>  "User not found!",
            ], 404);
        }

        $user->delete();
        $result = array(
            "status" => "true",
            "message" => "User has been deleted Successfully!",
            'data' => $user
        );

        return response()->json($result, 200);
    }
}
