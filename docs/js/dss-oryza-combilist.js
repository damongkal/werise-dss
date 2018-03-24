/**
 * global storage of ajaxdata
 * @type type
 */
var CombiListStorage = {
    idx1: 0,
    idx2: 0,
    ajaxdata: [],
    setIndex: function(idx1,idx2)
    {
        this.idx1 = idx1;
        this.idx2 = idx2;
    },
    getIndex: function()
    {
        return [this.idx1,this.idx2];
    },
    save: function(data)
    {
        this.ajaxdata = data;
    },
    getRunData: function(runnum)
    {
        return this.ajaxdata.hiyld.runnums[runnum];
    },
    getRunDataByIndex: function(idx)
    {
        var runnum = 0;
        if (idx===1) {
            runnum = this.idx1;
        } else {
            runnum = this.idx2;
        }
        return this.ajaxdata.hiyld.runnums[runnum];
    },
    getVarietyInfo: function(variety_name, field_index) {
        weriseApp.dbg('getVarietyInfo','args',variety_name + ' - ' + field_index);
        var variety_data = weriseApp.getVarietyInfo();
        for(var i=0; i<variety_data.length; i++) {
            if (field_index==='varname' && variety_data[i].variety_name == variety_name) {
                return variety_data[i];
            };
            if (field_index==='varcode' && variety_data[i].variety_code == variety_name) {
                return variety_data[i];
            };
        };
        return false;
    },
    getCountryInfo: function(fieldname) {
        var country_code = CombiListForm.getCountry();
        if (fieldname==='currency') {
            if (country_code == 'ID') {
                return 'Rupiah';
            }
            if (country_code == 'PH') {
                return 'Peso';
            }
            if (country_code == 'LA') {
                return 'Kip';
            }
            if (country_code == 'TH') {
                return 'Baht';
            }            
        }
    }
};

/**
 * two-crop combination table
 * @type type
 */
var TwoCropCombination = {
    prot: false,
    makeTable: function(toprecs,runnums) {
        this.prot = jQuery("#twocropcalbest").find("tbody");
        var idx, rec, idx2, rec2;
        for (var i=0; i<toprecs.length; i++)
        {
            idx = toprecs[i].first.dataset_id+'_'+toprecs[i].first.runnum;
            rec = runnums[idx];
            this.addFirstCrop(idx, rec, toprecs[i].minyld, toprecs[i].maxyld);
            for (var j=0; j<toprecs[i].second.length; j++)
            {
                idx2 = toprecs[i].second[j].dataset_id+'_'+toprecs[i].second[j].runnum;
                rec2 = runnums[idx2];
                this.addSecondCrop(idx, idx2, rec, rec2);
            }
        }

        // choose two calendar combination
        jQuery('.twocropbtn').click(function() {
            var tr = jQuery(this).parent().parent().attr('id');
            var token = tr.split("_");
            var idx1 = token[1]+'_'+token[2];
            var idx2 = token[3]+'_'+token[4];
            CombiListForm.showAdvisory(idx1,idx2);
        });
    },
    addFirstCrop: function(idx, rec, minyld, maxyld) {
        var tmp = '<tr id="twocrop_'+idx+'" class="twocropall1">';
        tmp = tmp + '<td>' + formatDate2(rec.sow_date,'','abbr') + '<br />' + formatDate2(rec.harvest_date,'','abbr') + '</td>';        
        tmp = tmp + '<td>&nbsp;</td>';
        tmp = tmp + '<td>' + WeriseTerms.getVarietyLabel(rec.variety) + '</td>';
        tmp = tmp + '<td>' + rec.rain_amt.toFixed(1) + '<br />' + rec.rain_code + '</td>';
        tmp = tmp + '<td class="text-right">' + rec.yield.toFixed(2) + '</td>';
        tmp = tmp + '<td>&nbsp;</td>';
        //tmp = tmp + '<td class="text-right"><strong>' + minyld +' to '+ maxyld + '</strong></td>';
        tmp = tmp + '</tr>';
        this.prot.append(tmp);
    },
    addSecondCrop: function(idx, idx2, rec, rec2)
    {
        var totalyld = parseFloat(rec.yield) + parseFloat(rec2.yield);
        var tmp = '<tr id="twocrop_'+idx+'_'+idx2+'" class="twocropall2">';
        tmp = tmp + '<td>&nbsp;</td>';        
        tmp = tmp + '<td>' + formatDate2(rec2.sow_date,'','abbr') + '<br />' + formatDate2(rec2.harvest_date,'','abbr') + '</td>';        
        tmp = tmp + '<td>' + WeriseTerms.getVarietyLabel(rec2.variety) + '</td>';
        tmp = tmp + '<td>' + rec2.rain_amt.toFixed(1) + '<br />' + rec2.rain_code + '</td>';
        tmp = tmp + '<td class="text-right">' + rec2.yield.toFixed(2) + '</td>';
        tmp = tmp + '<td class="text-right">' + totalyld.toFixed(2) + '<br />';
        tmp = tmp + '<a role="button" class="twocropbtn btn btn-outline-success btn-sm" href="javascript:;"><i class="fas fa-star"></i> Choose</a>';
        tmp = tmp + '</td>';        
        tmp = tmp + '</tr>';
        this.prot.append(tmp);
    },
    showChoice: function(first_idx,second_idx)
    {
        // highlight combination
        jQuery('.twocropall1').removeClass('table-info');
        jQuery('#twocrop_'+first_idx).addClass('table-info');
        jQuery('.twocropall2').removeClass('table-warning');
        jQuery('#twocrop_'+first_idx+'_'+second_idx).addClass('table-warning');
    }
};

/**
 * item IDs of two-crop calendar
 * @type type
 */
var CombiListForm = {
    // dataset filters
    country : '#cs2_country',
    station : '#cs2_station',
    tyear : '#cs2_type_year',
    adv_type : 'cs2_type',
    // variety combination
    c1variety : '#cs2_variety1',    
    c2variety : '#cs2_variety2',    
    // custom advisory filters, crop 1
    c1month : '#crop1_month',
    c1date : '#cs2_month1',
    c1fert : '#cs2_fertil1',
    // custom advisory filters, crop 2
    c2date : '#cs2_month2',
    c2fert : '#cs2_fertil2',
    /**
     * getters
     * @param {type} item_id
     * @returns {jQuery}
     */
    _get: function (item_id) {
        return jQuery(item_id).val();
    },
    getCountry: function () {
        return this._get(this.country);
    },
    getStation: function () {
        return parseInt(this._get(this.station));
    },
    getTypeYear: function () {
        return this._get(this.tyear);
    },    
    bindEvents: function() {
        // initialize all when country change
        jQuery(this.country).change(function () {
            CombiListForm.countryChange();
        });

        // location change
        jQuery('#location2_btn').click(function () {
            jQuery('#location2_div').hide();
            jQuery(CombiListForm.station).show();
        });

        // change year when station change
        jQuery(this.station).change(function () {
            CombiListForm.stationChange();
        });

        // change rice variety when year change
        jQuery(CombiListForm.tyear).change(function() {
            var typeyear = jQuery(this).val();
            var year = typeyear.substr(1,4);
            var tyear = getWeatherType(typeyear.substr(0,1));
            CombiListForm.populateSowDate1(typeyear, CombiListForm.c1date);
            CombiListForm.makeVarietyData();
            jQuery('#adv-year').html(tyear+' '+year);
            jQuery("#type-year-pre").html(tyear);
        });

        // show variety data when variety change
        jQuery(CombiListForm.c1variety).change(function() {
            CombiListForm.showVarietyData(jQuery(CombiListForm.c1variety + ' option:selected').text(),'1');
        });
        // show variety data when variety change
        jQuery(CombiListForm.c2variety).change(function() {
            CombiListForm.showVarietyData(jQuery(CombiListForm.c2variety + ' option:selected').text(),'2');
        });

        // change 2nd sowdate when 1st sowdate change
        jQuery(this.c1date).change(function() {
            CombiListForm.populateSowDate2(jQuery(this).val(), CombiListForm.c2date);
        });
        
        // two-crop extra parameters
        /*
        jQuery('#showreco').click(function() {
            jQuery('input[name='+CombiListForm.adv_type+']').val('recommend');
            jQuery('.cs2_recommend').show();
            jQuery('#showreco').hide();
            jQuery('#showcustom').hide();
        });*/
        
        // show custom parameters
        jQuery('#showcustom').click(function() {
            jQuery('input[name='+CombiListForm.adv_type+']').val('custom');
            jQuery('.cs2_custom').show();
            jQuery('#showcustom').hide();
        });
        // show two-crop calendar list
        jQuery('#showcombi').click(function() {
            if (CombiListForm.validateForm())
            {
                jQuery('#crop-advisory-form').hide();                
                CombiListForm.showCombinations();
            }
        });
        // change farm size
        jQuery("#farm-size").change(function() {
            WaterRequirement.showAdvisory();
            TotalProduction.showAdvisory();
        });
        // change family count
        jQuery("#family-num-young").change(function() {
            TotalProduction.showAdvisory();
        });
        // change family count
        jQuery("#family-num-old").change(function() {
            TotalProduction.showAdvisory();
        });        
        // change pump rate
        jQuery("#pump-rate").change(function() {
            WaterRequirement.showAdvisory();
        });
        // change fuel rate
        jQuery("#fuel-rate").change(function() {
            WaterRequirement.showAdvisory();
        });
        // change fuel price
        jQuery("#fuel-price").change(function() {
            WaterRequirement.showAdvisory();
        });
        // fake!!!
        jQuery('#fakeimg01').click(function() {
            // jQuery('#fakeimg02').show();
        });
        // fake!!!
        jQuery('#showcustomgy').click(function() {
            jQuery('#fakeimg03').show();
        });
    },
    initForm: function() {
        this.renderStation(this.getCountry());
        
        // populate currency
        jQuery('.currency-name').html(CombiListStorage.getCountryInfo('currency'));

        // use previous selection
        if (weriseApp.getStoredStation()>0)
        {
            // populateYear(jQuery(this.country).val(), weriseApp.getStoredStation(), 'o', [this.tyear]);
        }
        // use previous selection
        if (weriseApp.getStoredYear()>0)
        {
            this.populateSowDate1('x'+weriseApp.getStoredYear(), this.c1date);
            this.populateSowDate2(jQuery(this.c1date).val(), CombiListForm.c2date);
            CombiListForm.makeVarietyData();
        }

        // hide previous results
        jQuery('#cropseason').hide();
        jQuery('#dataselection2').show();
        jQuery('#dataselection').hide();
        jQuery('.cs2_custom').hide();
        jQuery('#grainyield_chart').hide();
    },
    /**
     * value change
     */
    countryChange: function () {
        var country_code = this.getCountry();
        if (country_code=='')
        {
            jQuery(this.country).val('ID');
        }
        country_code = this.getCountry();
        weriseApp.dbg('change', 'country', country_code);
        this.renderStation(country_code);
    },
    /**
     * value change
     */
    stationChange: function () {
        var station_id = this.getStation();
        weriseApp.dbg('change', 'station', station_id);

        // populate location name
        var location_name = DropDown.getStationName(station_id);
        jQuery("#location2_name").val(location_name);
        jQuery("#adv-location").html(location_name);

        // show location placeholder
        jQuery('#location2_div').show();
        jQuery(this.station).hide();

        // populate year
        DropDown.makeYear(this.getCountry(), station_id, 'o', [this.tyear]);
    },
    validateForm: function()
    {
        // validate country
        var country = jQuery(this.country).val();
        if (country==='')
        {
            weriseApp.showError(_t('Please select Country.'));
            return false;
        }
        // validate station
        var station_id = parseInt(jQuery(this.station).val()) || 0;
        if (station_id===0)
        {
            weriseApp.showError(_t('Please select Station.'));
            return false;
        }
        // validate type-year
        var type_year = jQuery(this.tyear).val();
        var year = parseInt(type_year.substr(1, 4)) || 0;
        var wtype = type_year.substr(0, 1);
        if (year===0)
        {
            weriseApp.showError(_t('Please select Year'));
            return false;
        }
        if (wtype!=='r' && wtype!=='f')
        {
            weriseApp.showError(_t('Please select Year'));
            return false;
        }
        var cstype = jQuery('input[name='+this.adv_type+']').val();
        if (cstype==='recommend')
        {
            // month1 = 1;
        } else
        {
            // validate variety
            var sowdate1 = jQuery(this.c1date).val();
            if (sowdate1==='0')
            {
                weriseApp.showError(_t('Please select Sow Date'));
                return false;
            }
            var variety1 = jQuery(this.c1variety).val();
            if (variety1==='')
            {
                weriseApp.showError(_t('Please select Variety'));
                return false;
            }
            var fert1 = jQuery(this.c1fert).val();
            if (fert1==='')
            {
                weriseApp.showError(_t('Please select Fertilizer Application'));
                return false;
            }
            // validate variety
            var sowdate2 = jQuery(this.c2date).val();
            if (sowdate2==='0')
            {
                weriseApp.showError(_t('Please select Sow Date'));
                return false;
            }
            var variety2 = jQuery(this.c2variety).val();
            if (variety2==='')
            {
                weriseApp.showError(_t('Please select Variety'));
                return false;
            }
            var fert2 = jQuery(this.c2fert).val();
            if (fert2==='')
            {
                weriseApp.showError(_t('Please select Fertilizer Application'));
                return false;
            }
        }
        return true;
    },
    populateSowDate1: function(type_year, item_sowdate_id)
    {
        var year = type_year.substr(1, 4);
        var d = new Date(year, 0, 1, 0, 0, 0, 0);
        var monthcode='', monthdesc='';

        var dropdown = jQuery(item_sowdate_id);
        jQuery(item_sowdate_id + ' option').remove();
        var newitem = jQuery("<option></option>");
        for(i=0; i<12; i++) {
            // day 1
            d.setDate(1);
            monthcode = formatDate2(d,'','abbr2');
            monthdesc = formatDate2(d,'','abbr');
            newitem = jQuery("<option></option>");
            newitem.attr("value",monthcode).text(monthdesc);
            dropdown.append(newitem);
            // day 15
            d.setDate(15);
            monthcode = formatDate2(d,'','abbr2');
            monthdesc = formatDate2(d,'','abbr');
            newitem = jQuery("<option></option>");
            newitem.attr("value",monthcode).text(monthdesc);
            dropdown.append(newitem);
            // next month
            d.setMonth(d.getMonth() + 1);
        };
    },
    populateSowDate2: function(sowdate1, item_sowdate_id)
    {
        var sow1 = sowdate1.split('-');
        var d = new Date(parseInt(sow1[0]), parseInt(sow1[1]), 1, 0, 0, 0, 0);
        d.setMonth(d.getMonth() + 3);

        var monthcode='', monthdesc='';

        var dropdown = jQuery(item_sowdate_id);
        jQuery(item_sowdate_id + ' option').remove();
        var newitem = jQuery("<option></option>");
        for(i=0; i<6; i++) {
            // day 1
            d.setDate(1);
            monthcode = formatDate2(d,'','abbr2');
            monthdesc = formatDate2(d,'','abbr');
            newitem = jQuery("<option></option>");
            newitem.attr("value",monthcode).text(monthdesc);
            if (i===0)
            {
                newitem.attr("selected","selected");
            }
            dropdown.append(newitem);            
            // day 15
            d.setDate(15);
            monthcode = formatDate2(d,'','abbr2');
            monthdesc = formatDate2(d,'','abbr');
            newitem = jQuery("<option></option>");
            newitem.attr("value",monthcode).text(monthdesc);
            dropdown.append(newitem);
            // next month
            d.setMonth(d.getMonth() + 1);
        };
    },
    showCombinations: function ()
    {
        // external parameters, common
        var type_year = jQuery(this.tyear).val();
        var cstype = jQuery('input[name='+this.adv_type+']').val();
        // split type year
        var year = type_year.substr(1, 4);
        var wtype = type_year.substr(0, 1);
        // build URL
        var url = new UrlBuilder('');
        url.addArg('pageaction','oryza2');
        url.addArg('action','combilist');
        url.addArg('cstype',cstype);
        url.addArg('country',this.getCountry());
        url.addArg('station',this.getStation());
        url.addArg('year',year);
        url.addArg('wtype',wtype);
        url.addArg('c1variety',jQuery(this.c1variety).val());
        url.addArg('c2variety',jQuery(this.c2variety).val());
        if (cstype==='recommend')
        {
            url.addArg('c1month',0);
        } else
        {
            url.addArg('c1date',jQuery(this.c1date).val());
            url.addArg('c1fert',jQuery(this.c1fert).val());
            url.addArg('c2date',jQuery(this.c2date).val());
            url.addArg('c2fert',jQuery(this.c2fert).val());
        }
        weriseApp.ajax(url.getUrl()).done(function(data) {
            CombiListForm.showCombinationsCallBack(data);
        });
    },
    showCombinationsCallBack: function (data)
    {
        CombiListStorage.save(data);
        // top combinations
        var toprecs = data.hiyld.toprecs;
        var runnums = data.hiyld.runnums;
        TwoCropCombination.makeTable(toprecs,runnums);
        // all calendars
        AllCalendars.makeTable(runnums);
        // show default choice
        var idx = toprecs[0].first.dataset_id+'_'+toprecs[0].first.runnum;
        var idx2 = toprecs[0].second[0].dataset_id+'_'+toprecs[0].second[0].runnum;
        this.showAdvisory(idx,idx2);
        
        // yield chart
        // showOryzaChart(data.chart_yield);
        //var chart = new OryzaChart();
        //chart.callHighCharts('#chart1',data.chart_yield,'test');

        // cleanup display
        jQuery("#twocropcal").show();
        jQuery("#advisory").show();
    },
    showAdvisory: function(first_idx,second_idx)
    {
        // store crop choices
        CombiListStorage.setIndex(first_idx,second_idx);
        // get data from previous stored ajax call
        var first_data = CombiListStorage.getRunData(first_idx);
        var second_data = CombiListStorage.getRunData(second_idx);
        // highlight combination
        TwoCropCombination.showChoice(first_idx,second_idx);
        // weather advisory: rainfall category
        jQuery('#f-rain').html(WeatherAdvisory.getRainCategory(first_data.rain_code,first_data.rain_amt));
        jQuery('#f-rain2').html(WeatherAdvisory.getRainCategory(second_data.rain_code,second_data.rain_amt));
        // crop calendar
        AllCalendars.showChoice(first_idx,second_idx);
        // weather chart
        this.showWeatherChart(first_data,second_data);
        // water requirements
        WaterRequirement.showAdvisory();
        // total production advisory
        TotalProduction.showAdvisory();
        // focus on advisory
        document.getElementById('bread-crumbs').scrollIntoView();        
    },
    /**
     * display highcharts
     */
    showWeatherChart: function(first_data,second_data)
    {
        var newcal = new CropCalendar;
        newcal.setCalendar(first_data);
        var newcal2 = new CropCalendar;
        newcal2.setCalendar(second_data);
        var weather_chart = new OryzaWeatherChart();
        weather_chart.setAjaxPage('weather2');
        weather_chart.setCalendar1(_t('Crop')+' 1',CropCalendarChart.getProps(newcal));
        weather_chart.setCalendar2(_t('Crop')+' 2',CropCalendarChart.getProps(newcal2));
        var country, station_id, year, tyear, wtype,month;
        country = this.getCountry();
        station_id = this.getStation();
        tyear = this.getTypeYear();
        year = tyear.substr(1, 4);
        wtype = tyear.substr(0, 1);
        var cstype = jQuery('input[name='+this.adv_type+']').val();
        if (cstype==='recommend') {
            month = jQuery(this.c1month).val();
        } else
        {
            var tmpmonth = jQuery(this.c1date).val();
            var tmpmonth2 = tmpmonth.split('-');
            month = parseInt(tmpmonth2[1]) || 1;
        }
        weather_chart.makeChart(country,station_id,0,wtype,false);
    },
    /**
     *
     * @param {type} country_code
     * @returns {undefined}
     */
    renderStation: function (country_code) {
        // render station dropdown
        jQuery("#location2_name").val('');
        DropDown.makeStation(country_code, CombiListForm.station, 'o');
        // draw google maps
        draw_google_maps(country_code, 'o');
    },
    /**
     * process the variety dropdowns / info
     * @returns {undefined}
     */
    makeVarietyData: function () {
        // filters
        var country_key = jQuery(this.country).val();
        var station_key = jQuery(this.station).val() || '0';
        var type_year = jQuery(this.tyear).val();
        weriseApp.dbg('makeVarietyData','args',country_key+'-'+station_key+'-'+type_year);
        var year = parseInt(type_year.substr(1, 4)) || 0;
        var wtype = type_year.substr(0, 1);
        // validation
        if (parseInt(station_key)===0)
        {
            weriseApp.dbg('makeVarietyData','invalid station',station_key);
            return;
        }
        if (year===0)
        {
            weriseApp.dbg('makeVarietyData','invalid year',year);
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
        url.addArg('data-all','1');
        weriseApp.ajax(url.getUrl()).done(function(data) {
            // store data in cache
            weriseApp.setVarietyInfo(data);
            // create dropdowndata
            var ddata = [];
            var dd = new DropDownBase;
            for(var i=0; i<data.length; i++) {
                ddata.push([data[i].variety,data[i].variety_name]);
            };
            // drop down
            dd.buildAll(ddata, CombiListForm.c1variety, _t('Variety'), '');
            dd.buildAll(ddata, CombiListForm.c2variety, _t('Variety'), '');
            // show variety info for recommended
            CombiListForm.showVarietyData(jQuery(CombiListForm.c1variety + ' option:selected').text(),'1');
            CombiListForm.showVarietyData(jQuery(CombiListForm.c2variety + ' option:selected').text(),'2');
        });
    },
    /**
     * display variety data
     * @returns {undefined}
     */
    showVarietyData: function (variety_code, target_item) {
        weriseApp.dbg('showVarietyData','args',variety_code + ' - ' + target_item);
        var maturity = '-', maturity_grp = '';
        var yield_avg = '-' , yield_potential = '-';
        // get variety info from cache
        variety_info = CombiListStorage.getVarietyInfo(variety_code,'varname');
        if (variety_info) {
                maturity = variety_info.maturity_min;
                if (variety_info.maturity_min != variety_info.maturity_max) {
                    maturity = variety_info.maturity_min + ' - ' + variety_info.maturity_max;
                };
                if (variety_info.maturity_group=='S') {
                    maturity_grp = 'SHORT';
                }
                if (variety_info.maturity_group=='M') {
                    maturity_grp = 'MEDIUM';
                }
                if (variety_info.maturity_group=='L') {
                    maturity_grp = 'LONG';
                }                
                yield_avg = variety_info.yield_avg;
                yield_potential = variety_info.yield_potential;
                if (yield_potential === null) {
                    yield_potential = '(unknown)';
                }
        }

        // string replace
        var str = jQuery("#variety-info-template").html();
        str = str.replace('{{variety_name}}',variety_code);
        str = str.replace('{{maturity}}',maturity);
        str = str.replace('{{maturity_grp}}',maturity_grp);        
        str = str.replace('{{yield_avg}}',yield_avg);
        str = str.replace('{{yield_potential}}',yield_potential);
        jQuery("#variety-info-"+target_item).html(str);
    }

};