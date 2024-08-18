<?php

declare(strict_types=1);

namespace Conquest\Command\Concerns;

trait RequiresContentPlaceholder
{
    /**
     * @var array|string The content placeholder of the stub.
     */
    // protected $contentPlaceholder;

    /**
     * Retrieve the content placeholder property
     */
    public function getContentPlaceholder(): string|array
    {
        if (isset($this->contentPlaceholder)) {
            return $this->contentPlaceholder;
        }

        if (method_exists($this, 'contentPlaceholder')) {
            return $this->contentPlaceholder();
        }

        return ['{{ content }}', '{{content}}'];
    }

    /**
     * Set the placeholder to use to replace as content placeholder
     */
    public function setContentPlaceholder(string|array $contentPlaceholder): void
    {
        $this->contentPlaceholder = $contentPlaceholder;
    }
}
