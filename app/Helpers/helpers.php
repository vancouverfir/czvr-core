<?php

function decimal_to_hm(float|int|null $decimal): string
{
    $minutes = (int) floor(max(0, (float) $decimal) * 60);

    return $minutes % 60 === 0
        ? sprintf('%dh', intdiv($minutes, 60))
        : sprintf('%dh %02dm', intdiv($minutes, 60), $minutes % 60);
}
