<?php

namespace PolPaymentPayolution\Command;

use Payolution\Migration\WorkflowMigrationInvoker;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to migrate the payolution orders
 *
 * @package PolPaymentPayolution\Command
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class WorkflowMigrationCommand extends ShopwareCommand
{
    /**
     * @var WorkflowMigrationInvoker
     */
    private $migrationInvoker;

    /**
     * WorkflowMigrationCommand constructor.
     *
     * @param WorkflowMigrationInvoker $migrationInvoker
     */
    public function __construct(WorkflowMigrationInvoker $migrationInvoker)
    {
        $this->migrationInvoker = $migrationInvoker;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pol:payment_payolution:migration')
            ->setDescription('Migrate old payolution orders to new workflow structure');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Payolution Migration Running since: ' . date_create_from_format('U', (string) time())->format('c'));

        $this->migrationInvoker->invokeMigration();

        $output->writeln('Payolution Migration Finished: ' . date_create_from_format('U', (string) time())->format('c'));
    }
}
