<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\UserSocial;

use App\Http\Controllers\Auth\traitSlug;

use Socialite;

class AuthSocialiteController extends Controller
{

	use traitSlug;
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginRedirectToProvider($service)
    {
    	$redirect = config('services.redirect.'.$service.'.login');
    	config(['services.'.$service.'.redirect' => $redirect]);
    	// return config('services.'.$service.'.redirect');
        return Socialite::driver($service)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginHandleProviderCallback($service)
    {
    	$redirect = config('services.redirect.'.$service.'.login');
    	config(['services.'.$service.'.redirect' => $redirect]);

        $user = Socialite::driver($service)->user();

        $check = UserSocial::whereHas('user')->find($user->getId());
        if ($check) {
        	Auth::loginUsingId($check->user->id);
        	return redirect('/');
        } else {
        	$checkEmail = User::where('email', $user->getEmail())->first();
        	if(count($checkEmail)){
        		$input = [
        			'id' => $user->getId(),
        			'user_id' => $checkEmail->id,
        			'email' => $user->getEmail(),
        			'name' => $user->getName(),
        			'avatar' => $user->getAvatar()
        		];
        		$this->_regUserSocial($input);
        		\Auth::loginUsingId($checkEmail->id);
        		return redirect('/');
        	}
        	return redirect('login')->withErrors(['email'=>['Your account is not found. please register first.']]);
        }
    }

    private function _regUserSocial($input){
    	UserSocial::create($input);
    }
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function regRedirectToProvider($service)
    {
    	$redirect = config('services.redirect.'.$service.'.reg');
    	config(['services.'.$service.'.redirect' => $redirect]);

        return Socialite::driver($service)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function regHandleProviderCallback($service)
    {
    	$redirect = config('services.redirect.'.$service.'.reg');
    	config(['services.'.$service.'.redirect' => $redirect]);

        $user = Socialite::driver($service)->user();

        $checkEmail = User::where('email', $user->getEmail())->first();
        if(count($checkEmail)){
        	$userData = $checkEmail;
        } else {
	        $username = $this->createSlug($user->getName());
	        $inputUser = [
	            'name' => $user->getName()?$user->getName():$user->getEmail(),
	            'email' => $user->getEmail(),
	            'password' => bcrypt('123456'),
	            'username' => $username,
	            'avatar' => $service == 'google'?$user->avatar_original: $user->getAvatar(),
	            'role_id' => 2,
	        ];

	        $userData = User::create($inputUser);
	    }

	    $check = UserSocial::whereHas('user')->find($user->getId());
        if ($check) {}
        else {
	        $inputUserSocial = [
				'id' => $user->getId(),
				'user_id' => $userData->id,
				'email' => $user->getEmail(),
				'name' => $user->getName(),
				'avatar' => $service == 'google'?$user->avatar_original: $user->getAvatar()
			];
			
			$this->_regUserSocial($inputUserSocial);
        }
		\Auth::loginUsingId($userData->id);

        return redirect('/');
    }
}
