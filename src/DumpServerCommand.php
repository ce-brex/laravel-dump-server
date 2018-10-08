<?php

namespace ClintonElectronics\DumpServer;

use LaravelZero\Framework\Commands\Command;

use InvalidArgumentException;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Server\DumpServer;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;

class DumpServerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dump-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the dump server to collect dump information.';

    /**
     * The Dump server.
     *
     * @var \Symfony\Component\VarDumper\Server\DumpServer
     */
    private $server;

    /**
     * DumpServerCommand constructor.
     *
     * @param  \Symfony\Component\VarDumper\Server\DumpServer  $server
     * @return void
     */
    public function __construct(DumpServer $server)
    {
        $this->server = $server;

        parent::__construct();
    }

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle(): void
    {
        $descriptor = new CliDescriptor(new CliDumper);

        $io = new SymfonyStyle($this->input, $this->output);

        $errorIo = $io->getErrorStyle();
        $errorIo->title('Laravel Zero Var Dump Server');

        $this->server->start();

        $errorIo->success(sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $this->server->listen(function (Data $data, array $context, int $clientId) use ($descriptor, $io) {
            $descriptor->describe($io, $data, $context, $clientId);
        });
    }
}
