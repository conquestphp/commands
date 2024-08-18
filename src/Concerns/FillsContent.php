<?php
namespace Conquest\Command\Concerns;

use Illuminate\Support\Collection;

trait FillsContent
{
    use RequiresContentPlaceholder;
    use RequiresDependencyPlaceholder;

    /**
     * @var Collection The dependencies of the stub.
     */
    protected Collection $dependencies;

    public abstract function getContent(): string;

    /**
     * Fill the content and dependencies in the stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return static
     */
    public function fillContent(string &$stub): string
    {
        $stub = str_replace($this->getContentPlaceholder(), $this->getContent(), $stub);
        $stub = str_replace($this->getDependencyPlaceholder(), $this->getDependencies(), $stub);
        return $stub;
    }

    /**
     * Get the formatted dependencies.
     *
     * @return string
     */
    public function getDependencies(): string
    {
        return collect($this->dependencies ?? [])
            ->unique()
            ->sort()
            ->map(fn ($dependency) => "use $dependency;")
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

    /**
     * Set the content placeholder.
     * 
     * @param  array|string  $contentPlaceholder
     */
    public function setContentPlaceholder(array|string $contentPlaceholder): void
    {
        $this->contentPlaceholder = $contentPlaceholder;
    }

    /**
     * Set the dependency placeholder.
     * 
     * @param  array|string  $dependencyPlaceholder
     */
    public function setDependencyPlaceholder(array|string $dependencyPlaceholder): void
    {
        $this->dependencyPlaceholder = $dependencyPlaceholder;
    }
}