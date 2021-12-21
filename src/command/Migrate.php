<?php


namespace Eadmin\command;


use Phinx\Migration\MigrationInterface;
use think\console\Input;
use think\console\input\Option as InputOption;
use think\console\Output;
use think\facade\Log;
use think\migration\command\migrate\Run;

class Migrate extends Run
{
    protected $path;
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('migrate:eadmin')
            ->setDescription('Migrate the database 指定目录')
            ->addArgument('cmd', 1, "run or rollback")
            ->addArgument('path', 1, "数据库迁移目录")
            ->addOption('--force', '-f', InputOption::VALUE_NONE, 'Force rollback to ignore breakpoints')
            ->addOption('--target', '-t', InputOption::VALUE_REQUIRED, 'The version number to migrate to')
            ->addOption('--date', '-d', InputOption::VALUE_REQUIRED, 'The date to migrate to');
    }
    /**
     * Migrate the database.
     *
     * @param Input  $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $cmd = $input->getArgument('cmd');
        $this->path = $input->getArgument('path');
        $version = $input->getOption('target');
        $date    = $input->getOption('date');
        $force   = !!$input->getOption('force');
        if($cmd == 'run'){
            // run the migrations
            $start = microtime(true);
            if (null !== $date) {
                $this->migrateToDateTime(new \DateTime($date));
            } else {
                $this->migrate($version);
            }
            $end = microtime(true);

            $output->writeln('');
            $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
        }else{
            // rollback the specified environment
            $start = microtime(true);
            if (null !== $date) {
                $this->rollbackToDateTime(new \DateTime($date), $force);
            } else {
                $this->rollback($version, $force);
            }
            $end = microtime(true);
            $output->writeln('');
            $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
        }

    }
    protected function getPath()
    {
        return $this->path;
    }
    protected function rollback($version = null, $force = false)
    {
        $migrations = $this->getMigrations();

        ksort($migrations);

        foreach ($migrations as $key=>$migration) {
            Log::error('数据:'.$key);

            $this->executeMigration($migration, MigrationInterface::DOWN);
        }
    }

    protected function rollbackToDateTime(\DateTime $dateTime, $force = false)
    {
        $versions   = $this->getVersions();
        $dateString = $dateTime->format('YmdHis');
        sort($versions);

        $earlierVersion      = null;
        $availableMigrations = array_filter($versions, function ($version) use ($dateString, &$earlierVersion) {
            if ($version <= $dateString) {
                $earlierVersion = $version;
            }
            return $version >= $dateString;
        });

        if (count($availableMigrations) > 0) {
            if (is_null($earlierVersion)) {
                $this->output->writeln('Rolling back all migrations');
                $migration = 0;
            } else {
                $this->output->writeln('Rolling back to version ' . $earlierVersion);
                $migration = $earlierVersion;
            }
            $this->rollback($migration, $force);
        }
    }
}
