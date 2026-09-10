<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class PosPagesTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testAllPosPagesAreAvailable(): void
    {
        $pages = [
            '/'          => "Run your records with confidence.",
            '/about'     => 'About the POS System',
            '/customers' => 'Customer Accounts',
            '/users'     => 'User Accounts',
        ];

        foreach ($pages as $route => $heading) {
            $result = $this->get($route);

            $result->assertStatus(200);
            $result->assertSee($heading);
        }
    }

    public function testCustomerAndUserRecordsAreRendered(): void
    {
        $customers = $this->get('/customers');
        $customers->assertSee('Kadeem Alford');
        $customers->assertSee('Naomi Lapaglia');

        $users = $this->get('/users');
        $users->assertSee('Rovic Bilbao');
        $users->assertSee('Jules Winnfield');
    }
}
