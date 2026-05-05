<?php

namespace App\Enums;

enum Gender: int
{
    case Male = 1;
    case Female = 0;
    case Other = 2;

    public function label(): string
    {
        return match($this) {
            Gender::Male => '男',
            Gender::Female => '女',
            Gender::Other => '其他',
        };
    }
}