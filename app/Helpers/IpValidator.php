<?php
declare(strict_types=1);

namespace Iotubby\Helpers;


use Nette\Forms\Controls\BaseControl;

class IpValidator
{

    public const REGEX_IPV4 = '/(\d{1,3}\.){3}\d{1,3}/';
    public const REGEX_IPV6 = '/(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/';

    public static function validateIPv4(string $ipv4): bool
    {
        return preg_match(self::REGEX_IPV4, $ipv4) == 1;
    }

    public static function validateIPv6(string $ipv6): bool
    {
        return preg_match(self::REGEX_IPV6, $ipv6) == 1;
    }

    /**
     * @param BaseControl $item
     * @param mixed $arg
     * @return bool
     */
    public static function validateInput(BaseControl $item, $arg)
    {
        return self::validateIPv4($item->getValue()) || self::validateIPv6($item->getValue());
    }

    public static function getInputValidator(): string
    {
        return static::class . '::validateInput';
    }

}