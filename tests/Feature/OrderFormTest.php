<?php

use App\Enums\OrderStatus;
use App\Enums\RoleName;
use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderEventEmployee;
use App\Models\OrderEventMenuItem;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role_id' => Role::idFor(RoleName::Admin)]));

    $this->customer = Customer::create(['user_id' => User::factory()->create()->id, 'is_active' => true]);
    $this->chef = Employee::create(['user_id' => User::factory()->create()->id, 'designation' => 'Chef', 'is_active' => true]);
    $this->waiter = Employee::create(['user_id' => User::factory()->create()->id, 'designation' => 'Waiter', 'is_active' => true]);

    $category = MenuCategory::create(['name' => 'Mains', 'slug' => 'mains', 'sort_order' => 0, 'is_active' => true]);
    $this->biryani = MenuItem::create(['menu_category_id' => $category->id, 'name' => 'Biryani', 'price' => 200, 'is_vegetarian' => false, 'is_available' => true]);
    $this->jamun = MenuItem::create(['menu_category_id' => $category->id, 'name' => 'Gulab Jamun', 'price' => 40, 'is_vegetarian' => true, 'is_available' => true]);
});

/**
 * Creates an order with two events through the form:
 *  Wedding: 100 × Biryani @ 200 + 100 × Gulab Jamun @ 40 = 24,000; chef + waiter
 *  Mehendi: 50 × Biryani @ 180 = 9,000; chef
 *  Subtotal 33,000, discount 1,000, tax 500 → total 32,500
 */
function createOrderThroughForm(object $test): Order
{
    $undoRepeaterFake = Repeater::fake();

    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_id' => $test->customer->id,
            'status' => OrderStatus::Confirmed,
            'discount' => 1000,
            'tax' => 500,
            'events' => [
                [
                    'name' => 'Wedding',
                    'event_date' => now()->addMonth()->toDateString(),
                    'guest_count' => 100,
                    'status' => 'pending',
                    'menuItemLines' => [
                        ['menu_item_id' => $test->biryani->id, 'quantity' => 100, 'unit_price' => 200],
                        ['menu_item_id' => $test->jamun->id, 'quantity' => 100, 'unit_price' => 40],
                    ],
                    'staffAssignments' => [
                        ['employee_id' => $test->chef->id, 'role' => 'Head chef'],
                        ['employee_id' => $test->waiter->id, 'role' => 'Server'],
                    ],
                ],
                [
                    'name' => 'Mehendi',
                    'event_date' => now()->addMonth()->subDay()->toDateString(),
                    'guest_count' => 50,
                    'status' => 'pending',
                    'menuItemLines' => [
                        ['menu_item_id' => $test->biryani->id, 'quantity' => 50, 'unit_price' => 180],
                    ],
                    'staffAssignments' => [
                        ['employee_id' => $test->chef->id, 'role' => 'Chef'],
                    ],
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    return Order::firstOrFail();
}

it('creates an order with events, menu items and staff in one save', function () {
    $order = createOrderThroughForm($this);

    expect($order->order_number)->toStartWith('ORD-')
        ->and($order->events)->toHaveCount(2)
        ->and(OrderEventMenuItem::count())->toBe(3)
        ->and(OrderEventEmployee::count())->toBe(3);

    $wedding = $order->events->firstWhere('name', 'Wedding');
    $mehendi = $order->events->firstWhere('name', 'Mehendi');

    expect((float) $wedding->menuItemLines->firstWhere('menu_item_id', $this->jamun->id)->line_total)->toBe(4000.0)
        ->and((float) $wedding->subtotal)->toBe(24000.0)
        ->and((float) $mehendi->subtotal)->toBe(9000.0)
        ->and($wedding->staffAssignments->firstWhere('employee_id', $this->chef->id)->role)->toBe('Head chef')
        ->and((float) $order->subtotal)->toBe(33000.0)
        ->and((float) $order->total)->toBe(32500.0);
});

it('updates, removes and recalculates nested rows when editing an order', function () {
    $order = createOrderThroughForm($this);
    $wedding = $order->events->firstWhere('name', 'Wedding');
    $mehendi = $order->events->firstWhere('name', 'Mehendi');
    $biryaniLineId = $wedding->menuItemLines->firstWhere('menu_item_id', $this->biryani->id)->id;

    $component = Livewire::test(EditOrder::class, ['record' => $order->getKey()]);

    $events = $component->get('data.events');

    // Remove the Mehendi event entirely.
    $events = collect($events)->reject(fn ($event) => $event['name'] === 'Mehendi')->all();

    // In the wedding: biryani quantity 100 → 50, and drop the waiter.
    $weddingKey = collect($events)->search(fn ($event) => $event['name'] === 'Wedding');
    $lineKey = collect($events[$weddingKey]['menuItemLines'])->search(fn ($line) => $line['menu_item_id'] == $this->biryani->id);
    $events[$weddingKey]['menuItemLines'][$lineKey]['quantity'] = 50;
    $events[$weddingKey]['staffAssignments'] = collect($events[$weddingKey]['staffAssignments'])
        ->reject(fn ($row) => $row['employee_id'] == $this->waiter->id)
        ->all();

    $component->set('data.events', $events)
        ->call('save')
        ->assertHasNoFormErrors();

    $order->refresh();
    $wedding->refresh();

    expect($order->events()->pluck('name')->all())->toBe(['Wedding'])
        ->and($mehendi->fresh())->toBeNull()
        // The biryani line was updated in place, not deleted and recreated.
        ->and(OrderEventMenuItem::find($biryaniLineId)?->quantity)->toBe(50)
        ->and(OrderEventEmployee::where('order_event_id', $wedding->id)->pluck('employee_id')->all())->toBe([$this->chef->id])
        // 50 × 200 + 100 × 40 = 14,000; total = 14,000 − 1,000 + 500
        ->and((float) $wedding->subtotal)->toBe(14000.0)
        ->and((float) $order->subtotal)->toBe(14000.0)
        ->and((float) $order->total)->toBe(13500.0);
});
