<?php
/**
 * Copyright © NTuanGiang Assurance, Inc. All rights reserved.
 */

namespace NTuanGiang\Assurance\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Assurance extends \Symfony\Component\Console\Command\Command
{
    const INSTANCE = 'instance';

    const INSTANCE_SHORTCUT = 'i';

    const METHOD = 'method';

    const METHOD_SHORTCUT = 'm';

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $appState,
        string $name = 'ntuangiang:assurance'
    )
    {
        $this->_objectManager = $objectManager;
        $this->_appState = $appState;
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->_appState->getAreaCode();
        } catch (\Exception $e) {
            $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        }

        $methodName = $input->getOption(self::METHOD);
        $instanceName = $input->getOption(self::INSTANCE);
        $instance = $this->_objectManager->create($instanceName);

        try {
            if ($methodName) {
                $instance->$methodName();
            } else {
                $instance->execute();
            }
        } catch (\Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");
        }
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $options = [
            new \Symfony\Component\Console\Input\InputOption(
                self::INSTANCE,
                self::INSTANCE_SHORTCUT,
                \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
                'Instance Name, \Vendor\Component'
            ),
            new \Symfony\Component\Console\Input\InputOption(
                self::METHOD,
                self::METHOD_SHORTCUT,
                \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
                'Method Name'
            ),
        ];

        $this->setDescription("Use bin/magento ntuangiang:assurance --instance='\Vendor\Component' --method='methodName'. Execute a cron and a command by hand.");
        $this->setDefinition($options);
        $this->setAliases(['ntg:as']);
        parent::configure();
    }
}
