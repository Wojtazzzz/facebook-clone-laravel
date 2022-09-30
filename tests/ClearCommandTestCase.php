<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class ClearCommandTestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpFaker();
    }
}
