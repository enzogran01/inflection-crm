<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['receita', 'despesa']);
        $status = $this->faker->randomElement(['pendente', 'pago', 'atrasado']);
        
        $category = null;
        if ($type === 'despesa') {
            $category = $this->faker->randomElement(['Infraestrutura', 'Folha de Pagamento', 'Marketing', 'Impostos', 'Serviços', 'Outros']);
        } elseif ($type === 'receita') {
            $category = $this->faker->randomElement(['Vendas', 'Serviços']);
        }

        return [
            'description' => $this->faker->sentence(3),
            'type' => $type,
            'category' => $category,
            'amount' => $this->faker->numberBetween(10000, 500000), // R$ 100,00 to R$ 5.000,00
            'due_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'paid_at' => $status === 'pago' ? $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d') : null,
            'status' => $status,
        ];
    }
}
