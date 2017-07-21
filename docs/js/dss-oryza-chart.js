function OryzaChart()
{
    /**
     *
     * @returns {undefined}
     */
    this.showChart = function () {
        hideErrorChart();
        jQuery('#homeimages').hide();
        getData('#chart1');
    };
    /**
     *
     * @param {type} item_chart
     * @param {type} wvar
     * @returns {undefined}
     */
    function getData(item_chart)
    {
        jQuery(item_chart).show();

        // build URL
        var url = new UrlBuilder('');
        url.addArg('pageaction', 'oryza');
        url.addArg('country', OryzaFormV1.getCountry());
        url.addArg('station', OryzaFormV1.getStation());
        for (var i = 0; i < OryzaFormV1.comparesets.length; i++) {
            var setno = OryzaFormV1.comparesets[i];
            var type_year = OryzaFormV1.getYear(setno);
            if (type_year!='') {
                url.addArg('year'+setno, parseInt(type_year.substr(1, 4)));
                url.addArg('wtype'+setno, type_year.substr(0, 1));
                url.addArg('variety'+setno, OryzaFormV1.getVariety(setno));
                url.addArg('fert'+setno, OryzaFormV1.getFertil(setno));
            }
        }
        weriseApp.ajax(url.getUrl()).done(function (data) {
            processData(item_chart,data);
        });
    };
    
    function processData(item_chart, ajaxdata)
    {
        if (ajaxdata.chart == false)
        {
            return;
        }
        // populate weather options for calendar
        DropDown.makeWvar(OryzaFormV1.getCountry(), OryzaFormV1.getStation(), OryzaFormV1.getYear(''), "#wvar");
        // save to global
        HighYieldStorage.save(ajaxdata);
        // build advisory first before chart
        OryzaAdvisory.showAdvisory(ajaxdata.advisory);
        jQuery('#opt-sow-adv').show();
        // crop chart
        var station_name = jQuery('#location_name').val();
        var chart = new OryzaChart();
        chart.callHighCharts(item_chart, ajaxdata.chart, station_name);
        if (jQuery('#type_year2').val() === '')
        {
            jQuery('#open-chart2').show();
        }
    }
    /**
     *
     * @param {type} item_chart
     * @param {type} wvar
     * @param {type} ajaxdata
     * @returns {undefined}
     */
    this.callHighCharts = function (item_chart, ajaxdata, station_name)
    {
        if (ajaxdata == false)
        {
            hideLoaderChart(item_chart);
            showErrorChart(_t('No data available.'));
            return;
        }
        
        // resize viewport
        var scrw = screen.width;
        if (scrw>1300)
        {
            scrw = 960;
        }
        jQuery(item_chart)
            .width(scrw - 0)
            .height('400px');

        jQuery(item_chart).highcharts({
            chart: {
                type: 'spline',
                borderRadius: 10,
                backgroundColor: '#D5FFB5',
                borderWidth: 1
            },
            credits: {
                enabled: false
            },
            title: {
                text: _t('Simulated Attainable Grain Yield')
            },
            subtitle: {
                text: _t('Station')+' : ' + station_name
            },
            legend: {
                layout: 'vertical',
                align: 'center',
                verticalAlign: 'bottom',
                borderWidth: 1,
                backgroundColor: '#ffffff'
            },
            xAxis: {
                title: {
                    text: _t('Sowing Date')
                },
                type: 'datetime',
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%b',
                    year: '%b',
                    day: '%b'
                }
            },
            yAxis: {
                title: {
                    text: _t('Grain yield')+' (t/ha)'
                },
                tickInterval: 1,
                min: 0,
                max: ajaxdata.maxyld
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' t/ha';
                }
            },
            series: ajaxdata.series
        });

        if (ajaxdata.wetseason!==undefined)
        {
            var chart = jQuery(item_chart).highcharts();
            chart.xAxis[0].addPlotBand({
                from: ajaxdata.wetseason.from,
                to: ajaxdata.wetseason.to,
                color: 'rgba(0, 29, 185, 0.3)',
                label: {
                    text: _t('Wet Season'),
                    style: {
                        color: '#000069',
                        fontSize: '18px' },
                    y: 40 }
            });
        }
    };
}


var HighYieldStorage = {
    ajaxdata: [],
    save: function(data)
    {
        this.ajaxdata = data;
    },
    getRecordBySowDate: function(sowdate)
    {
        var hi_yld = this.ajaxdata.advisory.hi_yld;
        var rec_sowdate = 0;
        for (var j=0; j<hi_yld.length; j++)
        {
            rec_sowdate = hi_yld[j][0];
            if (rec_sowdate === sowdate)
            {
                return hi_yld[j];
            }
        }
        return false;
    }
};