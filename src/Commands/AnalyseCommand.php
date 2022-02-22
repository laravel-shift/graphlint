<?php

namespace Olivernybroe\Graphlint\Commands;

use GraphQL\Language\Printer;
use Olivernybroe\Graphlint\Analyser\Analyser;
use Olivernybroe\Graphlint\Configuration;
use Olivernybroe\Graphlint\Kernel;
use Olivernybroe\Graphlint\Utils\NodeNameResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;

class AnalyseCommand extends Command
{
    private const ORIGINAL_SCHEMA = 'sdl';
    private const COMPILED_SCHEMA = 'compiled_schema';

    protected static $defaultName = 'analyse';

    protected static $defaultDescription = 'Analyses a graphql file';

    protected function configure()
    {
        $this->addArgument(
            self::COMPILED_SCHEMA,
            InputArgument::REQUIRED,
            'The compiled schema definition language.'
        );
        $this->addArgument(
            self::ORIGINAL_SCHEMA,
            InputArgument::OPTIONAL,
            'The original schema definition language.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configurationFile = getcwd() . DIRECTORY_SEPARATOR . 'graphlint.php';

        $kernel = new Kernel([
            $configurationFile,
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();


        $style = new SymfonyStyle(
            $input,
            $output
        );

        /** @var Analyser $analyser */
        $analyser = $container->get(Analyser::class);

        $originalSchema = $input->getArgument(self::ORIGINAL_SCHEMA);
        $compiledSchema = $input->getArgument(self::COMPILED_SCHEMA);

        $style->info("Analysing schema...");
        $result = $analyser->analyse($originalSchema);

        $problems = $result->getProblemsHolder()->getProblems();
        $problemsAmount = count($problems);

        if ($problemsAmount === 0) {
            $style->success("No problems found!");
            return self::SUCCESS;
        }

        $style->error("Found $problemsAmount problems");

        // Print out which inspections affected the schema.
        $style->writeln('<options=underscore>Applied inspections:</>');
        $style->listing($result->getAffectedInspections()->getInspections());

        foreach ($problems as $problemDescriptor) {
            $problemDescriptor->getFix()->fix($problemDescriptor);
        }

        $changedNode = Printer::doPrint($result->getDocumentNode());
        $originalNode = Printer::doPrint($result->getOriginalDocumentNode());

        /** @var ConsoleDiffer $differ */
        $differ = $container->get(ConsoleDiffer::class);
        $style->writeln($differ->diff(
            $originalNode,
            $changedNode,
        ));

        return self::FAILURE;
    }

}