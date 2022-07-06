<?php

namespace App\Console\Commands\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Illuminate\Console\Command;

class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algolia:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear algolia\'s dataset';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ALGOLIA_APP_ID = config('algolia.app_id');
        $ALGOLIA_ADMIN_KEY = config('algolia.admin_key');
        $ALGOLIA_USER_INDEX_NAME = config('algolia.user_index_name');

        $client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_ADMIN_KEY);

        $index = $client->initIndex($ALGOLIA_USER_INDEX_NAME);

        $res = $index->clearObjects();

        $res->wait();

        $this->info('Algolia\'s dataset is now empty');

        return 0;
    }
}
