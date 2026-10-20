<?php

namespace Tests\Feature;

use App\Livewire\ExpenseList;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

// Regression cover for the search filter's user scoping. The two LIKEs for the
// search term have to stay grouped: ungrouped, SQL reads
// `user_id = A AND title LIKE x OR description LIKE x` as
// `(user_id = A AND title LIKE x) OR (description LIKE x)`, and that second
// branch matches every user's rows. The paginated list grouped them correctly
// while the total did not, so the figure on the page included other people's
// expenses. Both now build from ExpenseList::filteredQuery().
class ExpenseSearchScopeTest extends TestCase
{
    use RefreshDatabase;

    private function expense(User $user, array $attributes = []): Expense
    {
        return Expense::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Something',
            'amount' => 1,
            'date' => now()->startOfMonth()->addDays(3),
            'type' => 'one-time',
        ], $attributes));
    }

    public function test_search_total_excludes_other_users_matching_expenses(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->expense($me, ['title' => 'Coffee Shop', 'amount' => 10]);
        // Matches the search term only on its description, which is exactly the
        // branch that used to escape the user_id filter.
        $this->expense($other, ['title' => 'Unrelated', 'description' => 'coffee beans', 'amount' => 999]);

        $component = Livewire::actingAs($me)
            ->test(ExpenseList::class)
            ->set('search', 'coffee');

        $this->assertEqualsWithDelta(10, (float) $component->viewData('total'), 0.001);
    }

    public function test_search_list_excludes_other_users_matching_expenses(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->expense($me, ['title' => 'Coffee Shop', 'amount' => 10]);
        $this->expense($other, ['title' => 'LeakedRow', 'description' => 'coffee beans', 'amount' => 999]);

        Livewire::actingAs($me)
            ->test(ExpenseList::class)
            ->set('search', 'coffee')
            ->assertSee('Coffee Shop')
            ->assertDontSee('LeakedRow');
    }

    public function test_search_matches_the_users_own_description(): void
    {
        $me = User::factory()->create();

        $this->expense($me, ['title' => 'Blue Bottle', 'description' => 'morning coffee', 'amount' => 7]);
        $this->expense($me, ['title' => 'Bus Pass', 'amount' => 40]);

        $component = Livewire::actingAs($me)
            ->test(ExpenseList::class)
            ->set('search', 'coffee')
            ->assertSee('Blue Bottle')
            ->assertDontSee('Bus Pass');

        $this->assertEqualsWithDelta(7, (float) $component->viewData('total'), 0.001);
    }

    public function test_search_total_respects_the_other_filters(): void
    {
        $me = User::factory()->create();
        $food = Category::create(['user_id' => $me->id, 'name' => 'Food', 'color' => '#111111']);
        $travel = Category::create(['user_id' => $me->id, 'name' => 'Travel', 'color' => '#222222']);

        $this->expense($me, ['title' => 'Coffee Shop', 'amount' => 10, 'category_id' => $food->id]);
        $this->expense($me, ['title' => 'Coffee In Transit', 'amount' => 25, 'category_id' => $travel->id]);
        // Same search term, but outside the selected date range.
        $this->expense($me, [
            'title' => 'Coffee Last Year',
            'amount' => 500,
            'category_id' => $food->id,
            'date' => now()->startOfMonth()->subYear(),
        ]);

        $component = Livewire::actingAs($me)
            ->test(ExpenseList::class)
            ->set('search', 'coffee')
            ->set('selectedCategory', $food->id);

        $this->assertEqualsWithDelta(10, (float) $component->viewData('total'), 0.001);
    }
}
