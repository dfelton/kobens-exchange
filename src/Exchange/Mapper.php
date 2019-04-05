<?php

namespace Kobens\Exchange\Exchange;

use Kobens\Exchange\Exception\Exception;
use Kobens\Exchange\ExchangeInterface;

final class Mapper
{
    /**
     * @var string[]
     */
    private static $mappings = [];

    public function __construct(array $mappings = [])
    {
        if ($mappings !== [] && static::$mappings !== []) {
            throw new \LogicException(\sprintf(
                '"%s" cannot be reinstantiated in the same process with additional mappings.',
                static::class
            ));
        } elseif ($mappings !== []) {
            foreach ($this->validateMappings($mappings) as $map) {
                static::$mappings[$map['key']] = $map['className'];
            }
        }
    }

    protected function validateMappings(array $mappings) : \Generator
    {
        foreach ($mappings as $key => $className) {
            if (!\class_exists($className)) {
                throw new Exception(\sprintf(
                    'Exchange map class "%s" not found for key "%s".',
                    $className,
                    $key
                ));
            }

            $implements = \class_implements($className);
            if (!isset($implements[ExchangeInterface::class])) {
                throw new Exception(\sprintf(
                    '"%s" only accepts interfaces of "%s". "%s" is lacking this requirement.',
                    static::class,
                    ExchangeInterface::class,
                    $className
                ));
            }

            yield [
                'key' => $key,
                'className' => $className
            ];
        }
    }

    public function getExchange(string $key) : ExchangeInterface
    {
        if (!isset(static::$mappings[$key])) {
            throw new Exception(\sprintf('Invalid Exchange Key "%s"', $key));
        }
        return new static::$mappings[$key]();
    }

    public function getKeys() : array
    {
        return \array_keys(static::$mappings);
    }
}