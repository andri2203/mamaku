<?php

namespace App;

enum ItemOutStatusEnum: string
{
    case RUSAK = 'rusak';
    case KADALUARSA = 'kadaluarsa';
    case HILANG = 'hilang';
}
