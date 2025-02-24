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

final class DiffCommand extends Command
{
    public function __construct(private ManagerRegistry $registry, private readonly string $emName)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bnza:job-manager:migrations:diff')
            ->setDescription(
                'Proxy to launch doctrine:migrations:migrate command as it would require a "configuration" option, and we can\'t define em/connection in config.'
            )
            ->addOption(
                'em',
                null,
                InputOption::VALUE_OPTIONAL,
                'Name of the Entity Manager to handle.',
                $this->emName
            )
            ->addOption(
                'from-empty-schema',
                null,
                InputOption::VALUE_NONE,
                'From empty schema'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->emName);
        $newInput = new ArrayInput([
            '--from-empty-schema' => $input->getOption('from-empty-schema'),
        ]);
        $newInput->setInteractive($input->isInteractive());
        $otherCommand = new \Doctrine\Migrations\Tools\Console\Command\DiffCommand(
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
