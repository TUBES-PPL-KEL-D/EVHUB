<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VendorProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'             => \App\Models\User::factory(),
            'company_name'        => $this->faker->company(),
            'company_email'       => $this->faker->companyEmail(),
            'company_phone'       => $this->faker->phoneNumber(),
            'company_address'     => $this->faker->address(),
            'company_description' => $this->faker->sentence(),
        ];
    }
}