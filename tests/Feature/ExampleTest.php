<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_browse_tasks()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/vfr');

        $response->assertStatus(200);
    }
}
