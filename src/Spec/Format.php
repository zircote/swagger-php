<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * The formats OpenAPI 3.1 and JSON Schema 2020-12 define.
 *
 * A format is an open string, so any other value can still be passed as one. Every attribute
 * that takes a `format` stores the string value.
 *
 * 3.0's `byte` and `binary` are not cases: 3.1 writes them as `contentEncoding: base64` and as
 * `contentMediaType` without a `type`, and the 3.0 compiler translates those back.
 */
enum Format: string
{
    // OpenAPI
    case Int32 = 'int32';
    case Int64 = 'int64';
    case Float = 'float';
    case Double = 'double';
    case Password = 'password';

    // JSON Schema
    case DateTime = 'date-time';
    case Date = 'date';
    case Time = 'time';
    case Duration = 'duration';
    case Email = 'email';
    case IdnEmail = 'idn-email';
    case Hostname = 'hostname';
    case IdnHostname = 'idn-hostname';
    case Ipv4 = 'ipv4';
    case Ipv6 = 'ipv6';
    case Uri = 'uri';
    case UriReference = 'uri-reference';
    case Iri = 'iri';
    case IriReference = 'iri-reference';
    case Uuid = 'uuid';
    case UriTemplate = 'uri-template';
    case JsonPointer = 'json-pointer';
    case RelativeJsonPointer = 'relative-json-pointer';
    case Regex = 'regex';

    /**
     * The type a value in this format has.
     */
    public function type(): Type
    {
        return match ($this) {
            self::Int32, self::Int64 => Type::Integer,
            self::Float, self::Double => Type::Number,
            default => Type::String,
        };
    }
}
