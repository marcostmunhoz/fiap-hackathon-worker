<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidValueException;
use Stringable;

/**
 * @template TValue
 *
 * @property TValue $value
 *
 * @codeCoverageIgnore
 */
readonly abstract class AbstractValueObject implements Stringable
{
    /**
     * @param TValue $value
     */
    public protected(set) mixed $value;

    /**
     * @param TValue $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $this->sanitize($value);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * @param TValue $value
     *
     * @return TValue
     */
    abstract protected function sanitize(mixed $value): mixed;

    /**
     * @param self<TValue> $other
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    protected function throwInvalidValueException(string $message): never
    {
        throw new InvalidValueException($message);
    }
}