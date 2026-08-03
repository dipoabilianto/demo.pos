<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\FixSQLiteTransactionLeak;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, FixSQLiteTransactionLeak {
        FixSQLiteTransactionLeak::refreshTestDatabase insteadof RefreshDatabase;
    }
}
