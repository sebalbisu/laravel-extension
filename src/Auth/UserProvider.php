<?php namespace Sebalbisu\Laravel\Auth;

use Illuminate\Contracts\Auth\UserProvider as UserProviderBase;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use App\Models\Entities;
use App\Models\Repositories as Repos;

class UserProvider implements UserProviderBase {
    
    protected $hasher;

    protected $repoUser;

    protected $repoPass;

    public function __construct(HasherContract $hasher, Repos\User $repoUser, Repos\Password $repoPass)
    {
        $this->hasher = $hasher;
        $this->repoUser = $repoUser;
        $this->repoPass = $repoPass;
    }

    public function retrieveById($identifier)
    {
        return $this->repoUser->getByIdForLogin($identifier['id']);
    }
    
    public function retrieveByToken($identifier, $token)
    {
        return $this->repoUser->getByIdTokenForLogin($identifier['id'], $token);
    }
    
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->password->remember_token = $token;

        $this->repoPass->save($user->password);
    }
    
    public function retrieveByCredentials(array $credentials)
    {
        return $this->repoUser->getByEmailForLogin($credentials['email']);
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        $isValid = $this->hasher->check( $credentials['password'], $user->getAuthPassword());

        return $isValid;
    }
}
