<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class GeneralClearDataCommand extends CommonCommand {
    protected function configure() {
        $this->setName('general:clearData');
        $this->setDescription('Clear all the data (DB and Files)');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $header_style = new OutputFormatterStyle('white', 'green', array('bold'));
        $output->getFormatter()->setStyle('header', $header_style);

        // RESET THE DATABASE
        $output->writeln('Reset the database');
        $output->writeln('Running...');
        $output->writeln('');

        $command = $input->getOption('in-box') ? '/vagrant/console mysql:reset -f' : './console mysql:reset -b -f';
        $this->runProcess($command, true, true);

        $output->writeln('');
        $output->writeln('<header>Success !</header>');

        // CLEAR ALL FILE DIRECTORIES
        $output->writeln('Clear all file directories');
        $output->writeln('Running...');
        $output->writeln('');

        $command = $input->getOption('in-box') ? '/vagrant/console files:clearDirectory -f' : './console files:clearDirectory -f';
        $this->runProcess($command, true, true);

        $output->writeln('');
        $output->writeln('<header>Success !</header>');
    }
}