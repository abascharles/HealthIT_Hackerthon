<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SyncFacilityUpdate;
use Tests\TestCase;

/**
 * Class SyncFacilityUpdateTest.
 *
 * @covers \App\Jobs\SyncFacilityUpdate
 */
class SyncFacilityUpdateTest extends TestCase
{
    /**
     * @var SyncFacilityUpdate
     */
    protected $syncFacilityUpdate;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->syncFacilityUpdate = new SyncFacilityUpdate();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->syncFacilityUpdate);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->syncFacilityUpdate->handle();
    }
}
