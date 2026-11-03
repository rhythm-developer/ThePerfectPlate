<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Enums\RoleName;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeAdmin(): User
{
    return User::factory()->create(['role_id' => Role::idFor(RoleName::Admin)]);
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
        '/admin/users',
        '/admin/users/create',
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

    $response = $this->get("/admin/users/{$custUser->id}/edit");
    expect($response->status())->toBe(200);
});

it('loads the view page and populated list page of every resource', function () {
    $this->actingAs(makeAdmin());

    $customer = Customer::create(['user_id' => User::factory()->create()->id, 'customer_code' => 'CUS-1', 'is_active' => true]);
    $employee = Employee::create(['user_id' => User::factory()->create()->id, 'employee_code' => 'EMP-1', 'designation' => 'Chef', 'is_active' => true]);
    $cat = MenuCategory::create(['name' => 'Starters', 'slug' => 'starters', 'sort_order' => 0, 'is_active' => true]);
    $item = MenuItem::create(['menu_category_id' => $cat->id, 'name' => 'Spring Rolls', 'price' => 150, 'is_vegetarian' => true, 'is_available' => true]);
    $order = Order::create(['order_number' => 'ORD-1', 'customer_id' => $customer->id, 'status' => OrderStatus::Pending, 'discount' => 0, 'tax' => 0]);
    $event = $order->events()->create(['name' => 'Reception', 'event_date' => now()->addDays(10), 'guest_count' => 50, 'status' => 'pending']);
    $payment = Payment::create(['order_id' => $order->id, 'amount' => 100, 'method' => PaymentMethod::cases()[0], 'type' => PaymentType::Payment, 'paid_at' => now()]);

    foreach ([
        'menu-categories' => $cat,
        'menu-items' => $item,
        'customers' => $customer,
        'employees' => $employee,
        'orders' => $order,
        'order-events' => $event,
        'payments' => $payment,
        'users' => $customer->user,
    ] as $slug => $record) {
        $view = $this->get("/admin/{$slug}/{$record->getKey()}");
        expect($view->status())->toBe(200, "View page /admin/{$slug}/{$record->getKey()}");

        $list = $this->get("/admin/{$slug}");
        expect($list->status())->toBe(200, "List page /admin/{$slug}");
    }
});
