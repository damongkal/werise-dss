/**
 * global storage of ajaxdata
 * @type type
 */
var CombiListStorage = {
    ajaxdata: [],
    save: function(data)
    {
        this.ajaxdata = data;
    },
    getRunData: function(runnum)
    {
        return this.ajaxdata.hiyld.runnums[runnum];        
    }
};

/**
 * item IDs of two-crop calendar
 * @type type
 */
var CombiListForm = {
    country : '#cs2_country',
    station : '#cs2_station',
    tyear : '#cs2_type_year',
    adv_type : 'cs2_type',
    c1month : '#crop1_month',
    c1date : '#cs2_month1',
    c1variety : '#cs2_variety1',
    c1fert : '#cs2_fertil1',
    c2date : '#cs2_month2',
    c2variety : '#cs2_variety2',
    c2fert : '#cs2_fertil2', 
    tmpvariety1 : '#cs2r_variety1',    
    tmpvariety2 : '#cs2r_variety2',    
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
    initForm: function() {
        this.renderStation(this.getCountry());

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
            DropDown.makeVariety(
                jQuery(this.country).val(),
                weriseApp.getStoredStation(),
                'r'+weriseApp.getStoredYear(),
                this.tmpvariety1);
            DropDown.makeVariety(
                jQuery(this.country).val(),
                weriseApp.getStoredStation(),
                'r'+weriseApp.getStoredYear(),
                this.tmpvariety1);
            DropDown.makeVariety(
                jQuery(this.country).val(),
                weriseApp.getStoredStation(),
                'r'+weriseApp.getStoredYear(),
                this.c1variety);
            DropDown.makeVariety(
                jQuery(this.country).val(),
                weriseApp.getStoredStation(),
                'r'+weriseApp.getStoredYear(),
                this.c2variety);                
        }
        
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
            CombiListForm.populateSowDate1(jQuery(this).val(), CombiListForm.c1date);
            DropDown.makeVariety(
                jQuery(CombiListForm.country).val(),
                jQuery(CombiListForm.station).val(),
                jQuery(this).val(),
                CombiListForm.tmpvariety1);            
            DropDown.makeVariety(
                jQuery(CombiListForm.country).val(),
                jQuery(CombiListForm.station).val(),
                jQuery(this).val(),
                CombiListForm.tmpvariety2);            
            DropDown.makeVariety(
                jQuery(CombiListForm.country).val(),
                jQuery(CombiListForm.station).val(),
                jQuery(this).val(),
                CombiListForm.c1variety);            
            DropDown.makeVariety(
                jQuery(CombiListForm.country).val(),
                jQuery(CombiListForm.station).val(),
                jQuery(this).val(),
                CombiListForm.c2variety);
        });
        // change 2nd sowdate when 1st sowdate change
        jQuery(this.c1date).change(function() {
            CombiListForm.populateSowDate2(jQuery(this).val(), CombiListForm.c2date);
        });
        // two-crop extra parameters
        jQuery('#showreco').click(function() {
            jQuery('input[name='+CombiListForm.adv_type+']').val('recommend');
            jQuery('.cs2_recommend').show();
            jQuery('#showreco').hide();
            jQuery('#showcustom').hide();
        });                        
        jQuery('#showcustom').click(function() {
            jQuery('input[name='+CombiListForm.adv_type+']').val('custom');
            jQuery('.cs2_custom').show();
            jQuery('#showreco').hide();
            jQuery('#showcustom').hide();
        });                
        // show two-crop calendar list
        jQuery('.showcombi').click(function() {
            jQuery('#homeimages').hide();
            if (CombiListForm.validateForm())
            {
                CombiListForm.showCombinations();
            }
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
        jQuery("#location2_name").val(DropDown.getStationName(station_id));

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
            this.showError(_t('Please select Country.'));
            return false;
        }
        // validate station
        var station_id = parseInt(jQuery(this.station).val()) || 0;
        if (station_id===0)
        {
            this.showError(_t('Please select Station.'));
            return false;
        }
        // validate type-year
        var type_year = jQuery(this.tyear).val();
        var year = parseInt(type_year.substr(1, 4)) || 0;
        var wtype = type_year.substr(0, 1);
        if (year===0)
        {
            this.showError(_t('Please select Year'));
            return false;
        }
        if (wtype!=='r' && wtype!=='f')
        {
            this.showError(_t('Please select Year'));
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
                this.showError(_t('Please select Sow Date'));
                return false;
            }            
            var variety1 = jQuery(this.c1variety).val();        
            if (variety1==='')
            {
                this.showError(_t('Please select Variety'));
                return false;
            }        
            var fert1 = jQuery(this.c1fert).val();        
            if (fert1==='')
            {
                this.showError(_t('Please select Fertilizer Application'));
                return false;
            }            
            // validate variety
            var sowdate2 = jQuery(this.c2date).val();        
            if (sowdate2==='0')
            {
                this.showError(_t('Please select Sow Date'));
                return false;
            }            
            var variety2 = jQuery(this.c2variety).val();        
            if (variety2==='')
            {
                this.showError(_t('Please select Variety'));
                return false;
            }        
            var fert2 = jQuery(this.c2fert).val();        
            if (fert2==='')
            {
                this.showError(_t('Please select Fertilizer Application'));
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
            monthcode = formatDate2(d,'','abbr2');
            monthdesc = formatDate2(d,'m-y','abbr');
            newitem = jQuery("<option></option>");
            newitem.attr("value",monthcode).text(monthdesc);
            dropdown.append(newitem);
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
        //dropdown.append(jQuery('<option></option>').attr("class",'select-extra').attr("value",0).text(_t('Sow Date')+' »'));    
        var newitem = jQuery("<option></option>");
        for(i=0; i<6; i++) {
            monthcode = formatDate2(d,'','abbr2');
            monthdesc = formatDate2(d,'m-y','abbr');
            newitem = jQuery("<option></option>");           
            newitem.attr("value",monthcode).text(monthdesc);
            if (i===0)
            {
                newitem.attr("selected","selected");                
            }            
            dropdown.append(newitem);
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
        if (cstype==='recommend')
        {
            url.addArg('c1month',0);
        } else
        {
            url.addArg('c1date',jQuery(this.c1date).val());
            url.addArg('c1variety',jQuery(this.c1variety).val());
            url.addArg('c1fert',jQuery(this.c1fert).val());
            url.addArg('c2date',jQuery(this.c2date).val());
            url.addArg('c2variety',jQuery(this.c2variety).val());
            url.addArg('c2fert',jQuery(this.c2fert).val());
        }
        //showLoaderChart('#chart1');
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
        jQuery("#dataselection2").hide();            
        jQuery("#twocropcal").show();
        jQuery("#advisory").show();
        jQuery(".rainadv-twocc").show();        
        jQuery("#total-production-div").show();
        jQuery("#supplement-irrig").show();
    },
    showAdvisory: function(first_idx,second_idx)
    {
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
        AllCalendars.showTotalYield(first_data,second_data);
        // weather chart
        var newcal = new CropCalendar;
        newcal.setCalendar(first_data);
        var newcal2 = new CropCalendar;
        newcal2.setCalendar(second_data);    
        var weather_chart = new OryzaWeatherChart();
        weather_chart.setAjaxPage('weather2');
        weather_chart.setCalendar1(_t('Crop')+' 1',CropCalendarChart.getProps(newcal));
        weather_chart.setCalendar2(_t('Crop')+' 2',CropCalendarChart.getProps(newcal2));
        var country,station_id,year,type_year,wtype,month;    
        country = jQuery(CombiListForm.country).val();
        station_id = jQuery(CombiListForm.station).val();
        type_year = jQuery(CombiListForm.tyear).val();
        year = type_year.substr(1, 4);
        wtype = type_year.substr(0, 1);
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
        // focus on advisory
        document.getElementById('advisory_anchor').scrollIntoView();
    },
    /**
     * display error in chart div
     */
    showError: function(error_txt)
    {
        var error_txt2 = _t('There was a problem generating the data you requested. Please contact the website Administrator.');
        if (error_txt==='')
        {
            jQuery('#dss-error-box').html(error_txt2).show();
        } else
        {
            jQuery('#dss-error-box').html(error_txt).show();
        }
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
    }        
};


/**
 * two-crop combination table
 * @type type
 */
var TwoCropCombination = {
    makeTable: function(toprecs,runnums) {    
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
        var prot = jQuery("#twocropcalbest").find("tbody");
        var tmp = '<tr id="twocrop_'+idx+'" class="twocropall1" style="background-color:#EFEFEF">';
        tmp = tmp + '<td>' + WeriseTerms.getVarietyLabel(rec.variety) + '</td>';
        tmp = tmp + '<td style="text-align:right">' + rec.rain_amt.toFixed(1) + '<br />' + rec.rain_code + '</td>';
        tmp = tmp + '<td>' + formatDate2(rec.sow_date,'','abbr') + '<br />' + formatDate2(rec.harvest_date,'','abbr') + '</td>';
        tmp = tmp + '<td style="text-align:right">' + rec.yield.toFixed(2) + '</td>';
        tmp = tmp + '<td colspan="3">&nbsp;</td>';
        tmp = tmp + '<td style="text-align:right;font-weight:700" colspan="2">' + minyld +' to '+ maxyld + '</td>';
        tmp = tmp + '<td>&nbsp;</td>';
        tmp = tmp + '</tr>';
        prot.append(tmp);
    },
    addSecondCrop: function(idx, idx2, rec, rec2)
    {
        var prot = jQuery("#twocropcalbest").find("tbody");
        var totalyld = parseFloat(rec.yield) + parseFloat(rec2.yield);
        var tmp = '<tr id="twocrop_'+idx+'_'+idx2+'" class="twocropall2">';
        tmp = tmp + '<td colspan="4">&nbsp;</td>';
        tmp = tmp + '<td>' + WeriseTerms.getVarietyLabel(rec2.variety) + '</td>';
        tmp = tmp + '<td style="text-align:right">' + rec2.rain_amt.toFixed(1) + '<br />' + rec2.rain_code + '</td>';
        tmp = tmp + '<td>' + formatDate2(rec2.sow_date,'','abbr') + '<br />' + formatDate2(rec2.harvest_date,'','abbr') + '</td>';
        tmp = tmp + '<td style="text-align:right">' + rec2.yield.toFixed(2) + '</td>';
        tmp = tmp + '<td style="text-align:right">' + totalyld.toFixed(2) + '</td>';
        tmp = tmp + '<td style="text-align:center"><a href="javascript:;" role="button" class="btn btn-small btn-success twocropbtn"><i class="icon-zoom-in icon-white"></i> </a></td>';
        tmp = tmp + '</tr>';
        prot.append(tmp);    
    },
    showChoice: function(first_idx,second_idx)
    {
        // highlight combination
        jQuery('.twocropall1').css('background-color','#efefef');
        jQuery('#twocrop_'+first_idx).css('background-color','yellow');
        jQuery('.twocropall2').css('background-color','#ffffff');
        jQuery('#twocrop_'+first_idx+'_'+second_idx).css('background-color','yellow');        
    } 
};
