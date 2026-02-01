<?php

namespace Notes\Test\Dto;

class BaseDto
{
    public function toArray(bool $filterEmptyValue = false): array
    {
        return $this->prepareValue($this, $filterEmptyValue);
    }

    /**
     * @param $value
     * @param bool $filterEmptyValue
     * @return array|bool|float|int|string
     */
    protected function prepareValue($value, bool $filterEmptyValue): array|bool|float|int|string
    {
        if (is_scalar($value)) {
            return $value;
        }

        $result = [];

        if (is_array($value) || is_object($value)) {
            foreach ($value as $index => $item) {
                if ($filterEmptyValue && empty($item)) {
                    continue;
                }
                $result[$index] = $this->prepareValue($item, $filterEmptyValue);
            }
            return $result;
        }

        return $result;
    }
}
