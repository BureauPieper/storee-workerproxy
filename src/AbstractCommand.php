<?php
/**
 * Created by PhpStorm.
 * User: piet
 * Date: 24-9-15
 * Time: 9:25
 */

namespace Bureaupieper\StoreeWorker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AbstractCommand extends Command
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function setContainer(ContainerBuilder $container = null)
    {
        $this->container = $container;
        return $this;
    }
}