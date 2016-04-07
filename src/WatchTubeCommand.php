<?php
/**
 * Created by PhpStorm.
 * User: piet
 * Date: 24-9-15
 * Time: 9:25
 */

namespace Bureaupieper\StoreeWorker;

use Pheanstalk\Pheanstalk;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchTubeCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('worker:watch-tube')
            ->setDescription('Watch a tube, and pass the payload to another program.')
            ->addArgument(
                'tube',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'cmd',
                InputArgument::REQUIRED
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);

        $tube = $input->getArgument('tube');
        $cmd = $input->getArgument('cmd');

        /** @var Pheanstalk $client */
        $client = $this->container->get('pheanstalk');

        while(true)
        {
            $job = $client
                ->watch($tube)
                ->ignore('default')
                ->reserve();

            $exec = escapeshellcmd(str_replace('%payload%', $job->getData(), $cmd));

            $output->writeln("<comment>$exec</comment>");

            $commandOutput = [];
            exec($exec, $commandOutput, $code);

            $t = date("Y-m-d H:i:s");

            if ($code) {
                // something went wrong, output and bury it
                $output->writeln(sprintf("$t <error>Exitcode %s for job %s, burying.</error> %s", $code, $job->getId(), implode(' ', $commandOutput)));
                $client->bury($job);
            }
            else {
                $output->writeln(sprintf("$t <info>Success!</info> %s", $commandOutput ? implode("\n", $commandOutput) : ''));
                $client->delete($job);
            }
        }
    }
}