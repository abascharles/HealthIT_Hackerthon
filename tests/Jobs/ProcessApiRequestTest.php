<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessApiRequest;
use Tests\TestCase;

/**
 * Class ProcessApiRequestTest.
 *
 * @covers \App\Jobs\ProcessApiRequest
 */
class ProcessApiRequestTest extends TestCase
{
    /**
     * @var ProcessApiRequest
     */
    protected $processApiRequest;

    /**
     * @var mixed
     */
    protected $access_token;

    /**
     * @var mixed
     */
    protected $currentPage;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->access_token = null;
        $this->currentPage = null;
        $this->processApiRequest = new ProcessApiRequest($this->access_token, $this->currentPage);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->processApiRequest);
        unset($this->access_token);
        unset($this->currentPage);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->processApiRequest->handle();
    }
}
