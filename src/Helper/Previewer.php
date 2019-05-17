<?php
declare(strict_types=1);

namespace BenTools\Where\Helper;

/**
 * @internal
 */
final class Previewer
{
    /**
     * @param string $expression
     * @param array  $values
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function preview(string $expression, array $values = []): string
    {
        if (0 === \count($values)) {
            return $expression;
        }

        if (self::isSequentialArray($values)) {
            return self::previewUnnamed($expression, $values);
        }

        return self::previewNamed($expression, $values);
    }

    /**
     * @param string $expression
     * @param array  $values
     * @return string
     * @throws \InvalidArgumentException
     */
    private static function previewUnnamed(string $expression, array $values): string
    {
        if (\count($values) !== \preg_match_all("/([\?])/", $expression)) {
            throw new \InvalidArgumentException("Number of variables doesn't match number of parameters in statement");
        }

        $preview = $expression;

        foreach ($values as $value) {
            $preview = \preg_replace("/([\?])/", self::escape($value), $preview, 1);
        }

        return $preview;
    }

    /**
     * @param string $expression
     * @param array  $values
     * @return string
     * @throws \InvalidArgumentException
     */
    private static function previewNamed(string $expression, array $values): string
    {
        $preview = $expression;
        $keywords = [];

        foreach ($values as $key => $value) {
            if (!\in_array($key, $keywords, true)) {
                $keywords[] = $key;
            }
        }
        $nbPlaceholders = \preg_match_all('#:([a-zA-Z0-9_]+)#', $expression, $placeholders);
        if ($nbPlaceholders > 0 && \count(\array_unique($placeholders[1])) !== \count($values)) {
            throw new \InvalidArgumentException("Number of variables doesn't match number of parameters in statement");
        }
        foreach ($keywords as $keyword) {
            $pattern = "/(\:\b" . $keyword . "\b)/i";
            $preview = \preg_replace($pattern, self::escape($values[$keyword]), $preview);
        }

        return $preview;
    }

    /**
     * @param $value
     * @return string
     * @throws \InvalidArgumentException
     */
    private static function escape($value)
    {
        $type = \gettype($value);
        switch ($type) {
            case 'NULL':
                return 'NULL';
            case 'boolean':
                return $value ? 'TRUE' : 'FALSE';
            case 'double':
            case 'integer':
                return $value;
            default:
                return "'" . \addslashes(self::stringify($value)) . "'";
        }
    }

    /**
     * @param $value
     * @return string
     * @throws \InvalidArgumentException
     */
    private static function stringify($value): string
    {
        if (\is_scalar($value)) {
            return (string) $value;
        }

        if (\is_object($value) && \is_callable([$value, '__toString'])) {
            return (string) $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        throw new \InvalidArgumentException(\sprintf('Expected string or stringable object, %s returned', \is_object($value) ? \get_class($value) : \gettype($value)));
    }

    /**
     * @param array $array
     * @return bool
     */
    private static function isSequentialArray(array $array): bool
    {
        return isset($array[0]) && \array_keys($array) === \range(0, \count($array) - 1);
    }
}
