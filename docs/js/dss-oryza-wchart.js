function OryzaWeatherChart()
{
    this.item_chart = "#chart2";
    this.cropcalendar1 = false;
    this.cropcalendar2 = false;
    this.ajaxpage = "weather";
    this.country = '';
    this.station_id = 0;
    this.wtype = '';
    this.wvar = 0;
    this.location = '';
    this.startdate = 0;
    this.enddate = 0;

}
;
OryzaWeatherChart.prototype = {
    constructor: OryzaWeatherChart,
    setAjaxPage: function (ajaxpage) {
        this.ajaxpage = ajaxpage
    },
    /**
     * @param int startdate
     * @param int enddate
     * @returns {undefined}
     */
    setPeriod: function (startdate, enddate) {
        this.startdate = startdate;
        this.enddate = enddate;
    },
    /**
     * @param string cropcalname
     * @param CropCalendarChart cropcaldata
     * @returns {undefined}
     */
    setCalendar1: function (cropcalname, cropcaldata) {
        this.cropcalendar1 = {name: cropcalname, data: cropcaldata};
        var margin = 24 * 3600 * 1000;
        this.startdate = formatDate2(cropcaldata[0].x, 'm-y', 'abbr2');
        var last_idx = cropcaldata.length - 1;
        this.enddate = formatDate2(cropcaldata[last_idx].x + (margin * 90), 'm-y', 'abbr2');
    },
    /**
     * @param string cropcalname
     * @param CropCalendarChart cropcaldata
     * @returns {undefined}
     */
    setCalendar2: function (cropcalname, cropcaldata) {
        this.cropcalendar2 = {name: cropcalname, data: cropcaldata};
        var margin = 24 * 3600 * 1000;
        var last_idx = cropcaldata.length - 1;
        this.enddate = formatDate2(cropcaldata[last_idx].x + (margin * 90), 'm-y', 'abbr2');
    },
    makeChart: function (country, station_id, wvar, wtype, reuse)
    {
        this.country = country;
        this.station_id = station_id;
        this.wvar = wvar;
        this.wtype = wtype;

        // re-use weather chart
        if (reuse === true && typeof (window.ajaxdataw) != "undefined")
        {
            var chartdata = window.ajaxdataw.chart;
            if (this.cropcalendar1 !== false)
            {
                chartdata.series.pop();
            }
            if (this.cropcalendar2 !== false)
            {
                chartdata.series.pop();
            }
            this.callHighCharts(item_chart, chartdata);

            return;
        }
        this.getData();
    },
    getData: function () {
        var that = this;
        var pct = 0;
        if (this.wvar === 0)
        {
            pct = 1;
        }
        // ajax progressbar
        showLoaderChart(this.item_chart);
        // ajax
        var url = new UrlBuilder('');
        url.addArg('pageaction', this.ajaxpage);
        url.addArg('country', this.country);
        url.addArg('station', this.station_id);
        url.addArg('wvar', this.wvar);
        url.addArg('wtype', this.wtype);
        url.addArg('pct', pct);
        url.addArg('start', this.startdate);
        url.addArg('end', this.enddate);
        weriseApp.ajax(url.getUrl()).done(function (data) {
            if (data.chart == false)
            {
                hideLoaderChart(this.item_chart);
                weriseApp.showError(_t('No weather data available.'));
                return;
            }
            // save to global
            window.ajaxdataw = data;            
            // weather advisory
            that.showWeatherAdvisory(data.chart.series[0].data);
            // weather chart
            that.callHighCharts(that.item_chart, data.chart);
        });
    },
    /**
     * 
     * @param {type} ajaxdata
     * @param CropCaledarChart cropcaldata
     * @returns {undefined}
     */
    callHighCharts: function (item_chart, ajaxdata)
    {
        // crop calendar 1
        if (this.cropcalendar1 !== false)
        {
            // set line to maximum
            for (var j = 0; j < this.cropcalendar1.data.length; j++)
            {
                this.cropcalendar1.data[j].y = ajaxdata.max;
            }
            // insert cropcal to chart
            ajaxdata.series.push(this.cropcalendar1);
        }
        // crop calendar 2
        if (this.cropcalendar2 !== false)
        {
            // set line to maximum
            for (var j = 0; j < this.cropcalendar2.data.length; j++)
            {
                this.cropcalendar2.data[j].y = ajaxdata.max;
            }
            // insert cropcal to chart
            ajaxdata.series.push(this.cropcalendar2);
        }

        var chartdim = weriseApp.getChartDimensions();
        jQuery(item_chart).width(chartdim[0] - 30).height(chartdim[1]);

        // dataset 
        var wtype = this.wtype;

        // weather variable
        var wvar = 0;
        var wvar_name = _t('Rainfall');
        var wvar_props = getWeatherVarProps(wvar);

        jQuery(item_chart).highcharts({
            chart: {
                type: 'line',
                borderRadius: 5,
                backgroundColor: '#D5FFB5',
                borderWidth: 1,
                zoomType: 'x'
            },
            credits: {
                enabled: false
            },
            title: {
                text: getWeatherType(wtype) + ' ' + _t('Weather Data'),
                x: -20 //center
            },
            xAxis: {
                title: {
                    text: ajaxdata.chart_period
                },
                type: 'datetime',
                dateTimeLabelFormats: {// don't display the dummy year
                    month: '%b',
                    year: '%b'
                }
            },
            yAxis: {
                title: {
                    text: wvar_name + ' [' + wvar_props[0] + ']'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }],
                min: 0
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                borderWidth: 1,
                backgroundColor: '#ffffff'
            },
            tooltip: {
                crosshairs: true,
                formatter: function () {
                    return (weatherChartTooltip(this, wvar_props));
                }
            },
            series: ajaxdata.series
        });
    },
    showWeatherAdvisory: function(ajaxdata)
    {
            // weather advisory: crop calendar 1
            if (this.cropcalendar1 !== false)
            {
                var results1 = this.extractWeatherAdvisoryData(ajaxdata, this.cropcalendar1.data);
                jQuery('#wet-dates').html(results1[0]);
                jQuery('#dry-dates').html(results1[1]);
                jQuery('#rain-onset').html(results1[2]);
            }
            // weather advisory: crop calendar 2
            if (this.cropcalendar2 !== false)
            {
                var results2 = this.extractWeatherAdvisoryData(ajaxdata, this.cropcalendar2.data);
                jQuery('#wet-dates2').html(results2[0]);
                jQuery('#dry-dates2').html(results2[1]);
                jQuery('#rain-onset2').html(results2[2]);
            }             
    },
    /**
     * 
     * @param Object seriesdata
     * @param CropCalendarChart cropcaldata
     * @returns {Array|doWeatherAdvisory2.results}
     */
    extractWeatherAdvisoryData : function (seriesdata, cropcaldata) {
        var results = [];
        // get sowdate harvest date
        var sowdate = 0;
        var harvestdate = 0;
        for (var i = 0; i < cropcaldata.length; i++)
        {
            if (i === 0)
            {
                sowdate = cropcaldata[i].x;
            }
            harvestdate = cropcaldata[i].x;
        }

        // flood dates
        var wetdates = window.ajaxdataw.advisory.wet_dates;
        var adv_wetdates = WeatherAdvisory.getWetDates(sowdate, harvestdate, wetdates);
        results.push(adv_wetdates);

        // drought dates
        var drydates = window.ajaxdataw.advisory.dry_dates;
        var adv_drydates = WeatherAdvisory.getDryDates(sowdate, harvestdate, drydates);
        results.push(adv_drydates);

        // onset rain
        var rain_onset = WeatherAdvisory.getRainOnset(sowdate, seriesdata);
        results.push(rain_onset);
        return results;
    }
};


function weatherChartTooltip(chart_data, wvar_props)
{
    var yval = '', xdate = Highcharts.dateFormat('%e.%b.%y', chart_data.x);
    if (chart_data.series.name.search('Crop') >= 0)
    {
        return '<b>' + chart_data.point.name + '</b><br/>' + xdate.toLowerCase();
    }
    if (typeof chart_data.point.low === 'undefined') {
        yval = chart_data.y + ' ' + wvar_props[0];
    } else
    {
        yval = chart_data.point.low + ' ' + wvar_props[0] + ' - ' + chart_data.point.high + ' ' + wvar_props[0];
    }
    return '<b>' + chart_data.series.name + '</b><br/>' + xdate.toLowerCase() + ': ' + yval;
}

