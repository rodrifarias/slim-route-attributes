<?php

namespace Rodrifarias\SlimRouteAttributes\Attributes\Validate;

class AttributesValidate
{
    public static function isPublicAccess(string $attributeName): bool
    {
        return str_ends_with($attributeName, 'PublicAccess');
    }

    public static function isMiddleware(string $attributeName): bool
    {
        return str_ends_with($attributeName, 'Middleware');
    }

    public static function isMapRoutes(string $attributeName): bool
    {
        return str_ends_with($attributeName, 'Map');
    }

    public static function isMethodHttp(string $attributeName): bool
    {
        $namespaceArray = explode('\\', $attributeName);
        return in_array(strtolower(end($namespaceArray)), ['get', 'post', 'put', 'delete', 'patch']);
    }
}
