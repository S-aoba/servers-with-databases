<?php

namespace Helpers;

class ValidationHelper
{
    public static function integer($value, float $min = -INF, float $max = INF): int
    {
        // PHPには、データを検証する組み込み関数があります。詳細は https://www.php.net/manual/en/filter.filters.validate.php を参照ください。
        $value = filter_var($value, FILTER_VALIDATE_INT, ["min_range" => (int) $min, "max_range"=>(int) $max]);

        // 結果がfalseの場合、フィルターは失敗したことになります。
        if ($value === false) throw new \InvalidArgumentException("The provided value is not a valid integer.");

        // 値がすべてのチェックをパスしたら、そのまま返します。
        return $value;
    }

    public static function type(?string $type): string {
        if($type === null) throw new \InvalidArgumentException("The provided value is null.");

        $availableTypeList = ['cpu', 'gpu', 'motherboard', 'power', 'memory', 'ssd', 'hd'];

        // If provided value extis in availableTypeList, Keep the original $type value.
        // If provided value does not extis in availableTypeList, Set to false.
        $type = strtolower($type);
        $type = in_array($type, $availableTypeList, true) ? $type : false;
        if($type === false) throw new \InvalidArgumentException("The provided value is not a valid type.");

        return $type;
    }

    public static function order(string $order): string {

        $order = strtoupper($order);
        $orderList = ['ASC', 'DESC'];
        $order = in_array($order, $orderList, true) ? $order : false;
        if($order === false) throw new \InvalidArgumentException('The provided value is not a valid order.');

        return $order;
    }
}