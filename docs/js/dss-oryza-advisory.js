var OryzaAdvisory = {
    /**
     * build the advisory content
     * @param {type} ajaxdata
     * @returns {undefined}
     */
    showAdvisory: function (ajaxdata) {
        // populate all calendars
        AllCalendars.makeTable(ajaxdata.hi_yld);
        var defval = ajaxdata.hi_yld[0];
        AllCalendars.showChoice(defval.dataset_id + '_' + defval.runnum);
        // high yield table
        HighYieldTable.makeTable(ajaxdata.hi_yld);
        HighYieldTable.showChoice(defval[0]);
        // yield comparison
        showAdvisory_Compare(ajaxdata);
        // show weather chart of top record
        this.showCropCal(defval[0]);
        jQuery('#advisory').show();
        jQuery('.adv-rainfall').show();
        // fertilizer advisory
        if (jQuery("#fertil").val() === 0)
        {
            jQuery('#fert-apply-adv').hide();
        }
    },
    showCropCal: function (sowdate) {
        // for 99 get selected from dropdown
        if (sowdate === 99)
        {
            sowdate = parseInt(jQuery('#preferred_sowdate').val());
        }
        // highlight choice
        HighYieldTable.showChoice(sowdate);
        // get high yield record
        var yld_rec = HighYieldStorage.getRecordBySowDate(sowdate);
        var newcal = new CropCalendar;
        newcal.setCalendar(yld_rec);
        // weather advisory
        jQuery('#f-rain').html(WeatherAdvisory.getRainCategory(yld_rec.rain_code, yld_rec.rain_amt));
        jQuery('#weather-advisory-div').show();
        // show calendar
        AllCalendars.showChoice(newcal.dataset_id + '_' + newcal.runnum);
        // show weather chart
        this.showWeatherChart(newcal);        
    },
    showWeatherChart : function (calendar_data) {
        var weather_chart = new OryzaWeatherChart();
        weather_chart.setAjaxPage('weather2');
        var type_year = OryzaFormV1.getYear('');
        var wtype = type_year.substr(0, 1);
        weather_chart.setCalendar1('Crop Calendar', CropCalendarChart.getProps(calendar_data));
        weather_chart.makeChart(OryzaFormV1.getCountry(), OryzaFormV1.getStation(), 0, wtype, false);
    }
}

var HighYieldTable = {
    prot: false,
    sowdates: [],
    makeTable: function (allcropdata)
    {
        var cropcalendar = false;
        this.prot = jQuery("#opt-planting").find("tbody");
        for (var k in allcropdata)
        {
            cropcalendar = allcropdata[k];
            // accumulate runnums
            this.sowdates.push(cropcalendar[0]);
            // display high yield only
            if (cropcalendar[2] === true)
            {
                this.addRecord(cropcalendar);
            }
        }
        this.addCustom();
    },
    addRecord: function (cropcalendar)
    {
        var sowdate = cropcalendar[0];
        var tmp = '<tr id="hiyldidx' + sowdate + '" class="hi_yld_tr">';
        tmp = tmp + '<td style="text-align:right">' + cropcalendar.rain_amt + '</td>';
        tmp = tmp + '<td>' + cropcalendar.rain_code + '</td>';
        tmp = tmp + '<td>' + formatDate2(sowdate, '', 'abbr') + '</td>';
        tmp = tmp + '<td style="text-align:right">' + cropcalendar[1].toFixed(2) + '</td>';
        tmp = tmp + '<td style="text-align:center"><a href="javascript:OryzaAdvisory.showCropCal(' + sowdate + ')" role="button" class="btn btn-small btn-success"><i class="icon-zoom-in icon-white"></i> </a></td>';
        tmp = tmp + '</tr>';

        this.prot.append(tmp);
    },
    addCustom: function () {
        // choose sowing date
        var tmp = '<tr id="hiyldidx99" class="hi_yld_tr">';
        tmp = tmp + '<td colspan="4">' + _t('Choose preferred') + ' : ';
        tmp = tmp + '<select name="preferred_sowdate" id="preferred_sowdate" style="width:150px;margin-bottom:0">';
        tmp = tmp + '<option value="">' + _t('Sowing Date') + ' &raquo;</option>';
        tmp = tmp + '</select>';
        tmp = tmp + '</td>';
        tmp = tmp + '<td style="text-align:center"><a href="javascript:OryzaAdvisory.showCropCal(99)" role="button" class="btn btn-small btn-success"><i class="icon-zoom-in icon-white"></i> </a></td>';
        tmp = tmp + '</tr>';
        this.prot.append(tmp);
        this.populateSowDate();
    },
    populateSowDate: function () {
        var dropdown = jQuery('#preferred_sowdate');
        this.sowdates.sort();
        var sowdate = 0;
        for (var i = 0; i < this.sowdates.length; i++)
        {
            sowdate = this.sowdates[i];
            dropdown.append(jQuery("<option></option>").attr("value", sowdate).text(formatDate(sowdate, 'm-d')));
        }
    },
    showChoice: function (idx) {
        jQuery('.hi_yld_tr').css('background-color', '#ffffff');
        jQuery('#hiyldidx' + idx).css('background-color', 'yellow');
    }
};

var AllCalendars = {
    prot: false,
    makeTable: function (allcropdata)
    {
        this.prot = jQuery("#opt-planting2").find("tbody");
        var cropnum = 1;
        for (var k in allcropdata)
        {
            this.addCalendar(cropnum, allcropdata[k]);
            cropnum++;
        }
        if (window.opt_show_npk === false)
        {
            jQuery('.npkamt').hide();
        }
        if (window.opt_show_alldates === false)
        {
            jQuery('.alldates').hide();
        }
    },
    addCalendar: function (cropnum, cropdata)
    {
        var newcal = new CropCalendar;
        newcal.setCalendar(cropdata);
        var fert = new FertilizerSchedule;
        variety_info = CombiListStorage.getVarietyInfo(cropdata.variety,'varcode');

        var tmp2 = '<tr id="calhead_' + newcal.dataset_id + '_' + newcal.runnum + '" class="allcalendar">';
        tmp2 = tmp2 + '<td colspan="10"><span class="cropnum"></span> &raquo; ';
        tmp2 = tmp2 + '<strong>Variety:</strong> ' + variety_info.variety_name + ' &bull; ';
        tmp2 = tmp2 + '<strong>Yield:</strong> ' + newcal.yield.toFixed(2) + ' t/ha';
        tmp2 = tmp2 + '</td>';
        tmp2 = tmp2 + '</tr>';
        this.prot.append(tmp2);

        var tmp = '<tr id="calendar_' + newcal.dataset_id + '_' + newcal.runnum + '" class="allcalendar">';
        tmp = tmp + '<td>' + formatDate2(newcal.sow_date, '', 'abbr') + '</td>';
        tmp = tmp + '<td class="alldates">' + formatDate2(newcal.panicle_init_date, '', 'abbr') + '</td>';
        tmp = tmp + '<td class="alldates">' + formatDate2(newcal.flower_date, '', 'abbr') + '</td>';
        tmp = tmp + '<td>' + formatDate2(newcal.harvest_date, '', 'abbr') + '</td>';
        //tmp = tmp + '<td style="text-align:right">' + newcal.yield.toFixed(2) + '</td>';
        tmp = tmp + '<td>' + fert.getApplyPeriod(newcal.fert_basal) + '</td>';
        tmp = tmp + '<td>' + fert.getApplyPeriod(newcal.fert_topdress1) + '</td>';
        tmp = tmp + '<td>' + fert.getApplyPeriod(newcal.fert_topdress2) + '</td>';
        tmp = tmp + '<td class="npkamt">' + fert.fert_basal_amt + '</td>';
        tmp = tmp + '<td class="npkamt">' + fert.fert_topdress1_amt + '</td>';
        tmp = tmp + '<td class="npkamt">' + fert.fert_topdress2_amt + '</td>';
        tmp = tmp + '</tr>';
        this.prot.append(tmp);
    },
    showChoice: function (first_idx, second_idx) {
        // hide all choices
        jQuery('.allcalendar').hide();
        // display first crop
        jQuery('#calhead_' + first_idx + ' .cropnum').addClass('badge').addClass('badge-info').html('First crop');        
        jQuery('#calhead_' + first_idx).show();
        jQuery('#calendar_' + first_idx).show();
        if (second_idx !== '')
        {
            // display second crop
            jQuery('#calhead_' + second_idx + ' .cropnum').addClass('badge').addClass('badge-warning').html('Second crop');
            jQuery('#calhead_' + second_idx).show();
            jQuery('#calendar_' + second_idx).show();
        }
    }

};


/**
 * water requirements advisory
 * @type type
 */
var WaterRequirement = {
    showAdvisory: function()
    {
        this.populateValues(1);
        this.populateValues(2);
    },
    populateValues: function(cropidx) {
        // get data from previous stored ajax call
        var crop_data = CombiListStorage.getRunDataByIndex(cropidx);

        // jQuery('#f-rain').html(WeatherAdvisory.getRainCategory(first_data.rain_code,first_data.rain_amt));
        var variety_info, deficit, reqt, pump_hours, pump_hours_total, fuel_consumed, fuel_cost;
        var pump_rate = parseInt(jQuery("#pump-rate").val());
        var fuel_rate = parseFloat(jQuery("#fuel-rate").val());
        var fuel_price = parseFloat(jQuery("#fuel-price").val());
        var farm_size = parseFloat(jQuery("#farm-size").val());

        // first crop
        variety_info = CombiListStorage.getVarietyInfo(crop_data.variety,'varcode');
        // transplant or dry seeding
        var tmp_date = new Date(crop_data.sow_date);
        var tmp_month = tmp_date.getMonth();
        var reqt_method = 'direct dry seeding';
        var reqt_method_month = '<br><small>sowing date is from July to February</small>';
        var reqt_depth = variety_info.tp_depth;
        if (tmp_month>=2 && tmp_month<=5) {
            reqt_method = 'transplanting';
            reqt_method_month = '<br><small>sowing date is from March to June</small>';
            var reqt_depth = variety_info.dds_depth;
        }
        var currency_name = CombiListStorage.getCountryInfo('currency');
        jQuery('#suppl-'+cropidx+'-var').html(variety_info.variety_name);
        jQuery('#suppl-'+cropidx+'-rain-amt').html(crop_data.rain_amt);
        jQuery('#suppl-'+cropidx+'-rain-code').html(crop_data.rain_code);
        jQuery('#suppl-'+cropidx+'-2').html(reqt_depth);
        jQuery('#suppl-'+cropidx+'-method').html(reqt_method+reqt_method_month);
        deficit = parseInt(crop_data.rain_amt - reqt_depth);
        if (deficit<0) {
            jQuery('#suppl-'+cropidx+'-3').html(Math.abs(deficit));
            jQuery('#suppl-'+cropidx+'-sched').html('Drought period (5-6 day interval)');            
            deficit = Math.abs(deficit);
            reqt = deficit * 10000 * 1000 / 1000;
            pump_hours = reqt / pump_rate / 3600;
            pump_hours_total = '('+parseInt(pump_hours)+' hr/ha) X ('+farm_size+' ha) = '+parseInt(pump_hours * farm_size)+' hr';
            jQuery('#suppl-'+cropidx+'-4').html(pump_hours_total);
            fuel_consumed = pump_hours * fuel_rate * farm_size;
            jQuery('#suppl-'+cropidx+'-5').html(parseInt(fuel_consumed) + ' L');
            fuel_cost = parseInt(fuel_consumed * fuel_price);
            jQuery('#suppl-'+cropidx+'-6').html(fuel_cost.toLocaleString('en') + ' ' + currency_name);
        } else {
            jQuery('#suppl-'+cropidx+'-3').html('0');
            jQuery('#suppl-'+cropidx+'-sched').html('Irrigation not needed');
            jQuery('#suppl-'+cropidx+'-4').html('');
            jQuery('#suppl-'+cropidx+'-5').html('');
            jQuery('#suppl-'+cropidx+'-6').html('');
        }
    }
};

/**
 * total production advisory
 * @type type
 */
var TotalProduction = {
    showAdvisory: function()
    {
        // get data from previous stored ajax call
        var first_data = CombiListStorage.getRunDataByIndex(1);
        var second_data = CombiListStorage.getRunDataByIndex(2);
        // initialize variables
        var farm_size = parseFloat(jQuery("#farm-size").val());
        var unit_yield, actual_yield, surplus;
        var unit_yield_total, actual_yield_total, surplus_total;

        // family consume
        var family_count_young = parseInt(jQuery("#family-num-young").val());
        var family_count_old = parseInt(jQuery("#family-num-old").val());
        var person_consume = 59.75 / 1000; // rice (t) consumed by 1 person for 6 months
        var family_consume = (person_consume * family_count_young / 2) + (person_consume * family_count_old); // family consumed
        var family_consume_total = family_consume * 2;

        // first crop
        unit_yield = first_data.yield; // unit yield
        unit_yield_total = unit_yield;
        jQuery('#grain_yield1').html(unit_yield.toFixed(2));
        var image_width = Math.floor(46 * unit_yield);
        jQuery('#rice-sack1').css('width', image_width);
        actual_yield = unit_yield * farm_size;// actual production
        actual_yield_total = actual_yield;
        jQuery('#actual-yield-1').html(actual_yield.toFixed(2));
        jQuery('#yield-consume-1').html(family_consume.toFixed(2));
        surplus = actual_yield - family_consume; // surplus
        surplus_total = surplus;
        jQuery('#yield-surplus-1').html(surplus.toFixed(2));

        // second crop
        unit_yield = second_data.yield; // unit yield
        unit_yield_total += unit_yield;
        jQuery('#grain_yield2').html(unit_yield.toFixed(2));
        var image_width = Math.floor(46 * unit_yield);
        jQuery('#rice-sack2').css('width', image_width);
        actual_yield = unit_yield * farm_size;// actual production
        actual_yield_total += actual_yield;
        jQuery('#actual-yield-2').html(actual_yield.toFixed(2));
        jQuery('#yield-consume-2').html(family_consume.toFixed(2));
        surplus = actual_yield - family_consume; // surplus
        surplus_total += surplus;
        jQuery('#yield-surplus-2').html(surplus.toFixed(2));

        // total grain yield
        jQuery('#total_grain_yield').html(unit_yield_total.toFixed(2));
        jQuery('#actual-yield-3').html(actual_yield_total.toFixed(2));
        jQuery('#yield-consume-3').html(family_consume_total.toFixed(2));
        jQuery('#yield-surplus-3').html(surplus_total.toFixed(2));

        jQuery('#total_grain_yield_p').show();
    }
};

function showAdvisory_Compare(ajaxdata)
{
    jQuery('#highest-compare-diff-seta').html(ajaxdata.compare_seta);
    jQuery('#highest-compare-diff-setb').html(ajaxdata.compare_setb);
    jQuery('#highest-compare-diff-yld').html(ajaxdata.compare_yld);
    jQuery('#highest-compare-diff-date').html(ajaxdata.compare_date);

    if (ajaxdata.compare_setb !== "")
    {
        jQuery('#highest-compare-diff').show();
    }
}