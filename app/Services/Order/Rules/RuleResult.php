<?php

namespace App\Services\Order\Rules;

class RuleResult
{
    protected bool $triggered = false;

    protected int $score = 0;

    protected string $ruleName;

    protected array $triggeredChecks;

    public function __construct(string $ruleName)
    {
        $this->ruleName = $ruleName;
    }

    public static function triggered(string $ruleName, int $score = 10, array $triggeredChecks = []): self
    {
        $result = new self($ruleName);
        $result->triggered = true;
        $result->score = $score;
        $result->triggeredChecks = $triggeredChecks;

        return $result;
    }

    public static function notTriggered(string $ruleName): self
    {
        return new self($ruleName);
    }

    public function isTriggered(): bool
    {
        return $this->triggered;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getRuleName(): string
    {
        return $this->ruleName;
    }

    public function getTriggeredChecks(): array
    {
        return $this->triggeredChecks;
    }
}
