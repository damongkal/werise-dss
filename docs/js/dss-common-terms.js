var WeriseTerms = {
    getVarietyLabel: function(variety){
        var tmp = variety.split('.');
        return tmp[0].toUpperCase();
    }    
}
/**
 * description of weather data type
 */
function getWeatherType(wtype)
{
    if (wtype=='f')
    {
        return _t('Forecast');
    }    
    return _t('Historical');
}

/**
 * for chart: get properties of variables
 * index
 * 0 = unit of measurement used
 * 1 = maximum value
 * @param {integer} wvar
 * @returns {Array|String}
 */
function getWeatherVarProps(wvar)
{
    if (wvar===0)
    {
        return ['mm/decade',400,'Rainfall'];
    }
    if (wvar===1 || wvar===2)
    {
        return ['°C',45,'Temperature'];
    }
    if (wvar===3)
    {
        return ['MJ/m²',35,'Solar Radiation'];
    }
    if (wvar===4)
    {
        return ['kPa',6,'Early morning vapor pressure'];
    }
    if (wvar===5)
    {
        return ['m/s',10,'Wind Speed'];
    }
    if (wvar===6)
    {
        return ['hours',24,'Sunshine Duration'];
    }    
    return(['unit',500,'Unknown']);
}

function launch_help(section)
{
    window.open("/admin.php?pageaction=help#"+section, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, width=1000, height=500");    
}