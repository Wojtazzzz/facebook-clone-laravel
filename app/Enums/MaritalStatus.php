<?php

namespace App\Enums;

enum MaritalStatus: string
{
    case SINGLE = 'Single';
    case IN_A_RELATIONSHIP = 'In a relationship';
    case ENGAGED = 'Engaged';
    case MARRIED = 'Married';
    case IN_A_CIVIL_PARTNERSHIP = 'In a civil partnership';
    case IN_A_DOMESTIC_PARTNERSHIP = 'In a domestic partnership';
    case IN_AN_OPEN_RELATIONSHIP = 'In an open relationship';
    case ITS_COMPLICATED = 'Its complicated';
    case SEPARATED = 'Separated';
    case WIDOWED = 'Widowed';
}
