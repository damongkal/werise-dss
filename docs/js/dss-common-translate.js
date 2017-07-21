/**
 * language text translation
 * @param {type} str
 * @returns {Window.langdata.target|window.langdata.target}
 */
function _t(str)
{
    var str_t = str.trim().toLowerCase();
    var str_en = '';
    var idx_found = -1;
    for (var i = 0; i < window.langdata.en.length; i++) {
        str_en = window.langdata.en[i].trim().toLowerCase();
        if (str_t == str_en)
        {
            idx_found = i;
            break;
        }
    }

    // get translated text
    if (idx_found !== -1 && window.langdata.target[idx_found]!= undefined)
    {
        return window.langdata.target[idx_found];
    }

    // no translation
    return str;
}