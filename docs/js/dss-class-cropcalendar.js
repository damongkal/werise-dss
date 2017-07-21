function CropCalendar() {
    this.dataset_id = 0;
    this.runnum = 0;
    this.sow_date = 0;
    this.panicle_init_date = 0;
    this.flower_date = 0;
    this.harvest_date = 0;
    this.fertsched = '';
    this.fert_basal = 0;
    this.fert_topdress1 = 0;
    this.fert_topdress2 = 0;
    this.yield = 0;
};

CropCalendar.prototype = {
    constructor: CropCalendar,
    setCalendar: function(rawdata) {        
        if (rawdata.sow_date === undefined)
        {
            this.dataset_id = rawdata.dataset_id;
            this.runnum = rawdata.runnum;
            this.sow_date = rawdata[0];
            this.panicle_init_date = rawdata.cropdate_panicle_init;
            this.flower_date = rawdata.cropdate_flowering;
            this.harvest_date = rawdata.cropdate_harvest;
            this.fertsched = rawdata.fertsched;
            this.yield = rawdata[1];
        } else
        {
            this.setCalendar2(rawdata);
        }
        var fert = new FertilizerSchedule;
        fert.setSchedule(this.sow_date,this.fertsched);
        this.setFertSchedule(fert);
    },
    setCalendar2: function(rawdata) {
        this.dataset_id = rawdata.dataset_id;
        this.runnum = rawdata.runnum;        
        this.sow_date = rawdata.sow_date;
        this.panicle_init_date = rawdata.panicleinit_date;
        this.flower_date = rawdata.flower_date;
        this.harvest_date = rawdata.harvest_date;
        this.fertsched = rawdata.fert;
        this.yield = rawdata.yield;
    },    
    setFertSchedule: function(FertilizerSchedule)
    {
        this.fert_basal = FertilizerSchedule.fert_basal;            
        this.fert_topdress1 = FertilizerSchedule.fert_topdress1;
        this.fert_topdress2 = FertilizerSchedule.fert_topdress2;        
    }
};

var CropCalendarChart = {
    /**
     * compile chart properties of crop calendar
     * @param CropCalendar cropcalendar
     * @returns {undefined}
     */
    getProps : function (cropcalendar) {
        // compile the dates
        var cropdates = [];
        cropdates.push({date : cropcalendar.sow_date, label : 'Sowing Date', marker : 'sow'});    
        cropdates.push({date : cropcalendar.panicle_init_date, label : 'Panicle Init.', marker : 'panicleinit'});
        cropdates.push({date : cropcalendar.flower_date, label : 'Flowering', marker : 'flower'});
        cropdates.push({date : cropcalendar.harvest_date, label : 'Harvest Date', marker : 'harvest'});
        if (cropcalendar.fert_basal>0)
        {
            cropdates.push({date : cropcalendar.fert_basal, label : 'Basal', marker : 'fert-basal'});
        }
        if (cropcalendar.fert_topdress1>0)
        {
            cropdates.push({date : cropcalendar.fert_topdress1, label : 'Top Dress 1', marker : 'fert-top1'});
        }
        if (cropcalendar.fert_topdress2>0)
        {
            cropdates.push({date : cropcalendar.fert_topdress2, label : 'Top Dress 2', marker : 'fert-top2'});
        }        
        cropdates.sort(function(a,b){return a.date - b.date;});
        
        // convert to chart readable format
        var chart_props = new Array();    
        for (var j=0; j<cropdates.length; j++)
        {
            chart_props.push({ x : cropdates[j].date, y:0 , name: _t(cropdates[j].label) , marker:{symbol:'url(images/cropcal-'+cropdates[j].marker+'.png)'}});
        }     
        return chart_props;
    }
};

function FertilizerSchedule() {
    this.fertsched = '';
    this.fert_basal = 0;
    this.fert_topdress1 = 0;
    this.fert_topdress2 = 0;
    this.fert_basal_amt = 0;
    this.fert_topdress1_amt = 0;
    this.fert_topdress2_amt = 0;    
};

FertilizerSchedule.prototype = {
    constructor: FertilizerSchedule,
    setSchedule: function(sowdate,fertsched)
    {
        this.fertsched = fertsched;
        var parts = this.fertsched.split(',');
        var d1 = parseInt(parts[0]);
        if (d1 !== 0)
        {
            this.fert_basal = this.getApplyDate(sowdate, parts[0], parts[1]);
            this.fert_basal_amt = parts[1];
            this.fert_topdress1 = this.getApplyDate(sowdate, parts[2], parts[3]);
            this.fert_topdress1_amt = parts[3];
            this.fert_topdress2 = this.getApplyDate(sowdate, parts[4], parts[5]);
            this.fert_topdress2_amt = parts[5];
        }        
    },
    getApplyDate: function (sowdate,fert_interval,fert_amt) {
        var tmp_date = sowdate + (86400000 * parseInt(fert_interval));
        if (parseInt(fert_amt)>0)
        {
            return tmp_date;
        }        
        return 0;
    },
    getApplyPeriod: function(fert_date) {
        if (fert_date===0)
        {
            return '';
        }
        var interval = 4 * 86400000;
        return formatDate2(fert_date - interval,'m-d','abbr') + ' to ' + formatDate2(fert_date+interval,'m-d','abbr');
    }    
};