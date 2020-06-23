<?php

namespace App\Http\Controllers;

use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Psr\Http\Message\StreamInterface;

class AuthController extends Controller
{
    /**
     * @return Application|Factory|RedirectResponse|View
     */
    public function showLogin()
    {
        if (!Auth::check()){
            return view('login');
        }
        return redirect()->back();
    }

    /**
     * @return Application|Factory|RedirectResponse|View
     */
    public function showRegister()
    {
        if (!Auth::check()){
            return view('register');
        }
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response|StreamInterface
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }
        $data = [
            'email' => $request->email,
            'password'  =>  $request->password,
        ];
        if(Auth::attempt($data))
        {
            $http = new \GuzzleHttp\Client;
            try {
                $response = $http->post(config('services.passport.login_endpoint'), [
                    'form_params' => [
                        'grant_type' => 'password',
                        'client_id' => config('services.passport.client_id'),
                        'client_secret' => config('services.passport.client_secret'),
                        'username' => $request->email,
                        'password'  =>  $request->password,
                    ]
                ]);

                $access_token = json_decode((string) $response->getBody(), true)['access_token'];

                if (!session()->has('access_token')){
                    session()->put('access_token', $access_token);
                }
                else{
                    session()->remove('access_token');
                }
                return $response->getBody();
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                Auth::logout();
                if ($e->getCode() === 400) {
                    return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
                } else if ($e->getCode() === 401) {
                    return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
                }
                return response()->json('Something went wrong on the server.', $e->getCode());
            }
        }
        else
        {
           return response([
                'unauthorized' => "Unauthorized"
            ]);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->sendEmailVerificationNotification();

        return $user;
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        Auth::logout();
        return redirect()->route('showLogin');
    }
}
