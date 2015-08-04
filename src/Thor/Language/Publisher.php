<?php

namespace Thor\Language;

use Illuminate\Filesystem\Filesystem;

class Publisher
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The destination of the lang files.
     *
     * @var string
     */
    protected $publishPath;

    /**
     * Create a new lang publisher instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $publishPath
     * @return void
     */
    public function __construct(Filesystem $files, $publishPath)
    {
        $this->files = $files;
        $this->publishPath = $publishPath;
    }

    /**
     * Publish lang files from a given path.
     *
     * @param  string  $package
     * @param  string  $source
     * @param  string  $namespace
     * @return void
     */
    public function publish($package, $namespace = null, $source = null)
    {
        if(empty($namespace)) {
            $namespace = last(explode('/', $package));
        }

        if(empty($source)) {
            // First we will figure out the source of the package's lang location
            // which we do by convention. Once we have that we will move the files over
            // to the "main" lang directory for this particular application.
            $source = $this->getSource($package);

            return $this->publish($package, $namespace, $source);
        }

        $result = true;
        foreach($this->files->directories($source) as $i => $langdir) {
            $langfolder = basename($langdir);

            $destination = $this->publishPath . "/packages/{$langfolder}/{$namespace}";
            $this->makeDestination($destination);

            $result = $result && $this->files->copyDirectory($langdir, $destination);
        }

        return $result;
    }

    /**
     * Get the source lang directory to publish.
     *
     * @param  string  $package
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getSource($package)
    {
        $possiblePaths = array(
            base_path() . "/vendor/{$package}/src/lang",
            base_path() . "/src/lang",
        );

        foreach($possiblePaths as $source) {
            if($this->files->isDirectory($source)) {
                return $source;
            }
        }

        throw new \InvalidArgumentException("There are no lang files for this package.");
    }

    /**
     * Create the destination directory if it doesn't exist.
     *
     * @param  string  $destination
     * @return void
     */
    protected function makeDestination($destination)
    {
        if(!$this->files->isDirectory($destination)) {
            $this->files->makeDirectory($destination, 0777, true);
        }
    }

}
