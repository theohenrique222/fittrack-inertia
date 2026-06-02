<?php

use App\Actions\BodyMeasurements\BodyMetricsCalculator;
use App\Enums\ActivityLevel;
use App\Enums\Goal;
use App\Models\BodyMeasurement;
use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'gender' => 'male',
        'birthdate' => '1990-06-15',
    ]);

    $this->client = Client::factory()->create([
        'user_id' => $this->user,
    ]);

    $this->measurement = BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 80,
        'height' => 180,
        'neck' => 38,
        'waist' => 85,
        'hip' => 100,
        'activity_level' => ActivityLevel::Moderate,
        'goal' => Goal::Maintain,
    ]);
});

test('calculates BMI correctly', function () {
    $calculator = new BodyMetricsCalculator($this->measurement, $this->user);
    $result = $calculator->calculate();

    $expectedBmi = 80 / (1.8 * 1.8);

    expect($result['bmi']['value'])->toBe(round($expectedBmi, 1));
    expect($result['bmi']['classification'])->toBe('Peso normal');
});

test('classifies underweight BMI', function () {
    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 50,
        'height' => 180,
        'activity_level' => ActivityLevel::Sedentary,
        'goal' => Goal::Gain,
    ]);

    $calculator = new BodyMetricsCalculator($measurement, $this->user);
    $result = $calculator->calculate();

    expect($result['bmi']['classification'])->toBe('Abaixo do peso');
});

test('classifies overweight BMI', function () {
    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 84,
        'height' => 170,
        'activity_level' => ActivityLevel::Sedentary,
        'goal' => Goal::Lose,
    ]);

    $calculator = new BodyMetricsCalculator($measurement, $this->user);
    $result = $calculator->calculate();

    expect($result['bmi']['classification'])->toBe('Sobrepeso');
});

test('calculates male body fat using US Navy method', function () {
    $calculator = new BodyMetricsCalculator($this->measurement, $this->user);
    $result = $calculator->calculate();

    expect($result['body_fat']['value'])->toBeGreaterThan(0);
    expect($result['body_fat']['value'])->toBeLessThan(50);
});

test('calculates female body fat using US Navy method', function () {
    $femaleUser = User::factory()->create([
        'gender' => 'female',
        'birthdate' => '1995-05-15',
    ]);

    $femaleClient = Client::factory()->create([
        'user_id' => $femaleUser,
    ]);

    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $femaleClient->id,
        'weight' => 55,
        'height' => 170,
        'neck' => 34,
        'waist' => 62,
        'hip' => 82,
        'activity_level' => ActivityLevel::Moderate,
        'goal' => Goal::Maintain,
    ]);

    $calculator = new BodyMetricsCalculator($measurement, $femaleUser);
    $result = $calculator->calculate();

    expect($result['body_fat']['value'])->toBeGreaterThan(0);
    expect($result['body_fat']['value'])->toBeLessThan(40);
});

test('returns zero body fat when neck and waist are missing', function () {
    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 80,
        'height' => 180,
        'neck' => null,
        'waist' => null,
        'activity_level' => ActivityLevel::Sedentary,
        'goal' => Goal::Maintain,
    ]);

    $calculator = new BodyMetricsCalculator($measurement, $this->user);
    $result = $calculator->calculate();

    expect($result['body_fat']['value'])->toBe(0.0);
    expect($result['body_fat']['classification'])->toBe('Não calculado');
});

test('calculates BMR using Mifflin-St Jeor for male', function () {
    $calculator = new BodyMetricsCalculator($this->measurement, $this->user);
    $result = $calculator->calculate();

    $expectedBmr = 10 * 80 + 6.25 * 180 - 5 * 35 + 5;

    expect($result['bmr'])->toBe(round($expectedBmr));
});

test('calculates BMR using Mifflin-St Jeor for female', function () {
    $femaleUser = User::factory()->create([
        'gender' => 'female',
        'birthdate' => '1995-05-15',
    ]);

    $femaleClient = Client::factory()->create([
        'user_id' => $femaleUser,
    ]);

    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $femaleClient->id,
        'weight' => 65,
        'height' => 165,
        'activity_level' => ActivityLevel::Moderate,
        'goal' => Goal::Maintain,
    ]);

    $calculator = new BodyMetricsCalculator($measurement, $femaleUser);
    $result = $calculator->calculate();

    $expectedBmr = 10 * 65 + 6.25 * 165 - 5 * 31 - 161;

    expect($result['bmr'])->toBe(round($expectedBmr));
});

test('adjusts TDEE for weight loss goal', function () {
    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 80,
        'height' => 180,
        'activity_level' => ActivityLevel::Sedentary,
        'goal' => Goal::Lose,
    ]);

    $calculator = new BodyMetricsCalculator($measurement, $this->user);
    $result = $calculator->calculate();

    $bmr = 10 * 80 + 6.25 * 180 - 5 * 35 + 5;
    $expectedTdee = ($bmr * 1.2) - 500;

    expect($result['macros']['calories'])->toBe(round($expectedTdee));
});

test('adjusts TDEE for weight gain goal', function () {
    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 60,
        'height' => 175,
        'activity_level' => ActivityLevel::Active,
        'goal' => Goal::Gain,
    ]);

    $calculator = new BodyMetricsCalculator($measurement, $this->user);
    $result = $calculator->calculate();

    $bmr = 10 * 60 + 6.25 * 175 - 5 * 35 + 5;
    $expectedTdee = ($bmr * 1.725) + 300;

    expect($result['macros']['calories'])->toBe(round($expectedTdee));
});

test('calculates daily water intake', function () {
    $calculator = new BodyMetricsCalculator($this->measurement, $this->user);
    $result = $calculator->calculate();

    expect($result['daily_water'])->toBe(round(80 * 35));
});

test('throws exception when user lacks gender', function () {
    $incompleteUser = User::factory()->create([
        'gender' => null,
        'birthdate' => '1990-01-01',
    ]);

    $incompleteClient = Client::factory()->create([
        'user_id' => $incompleteUser,
    ]);

    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $incompleteClient->id,
    ]);

    expect(fn () => new BodyMetricsCalculator($measurement, $incompleteUser))
        ->toThrow(RuntimeException::class, 'Usuário precisa ter gênero e data de nascimento');
});

test('throws exception when user lacks birthdate', function () {
    $incompleteUser = User::factory()->create([
        'gender' => 'male',
        'birthdate' => null,
    ]);

    $incompleteClient = Client::factory()->create([
        'user_id' => $incompleteUser,
    ]);

    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $incompleteClient->id,
    ]);

    expect(fn () => new BodyMetricsCalculator($measurement, $incompleteUser))
        ->toThrow(RuntimeException::class, 'Usuário precisa ter gênero e data de nascimento');
});

test('calculates lean mass correctly', function () {
    $calculator = new BodyMetricsCalculator($this->measurement, $this->user);
    $result = $calculator->calculate();

    $expectedLeanMass = round(80 * (1 - $result['body_fat']['value'] / 100), 1);

    expect($result['lean_mass'])->toBe($expectedLeanMass);
});

test('returns zero lean mass when body fat is zero', function () {
    $measurement = BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 80,
        'height' => 180,
        'neck' => null,
        'waist' => null,
        'activity_level' => ActivityLevel::Moderate,
        'goal' => Goal::Maintain,
    ]);

    $calculator = new BodyMetricsCalculator($measurement, $this->user);
    $result = $calculator->calculate();

    expect($result['lean_mass'])->toBe(0.0);
    expect($result['fat_mass'])->toBe(80.0);
});

test('includes all macro keys', function () {
    $calculator = new BodyMetricsCalculator($this->measurement, $this->user);
    $result = $calculator->calculate();

    expect($result['macros'])->toHaveKeys(['calories', 'protein', 'carbs', 'fat']);
    expect($result['macros']['protein'])->toHaveKeys(['grams', 'percentage']);
    expect($result['macros']['carbs'])->toHaveKeys(['grams', 'percentage']);
    expect($result['macros']['fat'])->toHaveKeys(['grams', 'percentage']);
});
