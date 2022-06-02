<?php

namespace Worksome\Graphlint\Fixes;

use GraphQL\Language\AST\NameNode;
use Worksome\Graphlint\ProblemDescriptor;
use Worksome\Graphlint\Utils\NodeNameResolver;

class SuffixNameFixer extends Fixer
{
    private string $suffix;

    public function __construct(
        private NodeNameResolver $nodeNameResolver,
    ) {}

    public function fix(ProblemDescriptor $problemDescriptor): void
    {
        $node = $problemDescriptor->getNode();

        if (!$node instanceof NameNode) {
            return;
        }

        $suffixedName = "{$this->nodeNameResolver->getName($node)}$this->suffix";

        $node->value = $suffixedName;
    }

    public function withSuffix(string $suffix): self
    {
        $this->suffix = $suffix;
        return $this;
    }
}