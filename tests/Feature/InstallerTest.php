<?php

namespace Tests\Feature;

use App\Support\Installer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    use RefreshDatabase;

    private ?string $lockBackup = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Simuler un environnement non installé (le verrou de dev est mis de côté).
        if (File::exists(Installer::lockPath())) {
            $this->lockBackup = File::get(Installer::lockPath());
            File::delete(Installer::lockPath());
        }
    }

    protected function tearDown(): void
    {
        if ($this->lockBackup !== null) {
            File::put(Installer::lockPath(), $this->lockBackup);
        }

        parent::tearDown();
    }

    public function test_install_wizard_is_reachable_when_not_installed(): void
    {
        $this->get('/install')->assertOk()->assertSee('Bienvenue');
        $this->get('/install/configure')->assertOk()->assertSee('Installer BePack');
    }

    public function test_requirements_helper_reports_php_version(): void
    {
        $reqs = Installer::requirements();
        $this->assertNotEmpty($reqs);
        $this->assertSame('PHP ≥ 8.2', $reqs[0][0]);
        $this->assertTrue($reqs[0][1]);
    }
}
