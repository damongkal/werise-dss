function CdfdmChart()
{
    /**
     *
     * @param {type} seriesdata
     * @returns {undefined}
     */
    this.callHighCharts = function (charttitle,seriesdata) {
        
        if(seriesdata===false) {
            return;
        }
        var item_chart = '#chart_pr';       

        jQuery(item_chart).highcharts({
            chart: {
                type: 'line',
                borderRadius: 10,
                backgroundColor: '#D5FFB5',
                borderWidth: 1,
                zoomType: 'x'
            },
            credits: {
                enabled: false
            },
            title: {
                text: charttitle
            },
            xAxis: {
                title: {
                    text: 'Date'
                },
                type: 'datetime'
            },
            yAxis: {
                min: 0
            },            
            legend: {
                layout: 'vertical',
                align: 'center',
                verticalAlign: 'bottom',
                borderWidth: 1,
                backgroundColor: '#ffffff'
            },
            series: seriesdata
        });
        jQuery(item_chart).show();
    };
}
