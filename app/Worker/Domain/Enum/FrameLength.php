<?php

namespace App\Worker\Domain\Enum;

enum FrameLength: int
{
    case EVERY_SECOND = 1;
    case EVERY_TWO_SECONDS = 2;
    case EVERY_FIVE_SECONDS = 5;
    case EVERY_TEN_SECONDS = 10;
    case EVERY_THIRTY_SECONDS = 30;
    case EVERY_MINUTE = 60;
}