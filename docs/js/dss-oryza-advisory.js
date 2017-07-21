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
        // focus on advisory
        document.getElementById('advisory_anchor').scrollIntoView();
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
        var crop = 1, croptitle = '1st';
        for (var k in allcropdata)
        {
            if (crop > 1)
            {
                croptitle = '2nd';
            }
            this.addCalendar(croptitle, allcropdata[k]);
            crop++;
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
    addCalendar: function (croptitle, cropdata)
    {
        var newcal = new CropCalendar;
        newcal.setCalendar(cropdata);
        var fert = new FertilizerSchedule;

        var tmp = '<tr id="calendar_' + newcal.dataset_id + '_' + newcal.runnum + '" class="allcalendar">';
        tmp = tmp + '<td style="font-weight:700">' + croptitle + '</td>';
        tmp = tmp + '<td>' + formatDate2(newcal.sow_date, '', 'abbr') + '</td>';
        tmp = tmp + '<td class="alldates">' + formatDate2(newcal.panicle_init_date, '', 'abbr') + '</td>';
        tmp = tmp + '<td class="alldates">' + formatDate2(newcal.flower_date, '', 'abbr') + '</td>';
        tmp = tmp + '<td>' + formatDate2(newcal.harvest_date, '', 'abbr') + '</td>';
        tmp = tmp + '<td style="text-align:right">' + newcal.yield.toFixed(2) + '</td>';
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
        // diplay the choice
        jQuery('.allcalendar').hide();
        jQuery('#calendar_' + first_idx).show();
        if (second_idx !== '')
        {
            jQuery('#calendar_' + second_idx).show();
        }
    },
    showTotalYield: function (first_data, second_data) {
        // first crop
        jQuery('#grain_yield1').html(first_data.yield.toFixed(2));
        var image_width = Math.floor(46 * first_data.yield);
        jQuery('#rice-sack1').css('width', image_width);
        // second crop        
        jQuery('#grain_yield2').html(second_data.yield.toFixed(2));
        var image_width2 = Math.floor(46 * second_data.yield);
        jQuery('#rice-sack2').css('width', image_width2);
        // total grain yield
        var total_yield = first_data.yield + second_data.yield;
        jQuery('#total_grain_yield').html(total_yield.toFixed(2));
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