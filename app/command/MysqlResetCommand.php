<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class MysqlResetCommand extends CommonCommand {
    protected function configure() {
        $this->setName('mysql:reset');
        $this->setDescription('Reset the database to the base state');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Resets completely the database (doesn\'t restore the last dump)');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $header_style = new OutputFormatterStyle('white', 'green', array('bold'));
        $output->getFormatter()->setStyle('header', $header_style);
        $dbConfig = ConfigHelper::getDbConfig();

        // DROP DB
        $output->writeln('Drop database "'.$dbConfig['db_name'].'" if exists');
        $output->writeln('Running...');
        $output->writeln('');

        $this->runProcess("./console mysql:exec 'DROP DATABASE IF EXISTS ".$dbConfig['db_name']."' --no-db", true, true, $dbConfig['db_pswd'], false);

        $output->writeln('');
        $output->writeln('<header>Success !</header>');

        // CREATE DB
        $output->writeln('Create database "'.$dbConfig['db_name'].'"');
        $output->writeln('Running...');
        $output->writeln('');

        $this->runProcess("./console mysql:exec 'CREATE DATABASE ".$dbConfig['db_name']."' --no-db", true, true, $dbConfig['db_pswd'], false);

        $output->writeln('');
        $output->writeln('<header>Success !</header>');

        // RUN BASEDB.SQL FILE
        $output->writeln('Restore base database archive');
        $output->writeln('Running...');
        $output->writeln('');

        $this->runProcess("./console mysql:exec -f 'config/database/baseDb.sql'", true, true, $dbConfig['db_pswd'], false);

        $output->writeln('');
        $output->writeln('<header>Success !</header>');

        // RUN LAST ARCHIVE
        $output->writeln('Restore last database archive');
        $output->writeln('Running...');
        $output->writeln('');

        $file = $input->getOption('force') ? '0_default.sql' : FileManager::getLastDbArchive();

        if($file) {
            $this->runProcess("./console mysql:exec -f 'config/database/archive/".$file."'", true, true, $dbConfig['db_pswd'], false);
        }
        else {
            $output->writeln('No Database archive found');
        }

        $output->writeln('');
        $output->writeln('<header>Success !</header>');
    }
}