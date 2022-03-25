<?php 

namespace App\Repositories;

use App\Models\AdminUser;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Activations\EloquentActivation as Activation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthBackendRepository {
    
    public function loginPost($email, $password) {
        try {
            $authenticate = [
                "email"     => $email,
                "password"  => $password  
            ];

            $adminUsers = AdminUser::where('email', $authenticate['email'])
                ->first();

            if($adminUsers) {
                $adminUsers = Sentinel::authenticate($authenticate);
                if($adminUsers) {
                    return returnCustom("", true); 
                }
                return returnCustom("Authenticate Failed!");
            }

            // for first login
            $response = (new \GuzzleHttp\Client())->post(env("IMPORTIRCOM_API") .  "api/login-as-admin" , [
                'headers'   => [
                    'content-type'  => 'application/json'
                ],
                'json'      => $authenticate
            ]);
            $response = json_decode($response->getBody()->getContents(), true);
            if(!$response['status']) {
                return returnCustom("Err(R) ABR-LP : ". $response['message']);
            }

            $adminUser = $response['message'];

            return $this->createNewAccountAdmin([
                "email"         => $adminUser['email'],
                "password"      => $adminUser['password'],
                "first_name"    => $adminUser['first_name'],
                "last_name"     => $adminUser['last_name'],
                "phone"         => $adminUser['phone']
            ]);
        } catch (\Exception $e) {
            return returnCustom("Err(e) ABR-LP : " . $e->getMessage());
        }
    }

    public function createNewAccountAdmin($params) {
        try {
            $validator = \Validator::make($params, [
                "email"         => "required",
                "first_name"    => "required",
                "last_name"     => "required",
                "phone"         => "required",
                "password"      => "required"
            ]);

            if($validator->fails()) {
                return returnCustom("Err ABR-CNAA (1) : " . implode(" - " , $validator->messages()->all()));
            }

            DB::beginTransaction();
                $adminUser             = new AdminUser();
                $adminUser->email      = $params['email'];
                $adminUser->password   = $params['password'];
                $adminUser->first_name = $params['first_name'];
                $adminUser->last_name  = $params['last_name'];
                $adminUser->phone      = $params['phone'];
                $adminUser->save();

                $activate = new Activation();
                $activate->user_id      = $adminUser['id'];
                $activate->code         = md5(date("Y-m-d H:i:s"));
                $activate->completed    = 1;
                $activate->completed_at = date("Y-m-d H:i:s");
                $activate->save();

            DB::commit();
            
            Sentinel::authenticate($adminUser);
            return returnCustom("", true);
        } catch (\Exception $e) {
            return returnCustom("Err(e) ABR-CNAA : " . $e->getMessage());
        }
    }
}