<?php

namespace App\Observers;

use Algolia\AlgoliaSearch\SearchClient;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        $ALGOLIA_APP_ID = config('algolia.app_id');
        $ALGOLIA_ADMIN_KEY = config('algolia.admin_key');
        $ALGOLIA_USER_INDEX_NAME = config('algolia.user_index_name');

        $client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_ADMIN_KEY);

        $index = $client->initIndex($ALGOLIA_USER_INDEX_NAME);

        $user = $user->only(['id', 'first_name', 'last_name', 'profile_image']);

        $res = $index->saveObjects([$user], ['objectIDKey' => 'id']);

        $res->wait();
    }

    public function deleted(User $user): void
    {
        $ALGOLIA_APP_ID = config('algolia.app_id');
        $ALGOLIA_ADMIN_KEY = config('algolia.admin_key');
        $ALGOLIA_USER_INDEX_NAME = config('algolia.user_index_name');

        $client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_ADMIN_KEY);

        $index = $client->initIndex($ALGOLIA_USER_INDEX_NAME);

        $res = $index->deleteObject($user->id);

        $res->wait();
    }
}
