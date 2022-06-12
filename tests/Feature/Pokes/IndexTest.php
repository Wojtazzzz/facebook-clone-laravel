<?php

namespace Tests\Feature\Pokes;

use App\Models\Poke;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;
    private Collection $users;

    private string $pokesRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->users = User::factory(100)->create();
        $this->pokesRoute = route('api.pokes.index');
    }

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->getJson($this->pokesRoute);

        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson($this->pokesRoute);

        $response->assertOk();
    }

    public function testReturnMaxTenPokes()
    {
        Poke::factory(20)->create([
            'user_id' => fn () => $this->faker()->unique()->randomElement($this->users->pluck('id')->except($this->user->id)),
            'friend_id' => $this->user->id,
            'latest_initiator_id' => fn () => $this->faker()->unique()->randomElement($this->users->pluck('id')->except($this->user->id)),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->pokesRoute);

        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testReturnOnlyPokesWhereLoggedUserIsPoked()
    {
        $faker = $this->faker()->unique();

        Poke::factory(8)->create([
            'user_id' => fn () => $faker->randomElement($this->users->pluck('id')->except($this->user->id)),
            'friend_id' => $this->user->id,
            'latest_initiator_id' => fn () => $faker->randomElement($this->users->pluck('id')->except($this->user->id)),
        ]);

        Poke::factory(8)->create([
            'user_id' => $this->user->id,
            'friend_id' => fn () => $faker->randomElement($this->users->pluck('id')->except($this->user->id)),
            'latest_initiator_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->pokesRoute);

        $response->assertOk()
            ->assertJsonCount(8);
    }

    public function testReturnEmptyResponseWhenNoPokes()
    {
        $response = $this->actingAs($this->user)->getJson($this->pokesRoute);

        $response->assertOk()
            ->assertJsonCount(0);
    }
}
