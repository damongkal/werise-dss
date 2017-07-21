/**
 * date manipulation
 * @type Array
 */

// global vars
var m_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
var m_names_abbr = new Array("JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC");    

/**
 * format date
 * @param {type} my_date
 * @param {type} my_format
 * @returns {String}
 */
function formatDate(my_date,my_format)
{
    return formatDate2(my_date,my_format,'');
}

/**
 * format date
 * @param {type} my_date
 * @param {type} my_format
 * @param {type} my_ref
 * @returns {String}
 */
function formatDate2(my_date,my_format,my_ref)
{
    if (my_date===0)
    {
        return '';
    }
    var tmp_date = new Date(my_date);
    var tmp_month = tmp_date.getMonth();
    var tmp_ref = m_names[tmp_month];
    if (my_ref==='abbr')
    {
        tmp_ref = m_names_abbr[tmp_month];
    }
    if (my_ref==='abbr2')
    {
        tmp_month = parseInt(tmp_month)+1;
        tmp_ref = String("00" + tmp_month).slice(-2);
        
    }    
    var day = tmp_date.getDate();
    var dayp = String("00" + day).slice(-2);
    
    if (my_format==='m-d')
    {
        return tmp_ref + '-' + dayp;     
    }    
    if (my_format==='m-y')
    {
        return tmp_ref + '-' + tmp_date.getFullYear();     
    }
    
    return tmp_date.getFullYear() + '-' + tmp_ref + '-' + dayp;
}