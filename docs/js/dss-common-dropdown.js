/**
 * create <select> dropdown item
 * @type type
 */
function DropDownBase()
{
    this.buildAll = function(data, item_id, item_label, defaultval)
    {
        weriseApp.dbg('DropDownBase', 'args', item_id+' '+defaultval);        
        // check if multi-level
        var multi = 0;
        jQuery.each(data, function(i) {
            optlabel = data[i][1];
            optvalue = data[i][0];
            if (data[i][2] != undefined)
            {
                multi = 1;
                return;
            }
        });
        if (multi===1)
        {
            buildAll2(data, item_id, item_label, defaultval);
            return;
        }
        var dropdown = initOptions(item_id,item_label);
        if (data===false)
        {
            return;
        }
        var newitem;
        jQuery.each(data, function(i){
            newitem = newOption(data[i][1],data[i][0]);
            if (data[i][0] == defaultval)
            {
                newitem.attr("selected","selected");
            }
            dropdown.append(newitem);
        });
        jQuery(item_id).trigger('change');
    };
    /**
     * multi level select
     * @param {type} data
     * @param {type} item_id
     * @param {type} item_label
     * @param {type} defaultval
     * @returns {undefined}
     */
    function buildAll2(data, item_id, item_label, defaultval)
    {
        var dropdown = initOptions(item_id,item_label);
        if (data===false)
        {
            return;
        }
        var newitem, newgroup, lvl, indentlvl, optlabel = "", optvalue = "";
        var new_default = '';
        jQuery.each(data, function(i){
            lvl = 1;
            optlabel = data[i][1];
            optvalue = data[i][0];
            if (data[i][2] != undefined)
            {
                lvl = parseInt(data[i][2]);
            }
            indentlvl = (lvl - 1) * 4;
            if (optvalue == "GRP")
            {
                // select optgroup
                newgroup = newGroup(indentOpt(indentlvl)+optlabel);
                dropdown.append(newgroup.attr("class",'select-extra'));            
            } else {
                if (new_default==='')
                {
                    new_default = optvalue;
                }
                // select option
                newitem = newOption(indentOpt(indentlvl)+optlabel,optvalue);
                if (optvalue == defaultval)
                {
                    new_default = optvalue;
                    newitem.attr("selected","selected");
                }
                newgroup.append(newitem.attr("class",'select-normal'));
            }
        });
        // set new default
        if (new_default != defaultval)
        {
            jQuery(item_id).val(new_default);
            weriseApp.dbg('DropDown','newval',jQuery(item_id).val());
        }
        hideLoader(item_id);        
        jQuery(item_id).trigger('change');
    };
    function initOptions(item_id,optionlabel) {
        // populate dropdown
        var dropdown = jQuery(item_id);
        jQuery(item_id + ' option').remove();
        jQuery(item_id + ' optgroup').remove();
        if (optionlabel!=='')
        {
            var newitem = newGroup(optionlabel+' »');
            dropdown.append(newitem.attr("class",'select-extra'));
        }
        return dropdown;
    };

    function newGroup(grouplabel) {
        var glabel = jQuery('<div/>').html(grouplabel).text();
        return jQuery("<optgroup>").attr('label',glabel);
    };

    function newOption(optionlabel,optionvalue) {
        return jQuery("<option></option>").attr("value",optionvalue).html(optionlabel);
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
    };
    /**
     * hide imgloader for drodown
     * @param {type} item_id
     * @returns {undefined}
     */
    function hideLoader(item_id)
    {
        jQuery(item_id).css("background-image", "")
            .css("color", "#555555");
    };
    function indentOpt(width)
    {
        if (width===0) return "";
        var filler = "&nbsp;" , dump = "";
        for (i=1;i<=width;i++)
        {
            dump = dump + filler;
        }
        return dump;
    };
}

/**
 * dropdown interface
 * @type type
 */
var DropDown = {
    /**
     * execute AJAX call to get variety
     * @param {string} country_key
     * @param {integer} station_key
     * @param {string} type_year
     * @param {string} item_id
     * @returns {null}
     */
    makeVariety: function(country_key, station_key, type_year, item_id)
    {
        weriseApp.dbg('DropDown','args',item_id+' '+station_key+' '+type_year);
        var year = parseInt(type_year.substr(1, 4)) || 0;
        var wtype = type_year.substr(0, 1);
        if (year===0)
        {
            return;
        }
        // build URL
        var url = new UrlBuilder('');
        url.addArg('pageaction','lookup');
        url.addArg('action','varieties');
        url.addArg('country',country_key);
        url.addArg('station',station_key);
        url.addArg('year',year);
        url.addArg('wtype',wtype);
        weriseApp.ajax(url.getUrl()).done(function(data) {
            var dd = new DropDownBase;
            dd.buildAll(data, item_id, '', weriseApp.getStoredVariety());
        });
    },
    /**
     * execute AJAX call to get station data
     * @param {string} country_key
     * @param {int} item_station_id
     * @param {string} ctype
     * @returns {void}
     */
    makeStation: function(country_key, item_id, ctype) {
        weriseApp.dbg('DropDown','args',item_id+' '+country_key);
        // build URL
        var url = new UrlBuilder('');
        url.addArg('pageaction','lookup');
        url.addArg('action','station');
        url.addArg('country',country_key);
        url.addArg('ctype',ctype);
        weriseApp.ajax(url.getUrl()).done(function(data) {
            // save data
            weriseApp.dropDownData.station = data;
            // transform data
            var newdata = [];
            var last_region_id = -1, last_subregion_id = -1, region_id = 0, subregion_id = 0;
            jQuery.each(data, function(i){
                region_id = parseInt(data[i].region_id);
                if (region_id != last_region_id)
                {
                    newdata.push(['GRP',data[i].region_name, 1]);
                }
                subregion_id = parseInt(data[i].subregion_id);
                if (subregion_id != last_subregion_id)
                {
                    newdata.push([data[i].station_id,data[i].subregion_name, 2]);
                }
                //newdata.push([data[i].station_id,data[i].station_name, 3]);
                last_region_id = region_id;
                last_subregion_id = subregion_id;
            });
            var dd = new DropDownBase;
            dd.buildAll(newdata, item_id, '', weriseApp.getStoredStation());
        });
    },
    getStationName : function (station_id) {
        var ret = '';
        // get station raw data
        var stations = weriseApp.dropDownData.station;
        // search for the station
        jQuery.each(stations, function(i) {
            if (station_id == stations[i].station_id) {
                ret = stations[i].subregion_name + ', ' + stations[i].region_name;
                //ret = stations[i].station_name + ', ' + stations[i].subregion_name + ', ' + stations[i].region_name;
                return;
            }
        });
        return ret;
    },
    /**
     * execute AJAX call to get station data
     * @param {string} country_key
     * @param {int} item_station_id
     * @param {string} ctype
     * @returns {void}
     */
    makeWvar: function(country_key, station_id, type_year, item_id) {
        var year = parseInt(type_year.substr(1, 4)) || 0;
        var wtype = type_year.substr(0, 1);
        if (year===0)
        {
            return;
        }
        // show loader image
        showLoader(item_id);
        // build URL
        var url = new UrlBuilder('');
        url.addArg('pageaction','lookup');
        url.addArg('action','wvar');
        url.addArg('country',country_key);
        url.addArg('station',station_id);
        url.addArg('year',year);
        url.addArg('wtype',wtype);
        weriseApp.ajax(url.getUrl()).done(function(data) {
            // transform data
            var newdata = [];
            jQuery.each(data, function(i){
                newdata.push([data[i].wvar_id,data[i].wvar_name]);
            });
            var dd = new DropDownBase;
            dd.buildAll(newdata, item_id, _t('Measured Variable'), window.dataselect.wvar);
        });
    },
    makeYear : function(country_key, station_key, dbsource, item_tyear_ids) {
        weriseApp.dbg('DropDown','args',station_key);
        // reset if no station is selected
        if (station_key==0)
        {
            for (var i = 0; i < item_tyear_ids.length; i++) {
                var dd = new DropDownBase;
                dd.buildAll(false, item_tyear_ids[i], '', false);
            }
            return;
        }
        var url = new UrlBuilder('');
        url.addArg('pageaction','lookup');
        url.addArg('action','stationyear');
        url.addArg('country',country_key);
        url.addArg('station',station_key);
        url.addArg('dbsource',dbsource);
        weriseApp.ajax(url.getUrl()).done(function(data) {
            // transform data
            var newdata = [];
            var last_wtype = '';
            jQuery.each(data, function(i){
                if (data[i].wtype != last_wtype)
                {
                    newdata.push(["GRP", getWeatherType(data[i].wtype), 1]);
                }
                newdata.push([data[i].wtype+data[i].year, data[i].year, 2]);
                last_wtype = data[i].wtype;
            });
            for (var i = 0; i < item_tyear_ids.length; i++) {
                var dd = new DropDownBase;
                dd.buildAll(newdata, item_tyear_ids[i], '', window.dataselect.wtype+window.dataselect.year);
            }
        });
    },
    setDefault : function(select_id,default_value) {
        jQuery("#"+select_id+' option[value="'+default_value+'"]').attr("selected",true);
    }        
}
