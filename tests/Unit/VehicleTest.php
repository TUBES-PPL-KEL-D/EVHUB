<?php

namespace Tests\Unit;

use App\Models\Vehicle;
use PHPUnit\Framework\TestCase;

class VehicleTest extends TestCase
{
    public function test_remaining_range_calculation_from_battery_percentage_and_full_range(): void
    {
        $vehicle = new Vehicle([
            'battery_percentage' => 80,
            'estimated_full_range_km' => 400,
        ]);

        $this->assertSame(320, $vehicle->remaining_range);
    }

    public function test_remaining_range_is_null_when_estimated_full_range_missing(): void
    {
        $vehicle = new Vehicle([
            'battery_percentage' => 55,
        ]);

        $this->assertNull($vehicle->remaining_range);
    }
}
