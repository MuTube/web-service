<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class MysqlExecCommand extends CommonCommand {
    protected function configure() {
        $this->setName('mysql:exec');
        $this->setDescription('Execute a command in the db');
        $this->addArgument('query', InputArgument::REQUIRED, 'The query to execute');
        $this->addOption('is-file', 'f', InputOption::VALUE_NONE, 'Run this input as sql file path');
        $this->addOption('no-db', null, InputOption::VALUE_NONE, 'Run this query without selecting the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $header_style = new OutputFormatterStyle('white', 'green', array('bold'));
        $output->getFormatter()->setStyle('header', $header_style);
        $query = $input->getArgument('query');

        if($input->getOption('is-file')) {
            if(!file_exists($query)) throw new RuntimeException('Sql file doesn\'t exist...');
            $command = $this->getMysqlCommand($query, $input->getOption('no-db'), true);
        }
        else {
            $command = $this->getMysqlCommand($query, $input->getOption('no-db'));
        }

        $output->writeln('Command : '.$command);
        $output->writeln('Running...');
        $output->writeln('');

        $result = $this->runProcess($command, true, true, ConfigHelper::getDbConfig()['db_pswd']);

        $output->writeln('');
        $output->writeln("<header>Success !</header>");
        $output->writeln('');

        if($result) {
            $output->writeln('Result :');
            $output->write($result);
        }
    }
}