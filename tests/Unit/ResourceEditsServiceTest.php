<?php

namespace Tests\Unit;

use App\Services\ResourceEditsService;
use PHPUnit\Framework\TestCase;

class ResourceEditsServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ResourceEditsService::class);
    }

    /**
     * Zero votes on a resource, 1 approval is minimum to merge
     */
    public function test_zero_votes_on_edit(): void
    {
        $this->assertEquals($this->service->requiredVotes(0), 1);
    }

    /**
     * 1 vote on a resource, 3 approvals is minimum to merge
     */
    public function test_one_vote_on_edit(): void
    {
        $this->assertEquals($this->service->requiredVotes(1), 3);
    }

    /**
     * 5 votes on a resource, so 5 approval is enough to merge
     */
    public function test_five_votes_on_edit(): void
    {
        $this->assertEquals($this->service->requiredVotes(5), 5);
    }

    /**
     * 25 votes on a resource, so something smaller but reasonable
     * Log(25) base 1.25 is 14, and add one, so 15
     */
    public function test_25_votes_on_edit(): void
    {
        $this->assertEquals($this->service->requiredVotes(25), 15);
    }

    /**
     * 1000 votes on a resource, so something much smaller
     * Log(1000) base 1.25 is 30, and add one, so 31
     */
    public function test_thousand_votes_on_edit(): void
    {
        $this->assertEquals($this->service->requiredVotes(1_000), 31);
    }

    /**
     * 1_000_000 votes on a resource, so something much smaller
     * Log(1_000_000) base 1.25 is 61, and add one, so 62
     */
    public function test_million_votes_on_edit(): void
    {
        $this->assertEquals($this->service->requiredVotes(1_000_000), 62);
    }
}
