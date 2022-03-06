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
        'profile_image',
        'background_image',
        'password'
    ];

    protected $hidden = [
        'email',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at',
        'pivot'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function pokedBy()
    {
        return $this->belongsToMany(
            User::class,
            'pokes',
            'poked_id',
            'poked_by_id'
        )
        ->as('pokeInfo')
        ->withPivot('count', 'created_at');
    }

    // public function messages()
    // {
    //     return $this->belongsToMany(
    //         User::class, 
    //         'messages',
    //         'sender_id',
    //         'receiver_id'
    //     );
    // }

    public function invites()
    {
        return $this->hasMany(Friendship::class, 'second_user')
            ->where('status', 'pending');
    }

    public function invitesOf()
    {
        return $this->hasMany(Friendship::class, 'acted_user')
            ->where('status', 'pending');
    }

    public function blocks()
    {
        return $this->hasMany(Friendship::class, 'second_user')
            ->where('status', 'blocked');
    }

    protected function friendsOfThisUser()
	{
		return $this->belongsToMany(User::class, 'friendships', 'first_user', 'second_user')
            ->withPivot('status')
            ->wherePivot('status', 'confirmed');
	}

	protected function thisUserFriendOf()
	{
		return $this->belongsToMany(User::class, 'friendships', 'second_user', 'first_user')
            ->withPivot('status')
            ->wherePivot('status', 'confirmed');
	}

	public function getFriendsAttribute()
	{
		if (!array_key_exists('friends', $this->relations)) {
            $this->loadFriends();
        }

		return $this->getRelation('friends');
	}

	protected function loadFriends()
	{
		if (!array_key_exists('friends', $this->relations)) {
            $friends = $this->mergeFriends();

            $this->setRelation('friends', $friends);
        }
	}

	protected function mergeFriends()
	{
		if($temp = $this->friendsOfThisUser) {
            return $temp->merge($this->thisUserFriendOf);
        } else {
            return $this->thisUserFriendOf;
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
