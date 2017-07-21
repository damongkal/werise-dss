function showDatagrid(ajaxdata)
{
    var grid_titles = new Array();
    grid_titles[0] = 'Date';
    grid_titles[1] = 'Set 1';
    if (jQuery('#type_year2').val()!=='')
    {
        grid_titles[2] = 'Set 2';
    }
    if (jQuery('#type_year3').val()!=='')
    {
        grid_titles[3] = 'Set 3';
    }
    if (jQuery('#type_year4').val()!=='')
    {
        grid_titles[4] = 'Set 4';
    }

    // tabular data
    jQuery('#datagrid1').TidyTable(
    {
        columnTitles : grid_titles,
        columnValues : ajaxdata
    });
}

function showRainSched(fert_apply)
{
    // convert json
    var tmp = [];
    var tmp2 = [];
    var cdate1 = '';
    var cdate2 = '';
    for (var i=0; i<fert_apply.length; i++)
    {
        cdate1 = formatDate2(fert_apply[i].from[0],'','abbr');
        cdate2 = formatDate2(fert_apply[i].to[0],'','abbr');
        tmp2 = [cdate1,fert_apply[i].from[1],cdate2,fert_apply[i].to[1]];
        tmp.push(tmp2);
    }    
    jQuery('#raw_rain').TidyTable(
    {
        columnTitles : ['From', 'RainFall', 'To', 'Rainfall'],
        columnValues : tmp
    });    
}