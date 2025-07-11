<?php
namespace App\ArgumentResolver;

use App\Dto\AbstractValidationDto;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
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

        $data = $this->normalizeNestedStructures($data, $dtoClass);

        yield new $dtoClass($data);
    }

    /**
     * @throws ReflectionException
     */
    private function normalizeNestedStructures(array $data, string $dtoClass): array
    {
        $reflection = new ReflectionClass($dtoClass);

        foreach ($reflection->getProperties() as $property) {
            $type = $property->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                continue;
            }

            $propertyName = $property->getName();
            $nestedClass = $type->getName();

            if (isset($data[$propertyName]) && is_array($data[$propertyName])) {
                if (is_subclass_of($nestedClass, AbstractValidationDto::class)) {
                    $data[$propertyName] = $this->normalizeNestedStructures(
                        $data[$propertyName],
                        $nestedClass
                    );
                    $data[$propertyName] = new $nestedClass($data[$propertyName]);
                } elseif (class_exists($nestedClass)) {
                    $data[$propertyName] = $this->denormalizeObject(
                        $data[$propertyName],
                        $nestedClass
                    );
                }
            }
        }

        return $data;
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
