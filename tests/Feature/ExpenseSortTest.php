<?php

namespace Tests\Feature;

use App\Livewire\ExpenseList;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExpenseSortTest extends TestCase
{
    use RefreshDatabase;

    private function seedExpenses(User $user): void
    {
        $apple = Category::create(['user_id' => $user->id, 'name' => 'Apple', 'color' => '#111111']);
        $mango = Category::create(['user_id' => $user->id, 'name' => 'Mango', 'color' => '#222222']);
        $zebra = Category::create(['user_id' => $user->id, 'name' => 'Zebra', 'color' => '#333333']);

        $start = now()->startOfMonth();

        // amount, date offset, category — deliberately uncorrelated so each sort gives a distinct order
        Expense::create(['user_id' => $user->id, 'category_id' => $zebra->id, 'title' => 'CoffeeRun', 'amount' => 5, 'date' => $start->copy()->addDays(7), 'type' => 'one-time']);
        Expense::create(['user_id' => $user->id, 'category_id' => $apple->id, 'title' => 'GroceryHaul', 'amount' => 50, 'date' => $start->copy()->addDays(5), 'type' => 'one-time']);
        Expense::create(['user_id' => $user->id, 'category_id' => $mango->id, 'title' => 'LaptopBuy', 'amount' => 500, 'date' => $start->copy()->addDays(6), 'type' => 'one-time']);
    }

    public function test_sort_by_amount_ascending_and_descending(): void
    {
        $user = User::factory()->create();
        $this->seedExpenses($user);

        Livewire::actingAs($user)
            ->test(ExpenseList::class)
            ->call('sortByField', 'amount')
            ->assertSet('sortBy', 'amount')
            ->assertSet('sortDirection', 'asc')
            ->assertSeeInOrder(['CoffeeRun', 'GroceryHaul', 'LaptopBuy'])
            ->call('sortByField', 'amount')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeInOrder(['LaptopBuy', 'GroceryHaul', 'CoffeeRun']);
    }

    public function test_sort_by_date_ascending_and_descending(): void
    {
        $user = User::factory()->create();
        $this->seedExpenses($user);

        Livewire::actingAs($user)
            ->test(ExpenseList::class)
            // default is date desc; toggling once makes it asc
            ->call('sortByField', 'date')
            ->assertSet('sortDirection', 'asc')
            ->assertSeeInOrder(['GroceryHaul', 'LaptopBuy', 'CoffeeRun'])
            ->call('sortByField', 'date')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeInOrder(['CoffeeRun', 'LaptopBuy', 'GroceryHaul']);
    }

    public function test_sort_by_category_ascending_and_descending(): void
    {
        $user = User::factory()->create();
        $this->seedExpenses($user);

        Livewire::actingAs($user)
            ->test(ExpenseList::class)
            ->call('sortByField', 'category')
            ->assertSet('sortBy', 'category')
            ->assertSet('sortDirection', 'asc')
            // by category name: Apple(GroceryHaul), Mango(LaptopBuy), Zebra(CoffeeRun)
            ->assertSeeInOrder(['GroceryHaul', 'LaptopBuy', 'CoffeeRun'])
            ->call('sortByField', 'category')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeInOrder(['CoffeeRun', 'LaptopBuy', 'GroceryHaul']);
    }
}
