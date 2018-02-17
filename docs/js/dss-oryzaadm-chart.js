function OryzaChart()
{
    /**
     *
     * @param {type} item_chart
     * @param {type} chart_data
     * @returns {undefined}
     */
    this.callHighCharts = function (item_chart, chart_data, chart_subtitle)
    {        
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
                text: chart_subtitle
            },            
            legend: {
                enabled: false
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
                min: 0
            },
            legend: {
                layout: 'vertical',
                align: 'center',
                verticalAlign: 'bottom',
                borderWidth: 1,
                backgroundColor: '#ffffff'
            },            
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' t/ha';
                }
            },
            series: chart_data
        });
    };
}