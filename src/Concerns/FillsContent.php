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

    abstract public function getContent(): string;

    /**
     * Fill the content and dependencies in the stub.
     *
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
     */
    public function addDependency(string $dependency): static
    {
        if (! isset($this->dependencies)) {
            $this->dependencies = collect();
        }

        $this->dependencies->push($dependency);

        return $this;
    }

    /**
     * Set the content placeholder.
     */
    public function setContentPlaceholder(array|string $contentPlaceholder): void
    {
        $this->contentPlaceholder = $contentPlaceholder;
    }

    /**
     * Set the dependency placeholder.
     */
    public function setDependencyPlaceholder(array|string $dependencyPlaceholder): void
    {
        $this->dependencyPlaceholder = $dependencyPlaceholder;
    }
}
