var WeatherForm = {
    /**
     * form items
     * @type String
     */
    item_country: "#country",
    item_station: "#station",
    item_tyear: "#type_year",
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
    getYear: function () {
        return this._get(this.item_tyear);
    },
    /**
     * initialize form
     */
    initForm: function () {

        this.renderStation(this.getCountry());

        // country change
        jQuery(this.item_country).change(function () {
            WeatherForm.countryChange();
        });

        // station change
        jQuery(this.item_station).change(function () {
            WeatherForm.stationChange();
        });
        
        // year change
        jQuery(this.item_tyear).change(function () {
            var typeyear = jQuery(this).val();
            jQuery("#type-year-pre").html(getWeatherType(typeyear.substr(0,1)));
        });        

        // show station select
        jQuery('#location_btn').click(function () {
            jQuery('#location_div').hide();
            jQuery(WeatherForm.item_station).show();
        });

        // form submission
        jQuery('#frm').submit(function () {
            if (WeatherForm.validateForm())
            {
                WeatherForm.submitForm();
            }
            return false;
        });

         // acknowledgement button click
        jQuery('#ack_btn').click(function () {
            // show acknowledgement data
            jQuery('#ack_container').show();
            this.hide();
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
        jQuery('#station').hide();

        // populate year
        DropDown.makeYear(this.getCountry(), station_id, 'w', [this.item_tyear]);
    },
    /**
     * submit form
     */
    submitForm: function () {
        jQuery('.afterload').show();        
        var chart = new WeatherChart();
        chart.showChart();
    },
    validateForm: function () {
        var country = this.getCountry();
        if (country === '')
        {
            weriseApp.showError(_t('Please select Country.'));
            return false;
        }
        var station_id = this.getStation();       
        if (station_id <= 0)
        {
            weriseApp.showError(_t('Please select Location.'));
            return false;
        }
        var type_year = this.getYear();
        var year = parseInt(type_year.substr(1, 4));
        var wtype = type_year.substr(0, 1);        
        if (year <= 0 || (wtype !== 'r' && wtype !== 'f'))
        {
            weriseApp.showError(_t('Please select Year.'));
            return false;
        }
        return true;
    },
    /**
     *
     * @param {type} country_code
     * @returns {undefined}
     */
    renderStation: function (country_code) {
        // render station dropdown
        jQuery("#location_name").val('');
        DropDown.makeStation(country_code, this.item_station, 'w');
        // draw google maps
        draw_google_maps(country_code, 'w');
    }
};

/**
 * on page load
 */
jQuery(function () {
    WeatherForm.initForm();
});
