<?php
namespace App\Enums;

enum OrganizationType: string{
    case GENERAL_UNION          = 'general_union';
    case SUB_UNION              = 'sub_union';
    case TRADE_UNION            = 'trade_union';
    case GOVERNMENT_INSTITUTION = 'government_institution';
    case INSURANCE_COMPANY      = 'insurance_company';
    case LAW_FIRM               = 'law_firm';
    case PLATFORM               = 'platform';
    case ORGANIZATION           = 'organization';
    case GUILD                  = 'guild';
}
