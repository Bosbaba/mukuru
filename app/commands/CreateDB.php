<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CreateDBQueryCommand
 */
class CreateDB extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:create_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates a db and user by reading the config file. ';

    /**
     * @var string
     */
    protected $line = "==========================================================================================";

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The config file is read for the database configuration values.
     * Create the database user and user privileges
     *
     * @return mixed
     */
    public function fire()
    {
        // Get database configs
        $driver = Config::get('database.default');
        $configs = Config::get('database.connections.' . $driver);

        $this->info(sprintf("We will be creating a database and user with the following credentials:"));
        $this->comment($this->line);
        $this->comment(sprintf('HOSTNAME: %s;', $configs['host']));
        $this->comment(sprintf('DATABASE: %s;', $configs['database']));
        $this->comment(sprintf('DB USER: %s;', $configs['username']));
        $this->comment(sprintf('DB PASSWORD: %s;', $configs['password']));
        $this->comment($this->line);
        $this->comment('NOTE: You will be prompted for your Mysql root password!');
        $this->comment($this->line);

        if ($this->confirm("Do you wish to continue?[y/n]", 'n'))
        {
            $root_password      = $this->secret("What is your MySQL root password:");
            $db_create          = sprintf('CREATE DATABASE IF NOT EXISTS %s;', $configs['database']);
            $create_user        = sprintf('CREATE USER %s@%s IDENTIFIED BY \'%s\';', $configs['username'], $configs['host'], $configs['password']);
            $grand_privileges   = sprintf(
                                            'GRANT CREATE, DROP, DELETE, INSERT, SELECT, UPDATE, ALTER, INDEX ON %s.* TO %s@%s;',
                                            $configs['database'],
                                            $configs['username'],
                                            $configs['host']
                                        );
            $flush_privileges   = 'FLUSH PRIVILEGES;';

            $create_statement   = $db_create . $create_user . $grand_privileges . $flush_privileges;

            $this->comment('Creating database...');

            try
            {
                $db =   new \PDO(
                    "mysql:dbname=mysql;host={$configs['host']}",
                    'root',
                    $root_password,
                    [
                        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    ]
                );

                $stmt = $db->prepare($create_statement);
                $stmt->execute();
            }
            catch (\PDOException $e)
            {
                return $this->error($e->getMessage());
            }

            $this->comment($this->line);
            $this->info('Database and user successfully created.');
            $this->comment($this->line);

            $this->comment('Seeding database...');

            if (Schema::hasTable('migrations'))
            {
                return $this->error("Database already seeded");
            }

            $this->comment('Setting up migration table...');
            $this->call('migrate:install', []);

            $this->comment('Creating the database tables, by running the migrations');
            $this->call('migrate', []);

            $this->comment($this->line);
            $this->comment('Seeding the database tables');
            $this->call('db:seed');
        }
        else
        {
            $this->error('Exiting...');
            $this->error('You will not be able to continue without finishing this setup. You can re-run this command again to retry.');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'admin_db_user',
                InputArgument::OPTIONAL,
                'The database sure with db and user create privileges, default to "root"',
                'root'
            ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

}
