<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateFacility;
use Tests\TestCase;

/**
 * Class CreateFacilityTest.
 *
 * @covers \App\Jobs\CreateFacility
 */
class CreateFacilityTest extends TestCase
{
    /**
     * @var CreateFacility
     */
    protected $createFacility;

    /**
     * @var mixed
     */
    protected $facility_data;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->facility_data = null;
        $this->createFacility = new CreateFacility($this->facility_data);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->createFacility);
        unset($this->facility_data);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->createFacility->handle();
    }
}
