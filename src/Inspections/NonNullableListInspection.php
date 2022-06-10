<?php

declare(strict_types=1);

namespace Worksome\Graphlint\Inspections;

use GraphQL\Language\AST\ListTypeNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NonNullTypeNode;
use Worksome\Graphlint\Fixes\NonNullFixer;
use Worksome\Graphlint\InspectionDescription;
use Worksome\Graphlint\ProblemsHolder;

class NonNullableListInspection extends Inspection
{
    public function __construct(
        private NonNullFixer $nonNullFixer,
    ) {
    }

    public function visitListType(
        ProblemsHolder $problemsHolder,
        ListTypeNode $listTypeNode,
        Node $parent,
    ): void {
        if ($parent instanceof NonNullTypeNode) {
            return;
        }

        $problemsHolder->registerProblem(
            $listTypeNode,
            $this->nonNullFixer
        );
    }

    public function definition(): InspectionDescription
    {
        return new InspectionDescription(
            "Lists must be non nullable.",
        );
    }
}
