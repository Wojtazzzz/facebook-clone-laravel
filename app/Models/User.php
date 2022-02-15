<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Prunable;

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
        'pivot'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

	public function friends()
	{
		return $this->belongsToMany(User::class, 'friendships', 'first_user', 'second_user')
            ->withPivot('status')
            ->wherePivot('status', 'confirmed');
	}

	public function friendOf()
	{
		return $this->belongsToMany(User::class, 'friendships', 'second_user', 'first_user')
            ->withPivot('status')
            ->wherePivot('status', 'confirmed');
	}

	public function getFriendsAttribute()
	{
		if (!array_key_exists('friends', $this->relations)) {
            $friends = $this->mergeFriends();
    
            $this->setRelation('friends', $friends);
        }

		return $this->getRelation('friends');
	}

	protected function mergeFriends()
	{
		if ($temp = $this->friends) {
            return $temp->merge($this->friendOf);
        } else {
            return $this->friendOf;
        }
	}

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(10));
    }

    protected function pruning()
    {
        // delete data which user created...
    }
}
