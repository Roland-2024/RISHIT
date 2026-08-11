<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiFoundationTest extends TestCase
{
    public function test_versioned_health_endpoint_uses_the_json_envelope(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'status' => 'ok',
                    'api_version' => 'v1',
                ],
            ]);
    }
}
