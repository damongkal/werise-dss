<?php
class werise_oryza_fertilizer
{
    const _FERT_NONE = 0;
    const _FERT_GEN = 1;
    const _FERT_SPC = 2;    

    public static function getTypeDesc($type)
    {
        if ($type == self::_FERT_NONE)
        {
            return _t('No Fertilizer');
        }        
        if ($type == self::_FERT_GEN)
        {
            return _t('Recommended Fertilizer');
        }
        if ($type == self::_FERT_SPC)
        {
            return _t('Recommended Fertilizer');
        }        
    }
}