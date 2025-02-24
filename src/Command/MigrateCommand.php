<?php

declare(strict_types=1);

namespace Bnza\JobManagerBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\YamlFile;
use Doctrine\Migrations\DependencyFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MigrateCommand extends Command
{
    public function __construct(private ManagerRegistry $registry, private readonly string $emName)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bnza:job-manager:migrations:migrate')
            ->setDescription(
                'Proxy to launch doctrine:migrations:migrate command as it would require a "configuration" option, and we can\'t define em/connection in config.'
            )
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.',
                'latest'
            )
            ->addOption(
                'em',
                null,
                InputOption::VALUE_OPTIONAL,
                'Name of the Entity Manager to handle.',
                $this->emName
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the migration as a dry run.')
            ->addOption('query-time', null, InputOption::VALUE_NONE, 'Time all the queries individually.')
            ->addOption(
                'allow-no-migration',
                null,
                InputOption::VALUE_NONE,
                'Don\'t throw an exception if no migration is available (CI).'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newInput = new ArrayInput([
            'version' => $input->getArgument('version'),
            '--dry-run' => $input->getOption('dry-run'),
            '--query-time' => $input->getOption('query-time'),
            '--allow-no-migration' => $input->getOption('allow-no-migration'),
        ]);
        $newInput->setInteractive($input->isInteractive());
        $otherCommand = new \Doctrine\Migrations\Tools\Console\Command\MigrateCommand(
            $this->getDependencyFactory($input)
        );
        $otherCommand->run($newInput, $output);

        return 0;
    }

    private function getDependencyFactory(InputInterface $input): DependencyFactory
    {
        $em = $this->registry->getManager($input->getOption('em'));
        $config = new YamlFile(__DIR__.'/../../config/doctrine_migrations.yaml');

        return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($em));
    }
}
