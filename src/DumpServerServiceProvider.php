<?php

namespace ClintonElectronics\DumpServer;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Server\Connection;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;

class DumpServerServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'debug-server');

        $this->app->singleton('command.dumpserver', DumpServerCommand::class);

        $this->commands('command.dumpserver');

        $host = $this->app['config']->get('debug-server.host');

        $this->app->when(DumpServer::class)->needs('$host')->give($host);

        $connection = new Connection($host, [
            'source' => new SourceContextProvider('utf-8', base_path()),
        ]);

        VarDumper::setHandler(function ($var) use ($connection) {
            (new Dumper($connection))->dump($var);
        });
    }
}
