<?php
class werise_weather_properties
{
    const _REALTIME = 'r';
    const _FORECAST = 'f';
    const _WVAR_RAINFALL = 0;
    const _WVAR_MINTEMP = 1;
    const _WVAR_MAXTEMP = 2;
    const _WVAR_IRRADIANCE = 3;
    const _WVAR_VAPOR = 4;
    const _WVAR_WINDSPEED = 5;
    const _WVAR_SUNSHINE = 6;
    
    public static function getColumnName($wvar)
    {
        switch ($wvar)
        {
            case self::_WVAR_MINTEMP :
                return 'min_temperature';
            case self::_WVAR_MAXTEMP :
                return 'max_temperature';
            case self::_WVAR_IRRADIANCE :
                return 'irradiance';
            case self::_WVAR_VAPOR :
                return 'vapor_pressure';
            case self::_WVAR_WINDSPEED :
                return 'mean_wind_speed';
            case self::_WVAR_SUNSHINE:
                return 'sunshine_duration';
            case self::_WVAR_RAINFALL :    
            default:
                return 'rainfall';
        }
    }
    
    /**
     * definition of wtype
     * @param string $wtype
     * @return string
     */
    public static function getTypeDesc($wtype)
    {
        if ($wtype===self::_REALTIME)
        {
            return _t('Historical');
        }
        if ($wtype===self::_FORECAST)
        {
            return _t('Forecast');
        }        
    }      
    
    /**
     * 
     * @param type $wvar
     * @return string
     */
    public static function getVarName($wvar)
    {
        switch ($wvar)
        {
            case self::_WVAR_RAINFALL:
                return _t('Rainfall');
            case self::_WVAR_MINTEMP:
                return _t('Minimum Temperature');
            case self::_WVAR_MAXTEMP :
                return _t('Maximum Temperature');
            case self::_WVAR_IRRADIANCE :
                return _t('Solar Radiation');
            case self::_WVAR_VAPOR :
                return _t('Early morning vapor pressure');
            case self::_WVAR_WINDSPEED :
                return _t('Wind Speed');
            case self::_WVAR_SUNSHINE :
                return _t('Sunshine Duration');
        }
    }    
}