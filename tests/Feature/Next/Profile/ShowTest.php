<?php

declare(strict_types=1);

namespace Tests\Feature\Next\Profile;

use App\Models\User;
use Tests\TestCase;

class ShowTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.ssg.show', [
            'user' => $this->user,
        ]);
    }

    public function testResponseReturnProperlyData(): void
    {
        $response = $this->getJson($this->route);

        $response->assertOk()
            ->assertJsonFragment([
                'background_image' => $this->user->background_image,
                'born_at' => $this->user->born_at->format('j F Y'),
                'created_at' => $this->user->created_at->format('F Y'),
                'first_name' => $this->user->first_name,
                'from' => $this->user->from ?? '',
                'id' => $this->user->id,
                'lives_in' => $this->user->lives_in ?? '',
                'marital_status' => $this->user->marital_status ?? '',
                'name' => $this->user->name,
                'profile_image' => $this->user->profile_image,
                'went_to' => $this->user->went_to ?? '',
                'works_at' => $this->user->works_at ?? '',
            ]);
    }
}
