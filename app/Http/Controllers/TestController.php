<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Algolia\AlgoliaSearch\SearchClient;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Mmo\Faker\PicsumProvider;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();

        $this->faker->addProvider(new PicsumProvider($this->faker));
    }

    public function __invoke()
    {
        $user = User::factory()->createOne();

        dump($user);

        // $user->delete();

        // $ALGOLIA_APP_ID = config('algolia.app_id');
        // $ALGOLIA_ADMIN_KEY = config('algolia.admin_key');
        // $ALGOLIA_USER_INDEX_NAME = config('algolia.user_index_name');

        // $client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_ADMIN_KEY);

        // $index = $client->initIndex($ALGOLIA_USER_INDEX_NAME);

        // $res = $index->deleteObject($user->id);

        // $res->wait();
    }
}
