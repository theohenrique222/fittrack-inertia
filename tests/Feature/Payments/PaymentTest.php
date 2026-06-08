<?php

use App\Actions\Payments\ListOverduePaymentsAction;
use App\Actions\Payments\ListPendingPaymentsAction;
use App\Actions\Payments\MarkPaymentAsPaidAction;
use App\Actions\Payments\RegisterPaymentAction;
use App\Actions\Payments\ReopenPaymentAction;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Http\Resources\PaymentResource;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $this->plan = Plan::factory()->create();
    $this->client = Client::factory()->create([
        'user_id' => User::factory()->create(['role' => UserRole::CLIENT])->id,
        'plan_id' => $this->plan->id,
    ]);
});

test('register payment action creates a payment', function () {
    $action = app(RegisterPaymentAction::class);

    $payment = $action->execute([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'amount' => 99.90,
        'due_date' => now()->addMonth(),
        'status' => PaymentStatus::PENDING,
    ]);

    expect($payment)->toBeInstanceOf(Payment::class)
        ->and($payment->client_id)->toBe($this->client->id)
        ->and($payment->plan_id)->toBe($this->plan->id)
        ->and($payment->amount)->toEqual(99.9)
        ->and($payment->status)->toBe(PaymentStatus::PENDING);
});

test('mark payment as paid action updates status', function () {
    $payment = Payment::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'status' => PaymentStatus::PENDING,
    ]);

    $action = app(MarkPaymentAsPaidAction::class);
    $action->execute($payment, [
        'paid_at' => now(),
        'payment_method' => 'pix',
    ]);

    $payment->refresh();

    expect($payment->status)->toBe(PaymentStatus::PAID)
        ->and($payment->paid_at)->not->toBeNull()
        ->and($payment->payment_method)->toBe('pix');
});

test('reopen payment action sets status back to pending', function () {
    $payment = Payment::factory()->paid()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    $action = app(ReopenPaymentAction::class);
    $action->execute($payment);

    $payment->refresh();

    expect($payment->status)->toBe(PaymentStatus::PENDING)
        ->and($payment->paid_at)->toBeNull();
});

test('list pending payments action returns only pending payments', function () {
    Payment::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'status' => PaymentStatus::PENDING,
    ]);
    Payment::factory()->paid()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);
    Payment::factory()->overdue()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    $action = app(ListPendingPaymentsAction::class);
    $payments = $action->execute();

    expect($payments)->toHaveCount(1)
        ->and($payments->first()->status)->toBe(PaymentStatus::PENDING);
});

test('list overdue payments action returns only overdue payments', function () {
    Payment::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'status' => PaymentStatus::PENDING,
    ]);
    Payment::factory()->overdue()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    $action = app(ListOverduePaymentsAction::class);
    $payments = $action->execute();

    expect($payments)->toHaveCount(1)
        ->and($payments->first()->status)->toBe(PaymentStatus::OVERDUE);
});

test('authenticated user can list payments', function () {
    Payment::factory()->count(3)->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    actingAs($this->admin);

    $response = $this->get(route('payments'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('payments', 3)
        ->has('students')
        ->has('plans')
    );
});

test('authenticated user can filter pending payments', function () {
    Payment::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'status' => PaymentStatus::PENDING,
    ]);
    Payment::factory()->paid()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    actingAs($this->admin);

    $response = $this->get(route('payments', ['filter' => 'pending']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('payments', 1)
    );
});

test('authenticated user can filter overdue payments', function () {
    Payment::factory()->overdue()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);
    Payment::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'status' => PaymentStatus::PENDING,
    ]);

    actingAs($this->admin);

    $response = $this->get(route('payments', ['filter' => 'overdue']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('payments', 1)
    );
});

test('authenticated user can create a payment', function () {
    actingAs($this->admin);

    $response = $this->post(route('payments.store'), [
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'amount' => 99.90,
        'due_date' => now()->addMonth()->format('Y-m-d'),
        'status' => PaymentStatus::PENDING->value,
    ]);

    $response->assertOk();
    expect(Payment::count())->toBe(1);
});

test('authenticated user can mark payment as paid', function () {
    $payment = Payment::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'status' => PaymentStatus::PENDING,
    ]);

    actingAs($this->admin);

    $response = $this->put(route('payments.mark-as-paid', $payment), [
        'paid_at' => now()->format('Y-m-d'),
        'payment_method' => 'pix',
    ]);

    $response->assertOk();
    $payment->refresh();

    expect($payment->status)->toBe(PaymentStatus::PAID);
});

test('authenticated user can reopen a payment', function () {
    $payment = Payment::factory()->paid()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    actingAs($this->admin);

    $response = $this->put(route('payments.reopen', $payment));

    $response->assertOk();
    $payment->refresh();

    expect($payment->status)->toBe(PaymentStatus::PENDING)
        ->and($payment->paid_at)->toBeNull();
});

test('unauthenticated users cannot access payments', function () {
    $this->get(route('payments'))->assertRedirect(route('login'));
    $this->post(route('payments.store'))->assertRedirect(route('login'));
});

test('payment factory paid state works', function () {
    $payment = Payment::factory()->paid()->create();

    expect($payment->status)->toBe(PaymentStatus::PAID)
        ->and($payment->paid_at)->not->toBeNull()
        ->and($payment->payment_method)->not->toBeNull();
});

test('payment factory overdue state works', function () {
    $payment = Payment::factory()->overdue()->create();

    expect($payment->status)->toBe(PaymentStatus::OVERDUE)
        ->and($payment->due_date->isPast())->toBeTrue();
});

test('payment belongs to client', function () {
    $payment = Payment::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    expect($payment->client)->toBeInstanceOf(Client::class)
        ->and($payment->client->id)->toBe($this->client->id);
});

test('payment belongs to plan', function () {
    $payment = Payment::factory()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
    ]);

    expect($payment->plan)->toBeInstanceOf(Plan::class)
        ->and($payment->plan->id)->toBe($this->plan->id);
});

test('payment resource returns correct structure', function () {
    $payment = Payment::factory()->paid()->create([
        'client_id' => $this->client->id,
        'plan_id' => $this->plan->id,
        'notes' => 'Some notes',
    ]);
    $payment->load(['client.user', 'plan']);

    $resource = (new PaymentResource($payment))->resolve();

    expect($resource)
        ->toHaveKey('id', $payment->id)
        ->toHaveKey('client_id', $this->client->id)
        ->toHaveKey('amount')
        ->toHaveKey('status', PaymentStatus::PAID)
        ->toHaveKey('notes', 'Some notes')
        ->toHaveKey('client')
        ->toHaveKey('plan');
});
