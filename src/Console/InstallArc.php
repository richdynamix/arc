<?php

namespace Richdynamix\Arc\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallArc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arc:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Arc';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Configuring your project...');
        $this->publishConfigs();
        if ($this->confirm('Would you like a Travis CI and K8s CD configuration?')) {
            return $this->withTravis();
        }

        return $this->withoutTravis();
    }

    /**
     * @param $cmd
     */
    private function runComposer($cmd): void
    {
        if ($cmd) {
            $process = new Process($cmd, null, array_merge($_SERVER, $_ENV), null, null);
            $process->run(function ($type, $line) {
                $this->line($line);
            });
        }
    }

    private function publishConfigs(): void
    {
        $this->info('Publishing Config...');
        $this->call('vendor:publish', ['--provider' => 'Richdynamix\Arc\ArcServiceProvider']);
    }

    private function withTravis()
    {
        $this->runComposer('composer require predis/predis --ignore-platform-reqs');

        unlink(base_path('docker-compose.yml'));
        copy(base_path('docker-compose-cicd.yml'), base_path('docker-compose.yml'));
        unlink(base_path('docker-compose-cicd.yml'));

        $this->info('Installing PHP CodeSniffer...');
        $this->runComposer('composer require squizlabs/php_codesniffer --ignore-platform-reqs --dev');

        $this->info('Installing PHP Mess Detector...');
        $this->runComposer('composer require phpmd/phpmd --ignore-platform-reqs --dev');

        $this->info('Installing PHPStan...');
        $this->runComposer('composer require phpstan/phpstan --ignore-platform-reqs --dev');

        $this->cleanUp();
    }

    private function withoutTravis()
    {
        $this->runComposer('composer require predis/predis --ignore-platform-reqs');

        unlink(base_path('docker-compose-cicd.yml'));

        $this->cleanUp();
    }

    private function cleanUp(): void
    {
        $this->info('Cleaning up and removing Arc...');
        $this->runComposer('composer remove richdynamix/arc --ignore-platform-reqs');
    }
}
