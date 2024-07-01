<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Illuminate\Support\Facades\Storage;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    protected function getEnvironmentSetUp($app)
    {
    }
}
