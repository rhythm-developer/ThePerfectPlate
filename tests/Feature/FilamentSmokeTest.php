<?php

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeAdmin(): User
{
    return User::factory()->create();
}

it('loads every catering resource index and create page', function () {
    $this->actingAs(makeAdmin());

    foreach ([
        '/admin',
        '/admin/menu-categories',
        '/admin/menu-categories/create',
        '/admin/menu-items',
        '/admin/menu-items/create',
        '/admin/customers',
        '/admin/customers/create',
        '/admin/employees',
        '/admin/employees/create',
        '/admin/orders',
        '/admin/orders/create',
        '/admin/payments',
        '/admin/payments/create',
    ] as $url) {
        $response = $this->get($url);
        expect($response->status())->toBe(200, "Failed loading {$url}: got {$response->status()}");
    }
});

it('loads order edit page with events relation manager and order-event edit with sub relation managers', function () {
    $this->actingAs(makeAdmin());

    $custUser = User::factory()->create();
    $customer = Customer::create(['user_id' => $custUser->id, 'customer_code' => 'CUS-1', 'is_active' => true]);

    $cat = MenuCategory::create(['name' => 'Starters', 'slug' => 'starters', 'sort_order' => 0, 'is_active' => true]);
    MenuItem::create(['menu_category_id' => $cat->id, 'name' => 'Spring Rolls', 'price' => 150, 'is_vegetarian' => true, 'is_available' => true]);

    $order = Order::create(['order_number' => 'ORD-1', 'customer_id' => $customer->id, 'status' => OrderStatus::Pending, 'discount' => 0, 'tax' => 0]);
    $event = $order->events()->create(['name' => 'Reception', 'event_date' => now()->addDays(10), 'guest_count' => 50, 'status' => 'pending']);

    $response = $this->get("/admin/orders/{$order->id}/edit");
    expect($response->status())->toBe(200);

    $response = $this->get("/admin/order-events/{$event->id}/edit");
    expect($response->status())->toBe(200);
});
