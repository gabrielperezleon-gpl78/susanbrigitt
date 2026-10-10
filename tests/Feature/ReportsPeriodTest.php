<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsPeriodTest extends TestCase
{
    use RefreshDatabase;

    public function test_named_period_ignores_dates_left_in_the_form(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-09 12:00:00', 'America/Caracas'));
        try {
            $this->actingAs(User::factory()->create())
                ->get(route('reports.index', [
                    'period' => 'this_month',
                    'date_from' => '2026-09-01',
                    'date_to' => '2026-09-30',
                ]))
                ->assertOk()
                ->assertSee('Resumen del 01/10/2026 al 09/10/2026.')
                ->assertDontSee('Resumen del 01/09/2026 al 30/09/2026.');
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_custom_period_requires_both_dates(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('reports.index', ['period' => 'custom']))
            ->assertSessionHasErrors(['date_from', 'date_to']);
    }
}
