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

    public static function isMethodHttp(string $attributeName): bool
    {
        $namespaceArray = explode('\\', $attributeName);
        return in_array(end($namespaceArray), ['Get', 'Post', 'Put', 'Delete', 'Patch']);
    }
}
