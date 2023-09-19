<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class TaskTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function a_user_can_browse_tasks()
    {
        $response = $this->get('/info');

        $response->assertStatus(200);
    }
}
