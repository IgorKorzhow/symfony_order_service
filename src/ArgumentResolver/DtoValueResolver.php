<?php
namespace App\ArgumentResolver;

use App\Dto\AbstractValidationDto;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class DtoValueResolver implements ValueResolverInterface
{
    /**
     * @throws ReflectionException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dtoClass = $argument->getType();

        if (!isset($dtoClass) || !is_subclass_of($dtoClass, AbstractValidationDto::class)) {
            return [];
        }

        $data = json_decode($request->getContent(), true) ?? [];

        if ($request->getMethod() === Request::METHOD_GET) {
            $data = array_merge($request->query->all(), $data);
        }

        $data = $this->normalizeNestedStructures($data, $dtoClass);

        yield $this->instantiateDto($dtoClass, $data);
    }

    /**
     * @throws ReflectionException
     */
    private function normalizeNestedStructures(array $data, string $dtoClass): array
    {
        $reflection = new ReflectionClass($dtoClass);

        foreach ($reflection->getProperties() as $property) {
            $type = $property->getType();

            if (!$type) {
                continue;
            }

            $propertyName = $property->getName();
            $types = $this->getTypeNames($type);

            if (isset($data[$propertyName]) && is_array($data[$propertyName])) {
                foreach ($types as $nestedClass) {
                    if (is_subclass_of($nestedClass, AbstractValidationDto::class)) {
                        $data[$propertyName] = $this->normalizeNestedStructures(
                            $data[$propertyName],
                            $nestedClass
                        );
                        $data[$propertyName] = $this->instantiateDto($nestedClass, $data[$propertyName]);
                        break;
                    } elseif (class_exists($nestedClass)) {
                        $data[$propertyName] = $this->denormalizeObject(
                            $data[$propertyName],
                            $nestedClass
                        );
                        break;
                    }
                }
            }
        }

        return $data;
    }

    private function getTypeNames(ReflectionNamedType|ReflectionUnionType $type): array
    {
        if ($type instanceof ReflectionNamedType) {
            return [$type->getName()];
        }

        // Handle union types
        return array_map(
            fn(ReflectionNamedType $t) => $t->getName(),
            $type->getTypes()
        );
    }

    private function instantiateDto(string $dtoClass, array $data): object
    {
        try {
            $reflection = new ReflectionClass($dtoClass);
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                return new $dtoClass();
            }

            $parameters = $constructor->getParameters();
            $args = [];

            foreach ($parameters as $param) {
                $name = $param->getName();

                if (array_key_exists($name, $data)) {
                    $args[] = $data[$name];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    throw new BadRequestHttpException("Missing required parameter: $name");
                }
            }

            return $reflection->newInstanceArgs($args);
        } catch (Throwable $e) {
            throw new BadRequestHttpException(
                sprintf('Failed to instantiate %s: %s', $dtoClass, $e->getMessage())
            );
        }
    }

    private function denormalizeObject(array $data, string $className): object
    {
        try {
            if (method_exists($className, 'fromArray')) {
                return $className::fromArray($data);
            }

            return new $className(...$data);
        } catch (Throwable $e) {
            throw new BadRequestHttpException(
                sprintf('Failed to denormalize %s: %s', $className, $e->getMessage())
            );
        }
    }
}

