/**
 * WeRise common functions
 * @type type
 */
var weriseApp = {
    showError: function (errmsg) {
        if (window._env == 'STAGE')
        {
            jQuery("#werise-error-msg").html(errmsg);
        } else
        {
            weriseApp.dbg('', 'error', errmsg);
            jQuery("#werise-error-msg").html('Please contact the website administrator.');
        }
        jQuery("#werise-error-div").show();
    },
    recentSelect: [],
    varietyInfo: false,
    ajaxCache: [],
    ajaxCacheIdx: [],
    dropDownData: {station: []},
    ajax: function (urlparams) {
        return jQuery.ajax({
            type: "GET",
            url: "ajax.php",
            data: urlparams,
            dataType: 'json',
            timeout: 5000})
                .fail(function () {
                    weriseApp.showError('ajax error: ' + urlparams);
                });
    },
    getStoredCountry: function () {
        return this.recentSelect.country;
    },
    getStoredStation: function () {
        return this.recentSelect.station;
    },
    getStoredYear: function () {
        return this.recentSelect.year;
    },
    getStoredVariety: function () {
        return this.recentSelect.variety;
    },
    setVarietyInfo: function (data) {
        this.varietyInfo = data;
    },
    getVarietyInfo: function () {
        return this.varietyInfo;
    },
    setAjaxCache: function (data) {
        var idx = 0;
        this.ajaxCache[idx] = data;
    },
    getAjaxCache: function () {
        var idx = 0;
        return this.ajaxCache[idx];
    },
    dbg: function (callerobj, marker, obj) {
        var oktypes = ["string", "number"];
        if (oktypes.indexOf(typeof obj) !== -1)
        {
            console.log(callerobj + '.' + weriseApp.dbg.caller.name + ' : ' + marker + ' => ' + obj);
        } else
        {
            console.log('dbg: ' + marker);
            console.log(typeof obj);
            console.log(obj);
        }
    },
    isJson: function (str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }            
};

/**
 * URL builder 
 * @param {type} base
 * @returns {UrlBuilder}
 */
function UrlBuilder(base) {
    this.base = base;
    this.args = [];
}
UrlBuilder.prototype = {
    base: '',
    args: [],
    constructor: UrlBuilder,
    addArg: function (key, val)
    {
        this.args.push(key + '=' + val);
    },
    getUrl: function ()
    {
        return this.base + this.args.join('&');
    }
};

/**
 * show loader for dropdown
 * @param {type} item_id
 * @returns {undefined}
 */
function showLoader(item_id)
{
    jQuery(item_id).css("background-image", "url('images/ajax-loader2.gif')")
            .css("background-repeat", "no-repeat")
            .css("color", "#ffffff");
}

/**
 * show imgloader for chart
 * @param {type} item_chart
 * @returns {undefined}
 */
function showLoaderChart(item_chart)
{
    jQuery(item_chart).css("display", "block")
            .css("height", "100px")
            .html('<div style="text-align:center"><img src="images/ajax-loader.gif" /><br />' + _t('Generating chart. Please wait...') + '</div>');
}

/**
 * hide imgloader for chart
 * @param {type} item_chart
 * @returns {undefined}
 */
function hideLoaderChart(item_chart)
{
    jQuery(item_chart).css("display", "none");
}

/**
 * hide imgloader for drodown
 * @param {type} item_id
 * @returns {undefined}
 */
function hideLoader(item_id)
{
    jQuery(item_id).css("background-image", "")
            .css("color", "#555555");
}

/**
 * display error in chart div
 */
function showErrorChart(error_txt)
{
    var error_txt2 = 'There was a problem generating the data you requested. Please contact the website Administrator.';
    if (error_txt == '')
    {
        jQuery('#dss-error-box').html(error_txt2).show();
    } else
    {
        jQuery('#dss-error-box').html(error_txt).show();
    }
}

/**
 * hide error in chart div
 */
function hideErrorChart()
{
    jQuery('#dss-error-box').hide();
}


var weriseLogin = {
    init: function () {
        jQuery(function () {

            jQuery('#login-modal').on('shown.bs.modal', function () {
                jQuery('#login-form #username').focus();
            });

            // display login
            if (!window.is_logged)
            {
                weriseLogin.showForm();
            }
            // login submit
            jQuery('#login-form #login-submit').click(function () {
                weriseLogin.submitForm();
            });
        });
    },
    showForm: function () {
        jQuery('#login-modal').modal('show');
    },
    errorForm: function (errormsg) {
        jQuery('#login-error-msg').html(errormsg);
        jQuery('#login-error').addClass('d-block');
    },
    submitForm: function () {
        // validate username
        var username = jQuery('#username').val();
        if (username === '')
        {
            weriseLogin.errorForm('Please choose a username.');
            return false;
        }
        // validate password
        var password = jQuery('#password').val();        
        if (password === '')
        {
            weriseLogin.errorForm('Please choose a password.');
            return false;
        }
        // validate credentials
        jQuery.ajax({
            type: "GET",
            url: "ajax.php",
            data: "pageaction=index&action=login&username=" + username + "&password=" + password,
            dataType: 'json',
            timeout: 20000,
            success: function (data) {
                if (data === 'success')
                {
                    jQuery('#login-modal').modal('hide');
                    location.reload();
                } else
                {
                    weriseLogin.errorForm('Invalid Credentials.');
                }
            },
            error: function (e, t, n) {
                weriseLogin.errorForm(e);
            }
        });

        return true;
    }
};
weriseLogin.init();
