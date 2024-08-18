<?php

declare(strict_types=1);

namespace Conquest\Command\Concerns;

trait RequiresDependencyPlaceholder
{
    /**
     * @var array|string The dependency placeholder of the stub.
     */
    protected $dependencyPlaceholder;

    /**
     * Retrieve the dependency placeholder property
     */
    public function getDependencyPlaceholder(): string|array
    {
        if (isset($this->dependencyPlaceholder)) {
            return $this->dependencyPlaceholder;
        }

        if (method_exists($this, 'dependencyPlaceholder')) {
            return $this->dependencyPlaceholder();
        }

        return ['{{ dependencies }}', '{{dependencies}}'];
    }

    /**
     * Set the placeholder to use to replace as dependency placeholder
     */
    public function setDependencyPlaceholder(string|array $dependencyPlaceholder): void
    {
        $this->dependencyPlaceholder = $dependencyPlaceholder;
    }
}
