<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class FilesClearDirectoryCommand extends CommonCommand {
    protected function configure() {
        $this->setName('files:clearDirectory');
        $this->setDescription('Clear the files directory (images, zips, ...)');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Clear really all the directories including user images, contact images and musics');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $header_style = new OutputFormatterStyle('white', 'green', array('bold'));
        $output->getFormatter()->setStyle('header', $header_style);

        $output->writeln('Clear directories');
        $output->writeln('Running...');
        $output->writeln('');

        $filesToClear = ['generatedImage', 'zip', 'contact_vcard'];

        if($input->getOption('force')) $filesToClear = array_merge($filesToClear, ['user_image']);

        foreach($filesToClear as $fileToClear) {
            $command = $input->getOption('in-box')
                ? 'rm -rf /vagrant/files/'.$fileToClear.'/*'
                : 'vagrant ssh --command "rm -rf /vagrant/files/'.$fileToClear.'/*"'
            ;

            $this->runProcess($command, true, true, false);
        }

        $output->writeln('');
        $output->writeln('<header>Success !</header>');
    }
}