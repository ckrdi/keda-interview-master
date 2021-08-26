<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender_id' => $this->faker->numberBetween(1, 2),
            'sent_to_id' => $this->faker->numberBetween(3, 4),
            'subject' => $this->faker->words(3, true),
            'message' => $this->faker->words(10, true)
        ];
    }
}
