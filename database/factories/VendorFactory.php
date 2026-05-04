<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'                => \App\Models\User::factory(),
            'company_name'           => $this->faker->company(),
            'legality_document_path' => 'documents/dummy.pdf',
            'status'                 => 'Pending',
        ];
    }
}