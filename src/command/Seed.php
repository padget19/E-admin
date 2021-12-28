<?php


namespace Eadmin\command;


use think\console\Input;
use think\console\input\Option as InputOption;
use think\console\Output;
use think\migration\command\seed\Run;

class Seed extends Run
{
    protected $path;
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('seed:eadmin')
            ->addArgument('path', 1, "数据库填充目录")
            ->setDescription('Run database seeders')
            ->addOption('--seed', '-s', InputOption::VALUE_REQUIRED, 'What is the name of the seeder?')
            ->setHelp(<<<EOT
                The <info>seed:run</info> command runs all available or individual seeders

<info>php think seed:run</info>
<info>php think seed:run -s UserSeeder</info>
<info>php think seed:run -v</info>

EOT
            );
    }
    protected function getPath()
    {
        return $this->path;
    }
    /**
     * Run database seeders.
     *
     * @param Input  $input
     * @param Output $output
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $seed = $input->getOption('seed');
        $this->path = $input->getArgument('path');
        $this->seeds = null;
        // run the seed(ers)
        $start = microtime(true);
        $this->seed($seed);
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }
}
