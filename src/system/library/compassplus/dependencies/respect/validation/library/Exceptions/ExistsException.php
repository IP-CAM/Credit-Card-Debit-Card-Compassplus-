<?php
 namespace Respect\Validation\Exceptions; class ExistsException extends ValidationException { public static $defaultTemplates = [ self::MODE_DEFAULT => [ self::STANDARD => '{{name}} must exists', ], self::MODE_NEGATIVE => [ self::STANDARD => '{{name}} must not exists', ], ]; } 