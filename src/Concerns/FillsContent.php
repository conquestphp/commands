<?php
namespace Conquest\Command\Concerns;

use Illuminate\Support\Collection;

trait FillsContent
{
    /**
     * @var Collection The dependencies of the stub.
     */
    protected Collection $dependencies;

    /**
     * @var array The default dependencies of the stub.
     */
    protected $defaultDependencies;

    public abstract function getContent(): string;

    /**
     * Fill the content and dependencies in the stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return static
     */
    public function fillContent(&$stub, $name)
    {
        $stub = str_replace(['{{ content }}', '{{content}}'], $this->getContent(), $stub);
        $stub = str_replace(['{{ dependencies }}', '{{dependencies}}'], $this->getDependencies(), $stub);
        return $this;
    }

    /**
     * Set the default dependencies.
     *
     * @param  string  ...$dependencies
     */
    public function defaultDependencies(...$dependencies): void
    {
        $this->defaultDependencies = $dependencies;
    }

    /**
     * Get the formatted dependencies.
     *
     * @return string
     */
    public function getDependencies(): string
    {
        return collect($this->defaultDependencies ?? [])
            ->merge($this->dependencies ?? collect())
            ->unique()
            ->sort()
            ->map(fn($dependency) => "use $dependency;")
            ->implode("\n");
    }

    /**
     * Add a dependency.
     *
     * @param  string  $dependency
     * @return static
     */
    public function addDependency(string $dependency): static
    {
        if (!isset($this->dependencies)) {
            $this->dependencies = collect();
        }

        $this->dependencies->push($dependency);
        return $this;
    }
}