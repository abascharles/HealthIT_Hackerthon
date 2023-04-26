<?php

namespace Tests\Unit\Jobs;

use App\Jobs\PopulateDatabase;
use Tests\TestCase;

/**
 * Class PopulateDatabaseTest.
 *
 * @covers \App\Jobs\PopulateDatabase
 */
class PopulateDatabaseTest extends TestCase
{
    /**
     * @var PopulateDatabase
     */
    protected $populateDatabase;

    /**
     * @var mixed
     */
    protected $results;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->results = null;
        $this->populateDatabase = new PopulateDatabase($this->results);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->populateDatabase);
        unset($this->results);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->populateDatabase->handle();
    }
}
