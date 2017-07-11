<?php

    namespace Soda\Analytics\Database\Models;

    use Carbon\Carbon;
    use Illuminate\Foundation\Auth\User as Authenticatable;

    class User extends Authenticatable{
        protected $table = 'soda_analytics_users';

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'name',
            'google_id',
            'email',
            'refresh_token',
            'code',
            'last_loggedin_at',
        ];

        /**
         * The attributes that should be mutated to dates.
         *
         * @var array
         */
        protected $dates = [
            'created_at',
            'updated_at',
            'last_loggedin_at',
        ];

        public function updateLoginTimestamp() {
            $this->setAttribute('last_loggedin_at', Carbon::now());
            $this->save();
        }
    }
