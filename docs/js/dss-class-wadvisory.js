var WeatherAdvisory = {
    getRainCategory : function(rain_code,rain_amt) {
        return rain_code + ' (' + rain_amt + ' mm)';
    },
    getRainOnset: function(sowdate, raindata) {    
        var onset_rain = '';
        var rain_accu = 0;
        for (var i=0; i<raindata.length; i++)
        {
            if (raindata[i][0]>=sowdate)
            {            
                rain_accu = rain_accu + raindata[i][1];
                if (rain_accu>=30 && onset_rain==='')
                {
                    onset_rain = formatDate2(raindata[i][0], 'm-d', 'abbr');
                }
            }
        }
        return onset_rain;
    },
    getDryDates: function (sowdate,harvestdate,drydates) {
        accu = [];
        for (var i = 0; i < drydates.length; i++)
        {
            if (drydates[i] >= sowdate && drydates[i] <= harvestdate)
            {
                accu.push(formatDate2(drydates[i], 'm-d', 'abbr'));
            }
        }
        return accu.join(' , ');    
    },
    getWetDates: function(sowdate, harvestdate, wetdates) {
        var accu = [];
        for (var i = 0; i < wetdates.length; i++)
        {
            if (wetdates[i] >= sowdate && wetdates[i] <= harvestdate)
            {
                accu.push(formatDate2(wetdates[i], 'm-d', 'abbr'));
            }
        }
        return accu.join(' , ');
    }    
};
