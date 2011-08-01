<?php

namespace Marphi\PhpStormSupportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Application;

/**
 * Generates command config
 *
 * @author Marcin Sikoń <marcin.sikon@gmail.com>
 */
class GenerateCommandToolsConfigCommand extends ContainerAwareCommand
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
                ->setDefinition(array(
                                     new InputOption('alias', 'a', InputOption::VALUE_OPTIONAL, 'The alias of command', 's'),
                                     new InputOption('name', '', InputOption::VALUE_OPTIONAL, 'The name of command', 'Symfony2')
                                ))
                ->setDescription('Generate config for PhpStorm Command Tool')
                ->setHelp(<<<EOT
The <info>phpstorm:command:config</info> command generate configuration for Command Line Tool Support

Generate and save file to default path <project>/.idea/commandlinetools/Symfony2.xml where `Symfony2` is name of command.

EOT
        )
                ->setName('phpstorm:command:config');
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When namespace doesn't end with Bundle
     * @throws \RuntimeException         When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = $this->getApplication()->all();
        $alias = $input->getOption('alias');
        $name = $input->getOption('name');

        $commandsArray = array();
        foreach ($commands as $command) {
            $commandsArray[] = array('name' => $command->getName(), 'help' => $command->getHelp(), 'params' => $this->getCommandParams($command));
        }


        /** @var $templating \Symfony\Component\Templating\EngineInterface */
        $templating = $this->getContainer()->get('templating');


        $raw = $templating->render('MarphiPhpStormSupportBundle::config.xml.twig', array('commands' => $commandsArray, 'name' => $name, 'alias' => $alias));




        $output->writeln($raw, OutputInterface::OUTPUT_RAW);
    }


    private function getCommandParams(Command $command)
    {
        $elements = array();

        $definition = $command->getDefinition();

        foreach ($definition->getArguments() as $argument) {
            /** @var $option \Symfony\Component\Console\Input\Inputargument */

            $elements[] = sprintf($argument->isRequired() ? '%s' : '[%s[="%s"]]', $argument->getName(), $argument->getDefault());
        }

        foreach ($definition->getOptions() as $option) {
            /** @var $option \Symfony\Component\Console\Input\Inputoption */
            
            $shortcut = $option->getshortcut() ? sprintf('-%s|', $option->getshortcut()) : '';
            $elements[] = sprintf('[%s--%s[="%s"]]', $shortcut, $option->getname(), $option->getdefault());
        }

        return implode(' ', $elements);
    }


}
