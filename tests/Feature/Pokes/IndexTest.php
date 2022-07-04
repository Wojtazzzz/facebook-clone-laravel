<?php

declare(strict_types=1);

namespace Tests\Feature\Pokes;

use App\Models\Poke;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private string $pokesRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->pokesRoute = route('api.pokes.index');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->pokesRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->pokesRoute);
        $response->assertOk();
    }

    public function testReturnMaxTenPokes(): void
    {
        Poke::factory(13)->create([
            'friend_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->pokesRoute);
        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testCanFetchMorePokesFromSecondPage(): void
    {
        Poke::factory(14)->create([
            'friend_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->pokesRoute.'?page=2');
        $response->assertOk()
            ->assertJsonCount(4);
    }

    public function testReturnOnlyPokesWhereLoggedUserIsPoked(): void
    {
        Poke::factory(3)->create([
            'friend_id' => $this->user->id,
        ]);

        Poke::factory(3)->create([
            'user_id' => $this->user->id,
            'latest_initiator_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->pokesRoute);
        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function testReturnEmptyResponseWhenNoPokes(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->pokesRoute);
        $response->assertOk()
            ->assertJsonCount(0);
    }
}
