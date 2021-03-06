<?php
declare(strict_types=1);

namespace BenTools\Where\Helper;

use BenTools\Where\Expression\Expression;
use function BenTools\Where\placeholders;
use function BenTools\Where\random_placeholders;
use function BenTools\Where\random_string;
use function BenTools\Where\where;

/**
 * @internal
 */
final class FieldHelper
{
    /**
     * @var string
     */
    private $field;

    /**
     * Field constructor.
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function isNull(): Expression
    {
        return where(\sprintf('%s IS NULL', $this->field));
    }

    /**
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function isNotNull(): Expression
    {
        return where(\sprintf('%s IS NOT NULL', $this->field));
    }

    /**
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function isTrue(): Expression
    {
        return where(\sprintf('%s = TRUE', $this->field));
    }

    /**
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function isFalse(): Expression
    {
        return where(\sprintf('%s = FALSE', $this->field));
    }

    /**
     * @param array       $values
     * @param null|string $placeholder
     * @param string      $glue
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function in(array $values, ?string $placeholder = '?', string $glue = ', '): Expression
    {
        $expression = '%s IN (%s)';

        if ('?' === $placeholder) {
            return where(\sprintf($expression, $this->field, placeholders($values, $placeholder, $glue)), ...\array_values($values));
        }

        if ('??' === $placeholder) {
            $placeholders = random_placeholders($values);
            $stringified = \sprintf(
                $expression,
                $this->field,
                \implode(
                    ', ',
                    \array_map(
                        function (string $placeholder) {
                            return ':'.$placeholder;
                        },
                        $placeholders
                    )
                )
            );

            return where($stringified, \array_combine($placeholders, $values));
        }

        if (null !== $placeholder) {
            throw new \InvalidArgumentException(\sprintf('Expected "?", "??" or null, got %s', $placeholder));
        }

        return where(\sprintf($expression, $this->field, \implode(', ', $values)));
    }

    /**
     * @param array  $values
     * @param string $placeholder
     * @param string $glue
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notIn(array $values, ?string $placeholder = '?', string $glue = ', '): Expression
    {
        $expression = '%s NOT IN (%s)';

        if ('?' === $placeholder) {
            return where(\sprintf($expression, $this->field, placeholders($values, $placeholder, $glue)), ...\array_values($values));
        }

        if ('??' === $placeholder) {
            $placeholders = random_placeholders($values);
            $stringified = \sprintf(
                $expression,
                $this->field,
                \implode(
                    ', ',
                    \array_map(
                        function (string $placeholder) {
                            return ':'.$placeholder;
                        },
                        $placeholders
                    )
                )
            );

            return where($stringified, \array_combine($placeholders, $values));
        }

        if (null !== $placeholder) {
            throw new \InvalidArgumentException(\sprintf('Expected "?", "??" or null, got %s', $placeholder));
        }

        return where(\sprintf($expression, $this->field, \implode(', ', $values)));
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function equals($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s = %s';

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notEquals($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s <> %s';

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function lt($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s < %s';

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function lte($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s <= %s';

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function gt($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s > %s';

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function gte($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s >= %s';

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param        $start
     * @param        $end
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function between($start, $end, ?string $placeholder = '?'): Expression
    {
        $expression = '%s BETWEEN %s AND %s';

        if ('?' === $placeholder) {
            return where(\sprintf($expression, $this->field, $placeholder, $placeholder), $start, $end);
        }

        if ('??' === $placeholder) {
            $placeholders = random_placeholders([$start, $end]);
            $stringified = \sprintf(
                $expression,
                $this->field,
                ':'.$placeholders[0],
                ':'.$placeholders[1]
            );

            return where($stringified, \array_combine($placeholders, [$start, $end]));
        }

        if (null !== $placeholder) {
            throw new \InvalidArgumentException(\sprintf('Expected "?", "??" or null, got %s', $placeholder));
        }

        return where(\sprintf($expression, $this->field, $start, $end));
    }

    /**
     * @param        $start
     * @param        $end
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notBetween($start, $end, ?string $placeholder = '?'): Expression
    {
        $expression = '%s NOT BETWEEN %s AND %s';

        if ('?' === $placeholder) {
            return where(\sprintf($expression, $this->field, $placeholder, $placeholder), $start, $end);
        }

        if ('??' === $placeholder) {
            $placeholders = random_placeholders([$start, $end]);
            $stringified = \sprintf(
                $expression,
                $this->field,
                ':'.$placeholders[0],
                ':'.$placeholders[1]
            );

            return where($stringified, \array_combine($placeholders, [$start, $end]));
        }

        if (null !== $placeholder) {
            throw new \InvalidArgumentException(\sprintf('Expected "?", "??" or null, got %s', $placeholder));
        }

        return where(\sprintf($expression, $this->field, $start, $end));
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function like(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s LIKE %s';
        $value = $surroundWith . $value . $surroundWith;

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notLike(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s NOT LIKE %s';
        $value = $surroundWith . $value . $surroundWith;

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function startsWith(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s LIKE %s';
        $value = $value . $surroundWith;

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notStartsWith(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s NOT LIKE %s';
        $value = $value . $surroundWith;

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function endsWith(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s LIKE %s';
        $value = $surroundWith . $value;

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notEndsWith(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s NOT LIKE %s';
        $value = $surroundWith . $value;

        switch ($placeholder) {
            case '?':
                return where(\sprintf($expression, $this->field, $placeholder), $value);
            case '??':
                $random = random_string();
                return where(\sprintf($expression, $this->field, ':'.$random), [$random => $value]);
            case null:
                return where(\sprintf($expression, $this->field, $value));
        }

        $placeholder = \ltrim($placeholder, ':');

        return where(\sprintf($expression, $this->field, ':'.$placeholder), [$placeholder => $value]);
    }
}
