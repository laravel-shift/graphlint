<?php

namespace Olivernybroe\Graphlint\Events;

use Olivernybroe\Graphlint\Fixer\FixerResult;

class AfterFixerEvent implements EventInterface
{
    public function __construct(
        private FixerResult $originalFixerResult,
        private FixerResult $compiledFixerResult,
    ) {}

    public function getOriginalFixerResult(): FixerResult
    {
        return $this->originalFixerResult;
    }

    public function getCompiledFixerResult(): FixerResult
    {
        return $this->compiledFixerResult;
    }
}