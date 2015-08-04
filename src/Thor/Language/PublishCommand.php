<?php

namespace Thor\Language;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PublishCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lang:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a package's lang files to the application";

    /**
     * The lang publisher instance.
     *
     * @var Publisher
     */
    protected $lang;

    /**
     * Create a new lang publish command instance.
     *
     * @param  Publisher  $lang
     * @return void
     */
    public function __construct(Publisher $lang)
    {
        parent::__construct();

        $this->lang = $lang;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $package = $this->input->getArgument('package');
        $namespace = $this->input->getArgument('namespace');

        $this->lang->publish($package, $namespace, $this->getPath());

        $this->output->writeln('<info>Lang files published for package:</info> ' . $package);
    }

    /**
     * Get the specified path to the files.
     *
     * @return string
     */
    protected function getPath()
    {
        $path = $this->input->getOption('path');

        if(!is_null($path)) {
            return $this->laravel['path.base'] . '/' . $path;
        }
        return null;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('package', InputArgument::REQUIRED, 'The name of the package being published.'),
            array('namespace', InputArgument::OPTIONAL, 'The name of the namespace of the package being published.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('path', null, InputOption::VALUE_OPTIONAL, 'The path to the lang files.', null),
        );
    }

}
