<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessSyncFacilityByUpdateTimeApi;
use Tests\TestCase;

/**
 * Class ProcessSyncFacilityByUpdateTimeApiTest.
 *
 * @covers \App\Jobs\ProcessSyncFacilityByUpdateTimeApi
 */
class ProcessSyncFacilityByUpdateTimeApiTest extends TestCase
{
    /**
     * @var ProcessSyncFacilityByUpdateTimeApi
     */
    protected $processSyncFacilityByUpdateTimeApi;

    /**
     * @var mixed
     */
    protected $access_token;

    /**
     * @var mixed
     */
    protected $current_page;

    /**
     * @var mixed
     */
    protected $update_time;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->access_token = null;
        $this->current_page = null;
        $this->update_time = null;
        $this->processSyncFacilityByUpdateTimeApi = new ProcessSyncFacilityByUpdateTimeApi($this->access_token, $this->current_page, $this->update_time);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->processSyncFacilityByUpdateTimeApi);
        unset($this->access_token);
        unset($this->current_page);
        unset($this->update_time);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->processSyncFacilityByUpdateTimeApi->handle();
    }
}
