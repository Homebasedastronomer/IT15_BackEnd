<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class HttpsEnforcementTest extends TestCase
{
    public function test_forces_https_url_generation_when_enabled(): void
    {
        URL::forceScheme(null);
        config()->set('app.force_https', true);

        (new AppServiceProvider($this->app))->boot();

        $url = route('home');

        $this->assertStringStartsWith('https://', $url);
    }
}
