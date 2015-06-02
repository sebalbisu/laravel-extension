<?php namespace Sebalbisu\Laravel\Auth;

//use Illuminate\Contracts\Auth\Authenticatable as UserContract;

trait AuthenticatableUser // implements UserContract
{
    protected $type;

    public function type()
    {
        if(!$this->type)
            $this->type = $this->getAuthIdentifier();

        return $this->type;
    }

    public function password()
    {
        return $this->hasOne('App\Models\Entities\Password', 'user_id');
    }

    public function getAuthIdentifier()
    {
        return $this->type = [
            'id'   => $this->id,
            'role' => $this->role->name,
        ];
    }

    public function getAuthPassword()
    {
        return $this->password->password;
    }

    public function setAuthPassword($value)
    {
        $this->password->password = $value;
    }

    public function getRememberToken()
    {
        $this->password->remember_token;
    }

    public function setRememberToken($token)
    {
        $this->password->remember_token = $token;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
