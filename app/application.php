<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class Application extends Symfony\Component\Console\Application {
    function __construct() {
        parent::__construct('Mutube-web-service Commands', '1.0');

        //include stuff
        include_once 'tool/helper/requireHelper.php';
        RequireHelper::requireAllRequiredFiles();

        //get commands from /app/command dir
        $this->loadDirectory('app/command');

        //add some global arguments or options
        $this->addGlobalOptionsAndArguments();
    }

    protected function loadDirectory($dir) {
        foreach(scandir('app/command') as $commandFileName) {
            if(strpos($commandFileName, '.') !== 0) {
                $filePath = $dir.'/'.$commandFileName;
                if(is_dir($filePath)) $this->loadDirectory($filePath);

                if(strpos($commandFileName, 'Command.php')) {
                    require_once $filePath;
                    list($className, $fileExtension) = explode('.php', $commandFileName);

                    if($commandFileName != 'CommonCommand.php') {
                        $this->add(new $className);
                    }
                }
            }
        }
    }

    protected function addGlobalOptionsAndArguments() {
        $this->getDefinition()->addOption(new InputOption('in-box', 'b', InputOption::VALUE_NONE, 'Run the command in the vagrant box'));
    }

    protected function configureIO(InputInterface $input, OutputInterface $output) {
        parent::configureIO($input, $output);
        $output->setDecorated(true);
    }
}