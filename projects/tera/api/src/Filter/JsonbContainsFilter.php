<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

// Import DBAL Types for parameter type hinting

final class JsonbContainsFilter extends AbstractFilter
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        protected LoggerInterface $logger, // Use promoted properties
        ?array $properties = null,
    ) {
        parent::__construct($managerRegistry, $properties);
    }

    /**
     * Generates OpenAPI documentation for the filter based on configured properties.
     */
    public function getDescription(string $resourceClass): array
    {
        $description = [];
        $properties = $this->getProperties();

        if (null === $properties) {
            $this->logger->warning(sprintf('JsonbContainsFilter is applied to "%s" but has no properties configured.',
                $resourceClass));

            return [];
        }

        foreach (array_keys($properties) as $property) {
            // Updated description to reflect array input possibility
            $description[$property] = [
                'property' => $property,
                // Indicate that the input can be an array of strings in the query
                'type' => 'array',
                'required' => false,
                'openapi_context' => [
                    'description' => sprintf(
                        'Filter where the JSONB field "%s" contains the provided JSON structure or all elements from the provided array. Use the PostgreSQL @> operator. Can be provided as a single JSON string or multiple query parameters (e.g., %s[]=value1&%s[]=value2).',
                        $property,
                        $property, // Add property name to example
                        $property  // Add property name to example
                    ),
                    // Example showing array input
                    'example' => '?sharingConditions[]=value1&sharingConditions[]=value2 OR ?sharingConditions=["value1", "value2"]',
                    'schema' => [
                        // OpenAPI schema for array of strings
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                    ],
                    // Allow parameter style 'form' with explode 'true' for array parameters
                    'style' => 'form',
                    'explode' => true,
                ],
            ];
        }

        return $description;
    }

    /**
     * Applies the JSONB @> filter logic, handling both single JSON strings and array inputs.
     */
    protected function filterProperty(
        string $property,
        mixed $value, // Value can now be string or array
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!$this->isPropertyEnabled($property, $resourceClass)) {
            return;
        }

        $finalJsonValue = null;

        // Case 1: Handle array input (e.g., ?property[]=value1&property[]=value2)
        if (is_array($value)) {
            if (empty($value)) {
                return; // Don't filter if the array is empty
            }
            // Ensure all elements are strings or numbers for simple JSON array encoding
            $filteredValue = array_filter($value, fn ($item) => is_string($item) || is_numeric($item));
            if (empty($filteredValue)) {
                $this->logger->warning(sprintf(
                    'Empty or invalid array values provided for JsonbContainsFilter on property "%s". Only strings/numbers supported in array mode.',
                    $property
                ),
                    ['value' => $value]);

                return;
            }
            // Encode the PHP array into a JSON array string
            try {
                // Use JSON_THROW_ON_ERROR for better error handling if encoding fails
                $finalJsonValue = json_encode(array_values($filteredValue), JSON_THROW_ON_ERROR); // Re-index array
            } catch (\JsonException $e) {
                $this->logger->error(sprintf(
                    'Failed to JSON encode array value for JsonbContainsFilter on property "%s". Error: %s',
                    $property,
                    $e->getMessage()
                ),
                    ['value' => $filteredValue]);

                return; // Don't filter if encoding fails
            }

        // Case 2: Handle single string input (expecting a valid JSON string)
        } elseif (is_string($value) && '' !== trim($value)) {
            // Validate that the input string is actually valid JSON
            try {
                // Attempt to decode just for validation
                json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                // If validation passes, use the original string value
                $finalJsonValue = $value;
            } catch (\JsonException $e) {
                // Log a warning if the provided string value isn't valid JSON
                $this->logger->warning(sprintf(
                    'Invalid JSON string provided for JsonbContainsFilter on property "%s". Error: %s',
                    $property,
                    $e->getMessage()
                ),
                    ['value' => $value]);

                return; // Don't apply filter if JSON string is invalid
            }
        } else {
            // Ignore null, empty strings, or other unexpected types
            return;
        }

        // If we don't have a valid JSON value string by now, return
        if (null === $finalJsonValue) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName($property);

        $queryBuilder
            ->andWhere(sprintf('JSON_CONTAINS(%s.%s, :%s) = true', $alias, $property, $parameterName))
            // Set the parameter using the final JSON string value
            // Explicitly set the type to string, as we are passing a JSON string.
            // Or use Types::JSON if your DBAL/Platform supports it correctly for parameters.
            ->setParameter($parameterName, $finalJsonValue, Types::STRING);
        // Alternatively, if you have a 'jsonb' type registered and it works for parameters:
        // ->setParameter($parameterName, $finalJsonValue, 'jsonb');
    }
}
