<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Wallet;
use App\Models\Payments_model;

class UserController extends Controller
{
    public function saveUserBuyCredits(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'credits' => 'required',
                'card_holder'=>'required',
                'amount' => 'required',
                'payment_intent_id' => 'required',
                'customer_id' => 'required',
                'payment_method_id' => 'required',
                'user_id' => 'required',
        ]);
         if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        else{
             $user = User::where('id', $request->user_id)->first();
             if(!empty($user)){
                 
                // $credits = floatval($request->amount) / 1.5; 
                $credits = floatval($request->credits); 
                $user->total_credits=number_format(floatval($user->total_credits) + floatval(number_format($credits, 2)),2);
                $user->update();
                Payments_model::create(array(
                   'user_id'=> $user->id,
                   'amount'=>$request->amount,
                   'payment_intent_id'=>$request->payment_intent_id,
                   'customer_id'=>$request->customer_id,
                   'payment_method_id'=>$request->payment_method_id,
                   'card_holder'=>$request->card_holder,
                ));
                 return response()->json([
                    'message' => 'Successfully purchased!',
                    'status' => 1,
                ], 200);
             }
             else{
                return response()->json(['msg' => 'Invalid User!','status'=>0], 200);
             }
            
        }
    }
    public function register(Request $request)
    {
        if ($request->role == 'organizer') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|unique:users,email',
                'phone_number' => 'required|unique:users,phone_number',
                'password' => 'required|string',
                'confirm_password' => 'required|string|same:password',
                'terms_and_conditions' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|unique:users,email',
                'phone_number' => 'required|unique:users,phone_number',
                'password' => 'required|string',
                'confirm_password' => 'required|string|same:password',
                'terms_and_conditions' => 'required',
            ]);
        }
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        // creaet 6 digit toekn
        $token = mt_rand(100000, 999999);
        
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'org_name' => $request->org_name ?? null,
            'org_website' => $request->org_website ?? null,
            'org_mailing_address' => $request->org_mailing_address ?? null,
            'org_communication_method' => $request->org_communication_method ?? null,
            'org_timezone' => $request->org_timezone ?? null,
            'country' => $request->country ?? null,
            'city' => $request->city ?? null,
            'role' => $request->role ?? null,
            'status' => 'inactive',
            'postal_code' => $request->postal_code ?? null,
            'address' => $request->address ?? null,
            'password' => bcrypt($request->password),
            'verify_token' => $token,
        ]);
        if(!empty($request->name)){
            $nameArray = explode(" ", $request->name);
            $user['firstname']=$nameArray[0];
            $user['lastname']=count($nameArray) > 1 ? $nameArray[1] : "";
        }
        // send mail 
        $message = "Your verification code is: ".$token;
        $subject = "Verification Code";
        $headers = "From: Event Plus <info@playoffz.com>";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $headers .= "X-Priority: 1\r\n";
        $headers .= "X-MSMail-Priority: High\r\n\r\n";
        
        mail($request->email, $subject, $message, $headers);

        Wallet::create([
            'user_id' => $user->id,
            'payouts' => 0,
            'current_balance' => 0,
            'total_balance' => 0,
        ]);

        return response()->json([
            'message' => 'Registration successful, please verify your email',
            'user' => $user,
        ], 201);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'verify_token' => 'required|string',
        ]);
        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->where('verify_token', $request->verify_token)->first();
        if ($user) {
            $user->status = 'active';
            $user->verify_token = null;
            $user->save();
            return response()->json([
                'message' => 'User verified successfully',
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong. Please try again',
            ], 422);
        }
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'email or password is incorrect',
            ], 401);
        } else {
            $user = Auth::user();
            if($user->status == 'inactive') {
                return response()->json([
                    'message' => 'Your account is not verified yet',
                ], 401);
            }
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token,
            ], 200);
        }
    }
}
