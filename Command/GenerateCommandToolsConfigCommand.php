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


        $raw = $this->commandsAsXml($name, $alias, $commands);

        $output->writeln($raw, OutputInterface::OUTPUT_RAW);
    }


    private function commandsAsXml($name, $alias, $commands)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $dom->appendChild($frameworkNode = $dom->createElement('framework'));

        $frameworkNode->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $frameworkNode->setAttribute('xsi:noNamespaceSchemaLocation', 'schemas/frameworkDescriptionVersion1.1.xsd');
        $frameworkNode->setAttribute('name', $name);
        $frameworkNode->setAttribute('invoke', 'app/console');
        $frameworkNode->setAttribute('alias', $alias);
        $frameworkNode->setAttribute('enabled', true);
        $frameworkNode->setAttribute('version', 1);


        /** @var $command \Symfony\Component\Console\Command\Command */
        foreach ($commands as $command) {
            $frameworkNode->appendChild($commandXml = $dom->createElement('command'));

            $commandXml->appendChild($dom->createElement('name', $command->getName()));
            $commandXml->appendChild($dom->createElement('help', $command->getHelp()));
            $commandXml->appendChild($dom->createElement('params', $this->getCommandParams($command)));
        }

        return $dom->saveXml();
    }


    private function getCommandParams(Command $command)
    {
        $elements = array();

        $definition = $command->getDefinition();

        foreach ($definition->getArguments() as $argument) {
            /** @var $option \Symfony\Component\Console\Input\Inputargument */

            $elements[] = sprintf($argument->isRequired() ? '%s'
                                          : '[%s[="%s"]]', $argument->getName(), $argument->getDefault());
        }

        foreach ($definition->getOptions() as $option) {
            /** @var $option \Symfony\Component\Console\Input\Inputoption */

            $shortcut = $option->getshortcut() ? sprintf('-%s|', $option->getshortcut()) : '';
            $elements[] = sprintf('[%s--%s[="%s"]]', $shortcut, $option->getname(), $option->getdefault());
        }

        return implode(' ', $elements);
    }


}
