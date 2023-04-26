<?php

namespace Tests\Unit\Tests\Unit\Jobs;

use Tests\TestCase;
use Tests\Unit\Jobs\SynchNewFacilityTest;

/**
 * Class SynchNewFacilityTestTest.
 *
 * @covers \Tests\Unit\Jobs\SynchNewFacilityTest
 */
class SynchNewFacilityTestTest extends TestCase
{
    /**
     * @var SynchNewFacilityTest
     */
    protected $synchNewFacilityTest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->synchNewFacilityTest = new SynchNewFacilityTest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->synchNewFacilityTest);
    }

    public function testTestHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
