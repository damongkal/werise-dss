/**
 * page behaviours
 */
jQuery(function () {
    // trigger one crop calendar
    jQuery('#csone').click(function () {
        OryzaFormV1.initForm();        
        jQuery('#cropseason').hide();
        jQuery('#dataselection').show();
        jQuery('#dataselection2').hide();
    });

    // trigger two crop calendar
    jQuery('#cstwo').click(function () {
        CombiListForm.initForm();        
        jQuery('#cropseason').hide();
        jQuery('#dataselection2').show();
        jQuery('#dataselection').hide();
        jQuery('.cs2_custom').hide();
        jQuery('#grainyield_chart').hide();
    });
});

var OryzaFormV1 = {
    item_country: '#country',
    item_station: '#station',
    item_tyear: '#type_year',
    item_variety : "#variety",
    item_fertil : "#fertil",
    comparesets : ['','2','3','4'],
    /**
     * getters
     * @param {type} item_id
     * @returns {jQuery}
     */
    _get: function (item_id) {
        return jQuery(item_id).val();
    },
    getCountry: function () {
        return this._get(this.item_country);
    },
    getStation: function () {
        return parseInt(this._get(this.item_station));
    },
    getYear: function (setno) {
        return this._get(this.item_tyear+setno);
    },
    getVariety: function (setno) {
        return this._get(this.item_variety+setno);
    },
    getFertil: function (setno) {
        return this._get(this.item_fertil+setno);
    },    
    initForm: function ()
    {
        this.renderStation(this.getCountry());

        // initialize all when country change
        jQuery(this.item_country).change(function () {
            OryzaFormV1.countryChange();
        });

        // change year when station change
        jQuery(this.item_station).change(function () {
            OryzaFormV1.stationChange();
        });

        // year change
        for(i=0; i<this.comparesets.length; i++) {            
            jQuery(this.item_tyear+this.comparesets[i]).change(function () {
                OryzaFormV1.yearChange(this);
            });
        };

        // show new set when button click
        jQuery('#compare').click(function () {
            OryzaFormV1.addDataSets();
        });

        // location change
        jQuery('#location_btn').click(function () {
            jQuery('#location_div').hide();
            jQuery(OryzaFormV1.item_station).show();
        });

        // form submission
        jQuery('#frm').submit(function () {           
            if (OryzaFormV1.validateForm())
            {
                OryzaFormV1.submitForm();
            }
            return false;            
        });
    },
    /**
     * value change
     */
    countryChange: function () {            
        var country_code = this.getCountry();
        if (country_code=='')
        {
            jQuery(this.item_country).val('ID');
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
        jQuery("#location_name").val(DropDown.getStationName(station_id));

        // show location placeholder
        jQuery('#location_div').show();
        jQuery(this.item_station).hide();

        // populate year
        DropDown.makeYear(this.getCountry(), station_id, 'o', [this.item_tyear]);            
    },    
    yearChange: function (item_id) {            
        var tyear = jQuery(item_id).val();
        weriseApp.dbg('change', 'year', tyear);
        var item_id2 = '#'+jQuery(item_id).attr('id');
        var setno = item_id2.replace(this.item_tyear, "");
        DropDown.makeVariety(this.getCountry(), this.getStation(), tyear, this.item_variety + setno);        
    },
    validateForm: function () {
        var country = this.getCountry();
        if (country === '')
        {
            showErrorChart(_t('Please select Country.'));
            return false;
        }
        var station_id = this.getStation();        
        if (station_id <= 0)
        {
            showErrorChart(_t('Please select Station.'));
            return false;
        }        
        for (var i = 0 ; i < this.comparesets.length; i++) {
            var ret = this.validateSet(i);
            if (ret===false)
            {
                return false;
            }
        }
        return true;
    },
    validateSet: function (setno)
    {
        var fertil = this.getFertil(setno);        
        if (fertil === '2')
        {
            showErrorChart('Site Specific Fertilizer Recommendation is not yet available. Please visit <a href="http://webapps.irri.org/nm/id" target="_blank">Nutrient Manager for Rice</a> for more information.');
            return false;
        }
                
        var set_number = 1;
        if (setno!=='')
        {
            set_number = parseInt(setno);
        }

        // only set 1 year is required
        var type_year = this.getYear(setno);
        if (setno === '' && type_year === '')
        {
            showErrorChart(_t('Please select Year') + ' ' + _t('for Set') + ' ' + set_number);
            return false;
        }

        // check variety if year is supplied
        var variety = this.getVariety(setno);
        if (type_year !== '' && variety === '')
        {
            showErrorChart(_t('Please select Variety') + ' ' + _t('for Set') + ' ' + set_number);
            return false;
        }
        
        // check fertil if year is supplied
        if (type_year !== '' && fertil === '')
        {
            showErrorChart(_t('Please select Fertilization') + ' ' + _t('for Set') + ' ' + set_number);
            return false;
        }

        return true;
    },
    /**
     * submit form
     */
    submitForm: function () {
        var chart = new OryzaChart();        
        chart.showChart();
    },    
    /**
     * displays more sets for comparisons
     * @returns void
     */
    addDataSets: function ()
    {
        var country_code = this.getCountry();
        var station_id = this.getStation();
        var tyear = this.getYear('');
        for (var i = 0; i < this.comparesets.length; i++) {
            var setno = this.comparesets[i];            
            var item = jQuery('#set' + setno);
            if (item.css('display') === 'none')
            {
                DropDown.makeYear(country_code, station_id, 'o', [this.item_tyear + setno]);
                DropDown.makeVariety(country_code, station_id, tyear, this.item_variety + setno);
                item.show();
                return;
            }
        };
    },    
    /**
     *
     * @param {type} country_code
     * @returns {undefined}
     */
    renderStation: function (country_code) {
        // render station dropdown
        jQuery("#location_name").val('');
        DropDown.makeStation(country_code, OryzaFormV1.item_station, 'o');
        // draw google maps
        draw_google_maps(country_code, 'o');
    }    
}