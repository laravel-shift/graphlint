<?php

declare(strict_types=1);

namespace Worksome\Graphlint\Fixes;

use GraphQL\Language\AST\NameNode;
use Illuminate\Support\Str;
use Worksome\Graphlint\ProblemDescriptor;
use Worksome\Graphlint\Utils\NodeNameResolver;

class CamelCaseNameFixer extends Fixer
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver,
    ) {
    }

    public function fix(ProblemDescriptor $problemDescriptor): void
    {
        $node = $problemDescriptor->getNode();

        if (!$node instanceof NameNode) {
            return;
        }

        $name = $this->nodeNameResolver->getName($node);

        if ($name === null) {
            return;
        }

        $camelCasedName = Str::camel($name);

        $node->value = $camelCasedName;
    }
}
