<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class MysqlDumpCommand extends CommonCommand {
    protected function configure() {
        $this->setName('mysql:dump');
        $this->setDescription('Create a dump of the current db');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $header_style = new OutputFormatterStyle('white', 'green', array('bold'));
        $output->getFormatter()->setStyle('header', $header_style);

        $output->writeln('Export current DB to config/database/archive');
        $output->writeln('Running...');
        $output->writeln('');

        $fileName = time().'_'.ConfigHelper::getDbConfig()['db_name'].'.sql';
        $dbConfig = ConfigHelper::getDbConfig();
        $command = vsprintf('mysqldump -t -u %s -p%s %s > config/database/archive/'.$fileName,
            [$dbConfig['db_user'], $dbConfig['db_pswd'], $dbConfig['db_name']]
        );

        $this->runProcess($command, true, true, false);

        $output->writeln('');
        $output->writeln('<header>Success !</header>');
    }
}