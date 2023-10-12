<?php

declare(strict_types=1);

namespace Looker\Template;

use function array_values;

final readonly class AggregateResolver implements Resolver
{
    /** @var list<Resolver> */
    private array $resolvers;

    public function __construct(Resolver ...$resolvers)
    {
        $this->resolvers = array_values($resolvers);
    }

    public function resolve(string $name): string
    {
        foreach ($this->resolvers as $resolver) {
            try {
                return $resolver->resolve($name);
            } catch (TemplateCannotBeResolved) {
            }
        }

        throw TemplateCannotBeResolved::becauseAllResolversAreExhausted($name, $this);
    }
}
