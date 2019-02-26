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

        $this->info('Cleaning up and removing Arc...');
        $this->runComposer( 'composer remove richdynamix/arc --ignore-platform-reqs');
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
}
