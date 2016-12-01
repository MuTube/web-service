<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Process\Process;

abstract class CommonCommand extends Command {

    protected $input;
    protected $output;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    protected function getMysqlCommand($execQuery, $noDatabaseSelection = false, $isFile = false) {
        $config = ConfigHelper::getDbConfig();
        $password = isset($config['db_pswd']) ? $config['db_pswd'] : '';
        $passwordParam = (strlen($password) > 0) ? '-p'.$config['db_pswd'] : '';

        $query = 'mysql -h %s -u %s %s';
        $parameters = array($config['db_host'], $config['db_user'], $passwordParam);

        if (!$noDatabaseSelection) {
            $query .= ' %s';
            $parameters[] = $config['db_name'];
        }

        return vsprintf($query, $parameters) . ($isFile ? " < ".$execQuery : " --execute '".$execQuery."'");
    }

    public function runProcess($command, $liveOutput = true, $throwExceptions = true, $passwordToHide = '') {
        if($this->input->getOption('in-box')) {
            if($this->isInBox()) throw new \RuntimeException('You cannot run a command in the box if you are already in the box...');
            $command = sprintf('vagrant ssh --command "%s"', $command);
        }

        $formatter = $this->getHelper('formatter');

        if ($liveOutput) {
            $this->output->write($formatter->formatBlock('[exec]', 'info', false) . str_replace($passwordToHide, 'XXXXXX', $command) . ' ... ', true);
        }

        $process = new Process($command);
        $process->setTimeout(null);
        $process->run();

        if ($throwExceptions && !$process->isSuccessful() && $process->getErrorOutput()) throw new \RuntimeException($process->getErrorOutput());

        return $process->getOutput();
    }

    public function isInBox() {
        return strpos(__DIR__, '/vagrant/');
    }
}