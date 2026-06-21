<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Livewire\ImportExpenses;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use App\Support\CategoryLibrary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guesses_category_from_merchant_description(): void
    {
        $this->assertSame('Groceries', CategoryLibrary::guessName('SAVE ON FOODS #993 VANCOUVER'));
        $this->assertSame('Food & Dining', CategoryLibrary::guessName('SQ *THAI SIAM RESTAURA Calgary'));
        // "ubereats" (Food) must beat "uber" (Transport)
        $this->assertSame('Food & Dining', CategoryLibrary::guessName('UBER CANADA/UBEREATS TORONTO'));
        $this->assertSame('Transport', CategoryLibrary::guessName('UBER CANADA/UBERTRIP TORONTO'));
        $this->assertSame('Entertainment', CategoryLibrary::guessName('Spotify P41CD89405 Stockholm'));
        $this->assertSame('Shopping', CategoryLibrary::guessName('STAPLES STORE #239 VANCOUVER'));
        $this->assertNull(CategoryLibrary::guessName('UBC SHCS VANCOUVER'));
    }

    public function test_new_users_receive_default_categories(): void
    {
        $user = (new CreateNewUser())->create([
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertGreaterThan(0, Category::where('user_id', $user->id)->count());
        $this->assertDatabaseHas('categories', ['user_id' => $user->id, 'name' => 'Groceries']);
        $this->assertDatabaseHas('categories', ['user_id' => $user->id, 'name' => 'Transport']);
    }

    public function test_seeding_defaults_is_idempotent(): void
    {
        $user = User::factory()->create();

        $first = CategoryLibrary::seedFor($user);
        $second = CategoryLibrary::seedFor($user);

        $this->assertGreaterThan(0, $first);
        $this->assertSame(0, $second, 'Re-seeding should not create duplicates.');
        $this->assertSame($first, Category::where('user_id', $user->id)->count());
    }

    public function test_import_auto_categorizes_rows_by_description(): void
    {
        $user = User::factory()->create();
        CategoryLibrary::seedFor($user);

        $csv = implode("\n", [
            'Account,Transaction Date,Description 1,Description 2,CAD$',
            '123,2026-04-25,SQ *THAI SIAM RESTAURA Calgary,,-88.82',
            '123,2026-04-26,Spotify P41CD89405 Stockholm,,-22.04',
            '123,2026-04-27,SAVE ON FOODS #993 VANCOUVER,,-48.59',
            '123,2026-04-28,UBER CANADA/UBEREATS TORONTO,,-24.78',
            '123,2026-04-28,UBER CANADA/UBERTRIP TORONTO,,-16.74',
            '123,2026-05-01,PAYROLL DEPOSIT,,2500.00',
        ])."\n";

        $file = UploadedFile::fake()->createWithContent('statement.csv', $csv);

        Livewire::actingAs($user)
            ->test(ImportExpenses::class)
            ->set('file', $file)
            ->set('dateColumn', 1)
            ->set('descriptionColumn', 2)
            ->set('amountColumn', 4)
            ->set('onlyOutflows', true)
            ->set('autoCategorize', true)
            ->call('import')
            ->assertHasNoErrors()
            ->assertRedirect(route('expenses.index'));

        $catId = fn (string $name) => Category::where('user_id', $user->id)->where('name', $name)->value('id');

        // Income row skipped, 5 spend rows imported
        $this->assertSame(5, Expense::where('user_id', $user->id)->count());

        $this->assertSame($catId('Food & Dining'), Expense::where('title', 'like', '%THAI SIAM%')->value('category_id'));
        $this->assertSame($catId('Entertainment'), Expense::where('title', 'like', 'Spotify%')->value('category_id'));
        $this->assertSame($catId('Groceries'), Expense::where('title', 'like', 'SAVE ON FOODS%')->value('category_id'));
        $this->assertSame($catId('Food & Dining'), Expense::where('title', 'like', '%UBEREATS%')->value('category_id'));
        $this->assertSame($catId('Transport'), Expense::where('title', 'like', '%UBERTRIP%')->value('category_id'));
    }

    public function test_import_falls_back_to_chosen_category_when_autocategorize_off(): void
    {
        $user = User::factory()->create();
        CategoryLibrary::seedFor($user);
        $shopping = Category::where('user_id', $user->id)->where('name', 'Shopping')->first();

        $csv = "Date,Description,Amount\n2026-04-25,MYSTERY MERCHANT XYZ,-10.00\n";
        $file = UploadedFile::fake()->createWithContent('statement.csv', $csv);

        Livewire::actingAs($user)
            ->test(ImportExpenses::class)
            ->set('file', $file)
            ->set('dateColumn', 0)
            ->set('descriptionColumn', 1)
            ->set('amountColumn', 2)
            ->set('autoCategorize', false)
            ->set('defaultCategoryId', $shopping->id)
            ->call('import')
            ->assertHasNoErrors();

        $this->assertSame($shopping->id, Expense::where('user_id', $user->id)->value('category_id'));
    }
}
