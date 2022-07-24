<?php

declare(strict_types=1);

namespace App\Console\Commands\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Illuminate\Console\Command;

class Clear extends Command
{
    protected $signature = 'algolia:clear';
    protected $description = 'Clear algolia\'s dataset';

    public function handle(): void
    {
        $ALGOLIA_APP_ID = config('algolia.app_id');
        $ALGOLIA_ADMIN_KEY = config('algolia.admin_key');
        $ALGOLIA_USER_INDEX_NAME = config('algolia.user_index_name');

        $client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_ADMIN_KEY);
        $index = $client->initIndex($ALGOLIA_USER_INDEX_NAME);
        $res = $index->clearObjects();

        $res->wait();

        $this->info('Algolia\'s dataset is now empty.');

        return;
    }
}
