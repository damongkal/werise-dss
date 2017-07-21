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
    setFertSchedule: function(sowdate,fertsched)
    {
        this.fertsched = fertsched;
        var parts = this.fertsched.split(',');
        var d1 = parseInt(parts[0]);
        if (d1 !== 0)
        {
            this.fert_basal = this.getFertDate(sowdate, parts[0], parts[1]);
            this.fert_basal_amt = parts[1];
            this.fert_topdress1 = this.getFertDate(sowdate, parts[2], parts[3]);
            this.fert_topdress1_amt = parts[3];
            this.fert_topdress2 = this.getFertDate(sowdate, parts[4], parts[5]);
            this.fert_topdress2_amt = parts[5];
        }        
    },
    getFertDate: function (fert_interval,fert_amt) {
        var tmp_date = this.sow_date + (86400000 * parseInt(fert_interval));
        if (parseInt(fert_amt)>0)
        {
            return tmp_date;
        }        
        return 0;
    },
    getFertPeriod: function(fert_date) {
        if (fert_date===0)
        {
            return '';
        }
        var interval = 4 * 86400000;
        return formatDate2(fert_date-interval,'m-d','abbr') + ' to ' + formatDate2(fert_date+interval,'m-d','abbr');
    }    
};