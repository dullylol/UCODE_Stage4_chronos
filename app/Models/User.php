<?php

    namespace App\Models;

    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Tymon\JWTAuth\Contracts\JWTSubject;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    class User extends Authenticatable implements JWTSubject
    {
        use Notifiable, HasFactory;

        protected $fillable = [
            'login',
            'email',
            'password',
        ];

        protected $hidden = [
            'password',
            'remember_token',
        ];

        public function getJWTIdentifier()
        {
            return $this->getKey();
        }

        public function getJWTCustomClaims()
        {
            return [];
        }
    }