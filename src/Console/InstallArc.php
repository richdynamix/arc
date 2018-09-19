<?php

namespace Richdynamix\Arc\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallArc extends Command
{
    const PACKAGES = [
        'barryvdh/laravel-ide-helper' => 'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
        'ellipsesynergie/api-response' => '',
        'genealabs/laravel-model-caching' => '',
        'lord/laroute' => 'Lord\Laroute\LarouteServiceProvider',
        'laravel/horizon' => 'Laravel\Horizon\HorizonServiceProvider',
    ];

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
    protected $description = 'Install Arc package dependencies';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('The following packages are suggested as development aids. Arc can install and configure these for you.');

        $questions = $this->verifyPackageInstalls();
        $this->disaplyConfirmation($questions);

        if ($questions) {
            $this->info('Installing...');
            $this->runComposerInstall($this->getComposerCommand($questions));
        }

        $this->publishConfigs($questions);
        $this->configureEnv($questions);
    }

    /**
     * @param $questions
     * @return string
     */
    private function getComposerCommand($questions): string
    {
        $composer = 'composer require ';
        $cmd = '';
        foreach ($questions as $question) {
            if (strtolower($question['status']) === 'install') {
                $cmd .= $question['package'] . ' ';
            }
        }

        if (!empty($cmd)) {
            return $composer.$cmd;
        }

        return '';
    }

    /**
     * @param $cmd
     */
    private function runComposerInstall($cmd): void
    {
        if ($cmd) {
            $process = new Process($cmd, null, array_merge($_SERVER, $_ENV), null, null);
            $process->run(function ($type, $line) {
                $this->line($line);
            });
        }
    }

    /**
     * @return array
     */
    private function verifyPackageInstalls(): array
    {
        $questions = [];
        $item = 0;
        foreach (self::PACKAGES as $package => $provider) {
            $questions[$item]['package'] = $package;
            $questions[$item]['provider'] = $provider;
            $status = $this->confirm("Would you like to install $package?") ? 'Install' : 'Not Installed';
            $questions[$item]['status'] = $status;
            ++$item;
        }
        return $questions;
    }

    /**
     * @param $questions
     */
    private function disaplyConfirmation($questions): void
    {
        $headers = ['Package', 'Provider', 'Status'];
        $this->table($headers, $questions);
    }

    /**
     * @param $questions
     */
    private function publishConfigs($questions): void
    {
        $this->info('Publishing Config...');
        $this->call('vendor:publish', ['--provider' => 'Richdynamix\Arc\ArcServiceProvider']);

        foreach ($questions as $question) {
            if ($question['provider']) {
                $this->call('vendor:publish', ['--provider' => $question['provider']]);
            }
        }
    }

    private function configureEnv($questions)
    {
        $packages = collect($questions);
        $env = file_get_contents(base_path('tools/docker/usr/local/share/env/20-arc-env'));

        $horizon = $packages->where('package', 'laravel/horizon')->first();
        if ($horizon['status'] === 'Install') {
            $oldHorizonConfig = 'START_HORIZON=${START_HORIZON:-false}';
            $newHorizonConfig = 'START_HORIZON=${START_HORIZON:-true}';
            $newEnv = str_replace($oldHorizonConfig, $newHorizonConfig, $env);

            $oldQueueConfig = 'START_QUEUE=${START_QUEUE:-true}';
            $newQueueConfig = 'START_QUEUE=${START_QUEUE:-false}';
            $newEnv = str_replace($oldQueueConfig, $newQueueConfig, $newEnv);

            file_put_contents(base_path('tools/docker/usr/local/share/env/20-arc-env'), $newEnv);
        }
    }
}
