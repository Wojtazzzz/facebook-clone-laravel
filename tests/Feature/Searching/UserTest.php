<?php

declare(strict_types=1);

namespace Tests\Feature\Searching;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne([
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
        ]);
    }

    public function testReturnEmptyResponseWhenNoQueryProvided(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute([
                'page' => 1,
            ]));

        $response->assertOk()
            ->assertJson([
                'data' => [],
            ]);
    }

    public function testReturnFriendWhenHisNameProvided(): void
    {
        User::factory()->create([
            'first_name' => 'John',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute([
                'page' => 1,
                'search' => 'Jo',
            ]));

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testReturnMax10Users(): void
    {
        User::factory(14)->create([
            'first_name' => 'John',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute([
                'page' => 1,
                'search' => 'John',
            ]));

        $response->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function testReturnRestUsersFromSecondPage(): void
    {
        User::factory(14)->create([
            'first_name' => 'John',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute([
                'page' => 2,
                'search' => 'Joh',
            ]));

        $response->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function testReturnEmptyResponseWhenNoResults(): void
    {
        User::factory(14)->create([
            'first_name' => 'John',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute([
                'page' => 1,
                'search' => 'Adam',
            ]));

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasOnlyFirstPage(): void
    {
        User::factory(4)->create([
            'first_name' => 'John',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute([
                'page' => 1,
                'search' => 'John',
            ]));

        $response->assertOk()
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => null,
                'prev_page' => null,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasSecondPage(): void
    {
        User::factory(11)->create([
            'first_name' => 'John',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute([
                'page' => 1,
                'search' => 'John',
            ]));

        $response->assertOk()
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => 2,
                'prev_page' => null,
            ]);
    }

    public function testSecondPageReturnProperlyPaginationData(): void
    {
        User::factory(12)->create([
            'first_name' => 'John',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->getRoute([
                'page' => 2,
                'search' => 'John',
            ]));

        $response->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
                'next_page' => null,
                'prev_page' => 1,
            ]);
    }

    private function getRoute(array $params): string
    {
        return route('api.search', $params);
    }
}
