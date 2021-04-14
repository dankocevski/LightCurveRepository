<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-us">
<head>
    <title>Fermi LAT Light Curve Repository</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="icon" type="image/png" href="./img/favicon2.png">

    <!-- jQuery -->
    <script type="text/javascript" src="./js/jquery-1.12.0.min.js"></script>
    <!-- <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> -->
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> -->

    <!-- Chart.js -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

    <!-- Reset css -->
    <link rel="stylesheet" hrmouseef="./css/reset.css" type="text/css" />

    <!-- NASA theme -->
    <link rel="stylesheet" href="./css/NASA.css">

    <!-- Bootstrap compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Bootstrap Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Bootstrap compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <!-- Highcharts CDN -->
    <script src="https://code.highcharts.com/highcharts.src.js"></script>
    <!-- <script src="https://code.highcharts.com/stock/highstock.js"></script> -->
    <script src="https://code.highcharts.com/stock/modules/data.js"></script>
    <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/stock/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Load test data -->
    <!-- <script type="text/javascript" src="./data/photon_flux_error.json"></script> -->



</head>

<!-- custom css -->
<style type="text/css">

    #footer { 
        float:left;
        padding:25px 0 10px 10px;
        width:99%}
    }

    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }

    /*  .modal-open .container-fluid, .modal-open  .container {
            -webkit-filter: blur(5px) grayscale(90%);
        }   
    */
    .modal-backdrop {
       /*background-color: red;*/
       -webkit-filter: blur(5px) grayscale(90%);
    }

    body.modal-open .background-container{
        -webkit-filter: blur(4px);
        -moz-filter: blur(4px);
        -o-filter: blur(4px);
        -ms-filter: blur(4px);
        filter: blur(4px);
        filter: url("https://gist.githubusercontent.com/amitabhaghosh197/b7865b409e835b5a43b5/raw/1a255b551091924971e7dee8935fd38a7fdf7311/blur".svg#blur);
        filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius='4');
    }

    .plot_container{
        background: url('./img/loading_apple.gif') no-repeat;
        background-repeat: no-repeat; 
        background-position:center; 
        background-size:30px; 
        min-height: 450px;
    }

    .hidden {
       position: absolute;
       top: -9999em;
       width:100%;
       height:400px;
       padding-right:10px;
    }

    .highcharts-data-table table {
       top: -9999em;
       left: -9999em;
    }

    .tooltip{
        opacity: 1;

    }

    .tooltip-inner {
        color: white;
        background: #00468b;
        opacity: 1;

    }

    .tooltip.top .tooltip-arrow{
        bottom:0;
        left:50%;
        margin-left:-5px;
        border-left:5px solid transparent;
        border-right:5px solid transparent;
        border-top:5px solid #00468b
    }


</style>


<body id="body-plain">

    <script type="text/javascript" src="./js/dat.gui.js"></script>
    <script type="text/javascript">
        
        // Define the color palette
        var palette = ['#173F5F', '#20639B', '#3CAEA3', '#F6D55C', '#ED553B']

        var classifications;
        var occurrences;
        var colors;
        var catalog;
        var magic_word_submitted;
        var passphrase;
        
        var chart1;
        var chart2;

        var data;
        var flux;
        var flux_error;
        var flux_upper_limits;
        var photon_index;
        var photon_index_error;
        var ancillary_data;
        var responseText;

        // Setting global light curve defaults 
        var cadence = 'weekly'
        var flux_type = 'photon'
        var index_type = 'fixed'
        var ts_min = '4'
        var ancillary_type = 'photon_index'
        // var ancillary_type = 'fit_convergance'
        var ancillary_data_label = 'Photon Index'

        // var chartType = 'linear';
        var chartType = 'datetime';
        var xtitle = 'Date (UTC)'

        var counts = {};
        var fit_tolerance;

        var mets = new Array;

        var flux_type_label
        var flux_type

        var URL_light_curve_data;

        // Default plot options
        var show_upper_limits = true;
        var show_error_bars = true;
        var tooltip_toggle = true;
        var upper_limits_toggle = true
        var data_line_width = 0.5
        var defaultFormatter;
        var show_nonconvergant_fits = false;
        var show_unconstrained_fits = false;
        var show_tooltip = true;

		var flux_forPlotting
		var flux_error_forPlotting
		var flux_upper_limits_forPlotting
		var photon_index_forPlotting
		var photon_index_error_forPlotting

        // Extending the addClass jquery function to accept a callback function
        ;(function ($) {
            var oAddClass = $.fn.addClass;
            $.fn.addClass = function () {
                for (var i in arguments) {
                    var arg = arguments[i];
                    if ( !! (arg && arg.constructor && arg.call && arg.apply)) {
                        setTimeout(arg.bind(this));
                        delete arguments[i];
                    }
                }
                return oAddClass.apply(this, arguments);
            }

        })(jQuery);

        // A function to set cookie data
        function setCookieData(name, value, expiration_days) {
            var date = new Date();
            date.setTime(date.getTime() + (expiration_days*24*60*60*1000));
            var expires = "expires="+ date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        // Get the cookie data. Return the value if found, return empty string if no cookie is found
        function getCookieData(name) {
            var name_mod = name + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name_mod) == 0) {
                    return c.substring(name_mod.length, c.length);
                }
            }
            return null;
        }

        // Quick and simple export target #table_id into a csv
        function download_table_as_csv(table_id, separator = ',') {
            // Select rows from table_id
            var rows = document.querySelectorAll('table#' + table_id + ' tr');
            // Construct csv
            var csv = [];
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll('td, th');
                for (var j = 0; j < cols.length; j++) {
                    // Clean innertext to remove multiple spaces and jumpline (break csv)
                    var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ')
                    // Escape double-quote with double-double-quote (see https://stackoverflow.com/questions/17808511/properly-escape-a-double-quote-in-csv)
                    data = data.replace(/"/g, '""');
                    // Push escaped string
                    row.push('"' + data + '"');
                }
                csv.push(row.join(separator));
            }
            var csv_string = csv.join('\n');
            // Download it
            var filename = GetUrlValue('source_name') + '_' + cadence + '_' + new Date().toLocaleDateString() + '.csv';
            var link = document.createElement('a');
            link.style.display = 'none';
            link.setAttribute('target', '_blank');
            link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv_string));
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function MET2date(MET) {

            // Account for leap seconds
            if (MET>157766400) {MET=MET-1}  // 2005 leap second
            if (MET>252460801) {MET=MET-1}  // 2008 leap second
            if (MET>362793601) {MET=MET-1}  // 2012 leap second
            if (MET>457401601) {MET=MET-1}  // 2015 leap second
            if (MET>504921601) {MET=MET-1}  // 2016 leap second

            // Convert seconds to milliseconds
            var MET = MET * 1000

            // Define the date @ MET = 0
            // var date0 = new Date("January 01, 2001 00:00:00");
            var date0 = new Date('January 1, 2001 00:00:00 UTC');

            // Get the new date at the specified MET
            var date = new Date(+date0 + MET)

            return date
        }

        function MET2JD(MET) {

            // Get the date associated with the MET
            date = MET2date(MET)

            // Get the date components
            // var day = date.getDate() - 1;    // Subtracting a day to make results match NASA's xtime results
            var day = date.getUTCDate() ;   // Subtracting a day to make results match NASA's xtime results
            var year = date.getUTCFullYear();
            var month = date.getUTCMonth() + 1; // Adding a month because the javascript date object starts with month 0?

            // Get the julian date from the date components
            var julianDate = Math.floor((1461 * (year + 4800 + (month - 14) / 12)) / 4 + (367 * (month - 2 - 12 * ((month - 14) / 12))) / 12 - (3 * ((year + 4900 + (month - 14) / 12) / 100)) / 4 + day - 32075);
            // var julianDate = ((1461 * (year + 4800 + (month - 14) / 12)) / 4 + (367 * (month - 2 - 12 * ((month - 14) / 12))) / 12 - (3 * ((year + 4900 + (month - 14) / 12) / 100)) / 4 + day - 32075);

            return julianDate
        }

        function MET2MJD(MET) {

            // Get the julian date
            julianDate = MET2JD(MET)

            // Calculate the difference between the julian date and the modified julian date
            modifiedJulianDate = julianDate - 2400000.5

            return modifiedJulianDate
        }

        function countUnique(iterable) {

            return new Set(iterable).size;
        }

        function getURLVariables() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        }

        function GetUrlValue(VarSearch){
            var SearchString = window.location.search.substring(1);
            var VariableArray = SearchString.split('&');
            for(var i = 0; i < VariableArray.length; i++){
                var KeyValuePair = VariableArray[i].split('=');
                if(KeyValuePair[0] == VarSearch){
                    return KeyValuePair[1];
                }
            }
        }

        function median(values) {

            values.sort( function(a,b) {return a - b;} );

            var half = Math.floor(values.length/2);

            if (values.length % 2) {
                return values[half];
            } else {
                return (values[half-1] + values[half]) / 2.0;
            }
        }

        function median_from_matrix(data) {
            
            var values = new Array;
            for (var i = 0; i < data.length; i++) { values.push(data[i][1]) }

            return median(values)
        }              

        // function defaultFormatter() {

        //     var x_string
        //     if (xtitle.includes('Mission')) {
        //         x_string = 'MET = ' + this.x
        //     } else if (xtitle.includes('Modified Julian Date')) {
        //         x_string = 'MJD = ' + this.x
        //     } else if (xtitle.includes('Julian Date')) {
        //         x_string = 'JD = ' + this.x
        //     } else if (xtitle.includes('Date')) {
        //         x_string = 'Date = ' + Highcharts.dateFormat('%e %b %Y', this.x)
        //     }

        //     // Define the yaxis tooltip
        //     if (flux_type.includes('photon')) {
        //         var y_string = 'Photon Flux = ' + this.y.toExponential(2);
        //     } else {
        //         var y_string = 'Energy Flux = ' + this.y.toExponential(2);
        //     }

        //     // Combine the tooltip elements
        //     var tooltip_string =  x_string + '<br>' + y_string

        //     return tooltip_string

        // }

        function createScatterPlot() {

            Highcharts.setOptions({
                lang: {
                    thousandsSep: ""
                },              
                chart: {
                    style: {
                        fontFamily: 'Helvetica Neue"'
                    }
                }
            });

            chart1 = new Highcharts.chart('chart1', {

                tooltip: {
                    enabled: tooltip_toggle

                },

                chart: {
                    type: 'scatter',
                    zoomType: 'xy',
                    borderWidth: 0,
                    plotBorderWidth: 1.5,
                    plotBorderColor: "#000000"
                },

                title: {
                    enabled: false,
                    text: '4FGL J0924.0+2816',
                        style: {
                            color: "#333333",
                            fontSize: "14px",
                            display: 'none'
                        }
                },

                subtitle: {

                    text: null
                },

                xAxis: [{
                    type: chartType,
                    // dateTimeLabelFormats: { day: '%d %b %Y' },
                    title: {
                        enabled: true,
                        text: xtitle,
                        style: {
                            fontSize: "14px",
                            color: "#333333",

                        }
                    },
                    startOnTick: false,
                    endOnTick: false,
                    showLastLabel: true,
                    gridLineWidth: 0,
                    minorTicks: true,
                    minorGridLineWidth: 0,
                    minorTickWidth: 1,
                    minorTickPosition: 'inside',
                    tickLength: 4,
                    tickWidth: 1,
                    tickLength: 5,
                    tickColor: "#000000",
                    tickPosition: "inside",
                    lineColor: "#000000",
                    // tickInterval: 30240000*4,
                    labels: {
                        style: {
                            color: "#333333",
                            fontSize: "14px"
                        }
                    },
                    dateTimeLabelFormats: {  
                        day: '%e %b \'%Y', 
                        week: '%e %b \'%y',  
                        month: '%b %Y'
                    },
                    events: {
                        afterSetExtremes: function (event) {
                            var xMin = event.min;
                            var xMax = event.max;
                            
                            var ex = chart2.xAxis[0].getExtremes();

                            if (ex.min != xMin || ex.max != xMax) chart2.xAxis[0].setExtremes(xMin, xMax, true, false);
                        }
                    }
                },{
                    linkedTo: 0,
                    opposite: true,
                    type: chartType,
                    title: {
                        enabled: false,
                    },
                    tickWidth: 1,
                    tickLength: 5,
                    tickColor: "#000000",
                    tickPosition: "inside",
                    gridLineWidth: 0,
                    lineColor: "#000000",
                    labels: {
                        style: {
                            color: "#333333",
                            fontSize: "0px"
                        }
                    }
                }],

                yAxis: [{
                    title: {
                        useHTML: true,
                        text: 'Photon Flux ( "Photon Flux ( ph cm<sup>-2</sup> s<sup>-1</sup> )" )',
                        style: {
                            fontSize: "14px",
                            color: "#333333",
                        }
                    },
                    tickWidth: 1,
                    tickLength: 5,
                    tickColor: "#000000",
                    tickPosition: "inside",
                    gridLineWidth: 0,
                    min: 0,
                    max: median_from_matrix(flux)*10,
                    labels: {
                        formatter: function() {

                            // var strValue
                            // if (this.value == 0) { let strValue = '0'; console.log(strValue) } else { let strValue = this.value.toExponential(); console.log(strValue)  }
                            
                            let strValue = this.value.toExponential(2);
                            return strValue;
                        },
                        style: {
                            color: "#333333",
                            fontSize: "14px"
                        }
                    }
                    // gridLineWidth: 0
                },{
                    linkedTo: 0,
                    opposite: true,

                    title: {
                        enabled: false,
                    },
                    tickWidth: 1,
                    tickLength: 5,
                    tickColor: "#000000",
                    tickPosition: "inside",
                    gridLineWidth: 0,
                    labels: {
                        style: {
                            color: "#333333",
                            fontSize: "0px"
                        }
                    }
                }],

                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                    x: 100,
                    y: 70,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.chart.backgroundColor,
                    borderWidth: 1,
                    enabled: false
                },

                plotOptions: {
                    scatter: {
                        marker: {
                            radius: 3,
                            states: {
                                hover: {
                                    enabled: true,
                                    lineColor: 'rgb(100,100,100)'
                                }
                            }
                        },
                        states: {
                            hover: {
                                marker: {
                                    enabled: false
                                }
                            }
                        // }
                        },
                        tooltip: {
                            distance: 25,
                            // distance: 1000,
                            headerFormat: '<b>{series.name}</b><br>',
                            xDateFormat: '%Y-%m-%d',
                            // pointFormat: pointFormat_chart1,
                            // pointFormat: '',

                            pointFormatter: function() {

                                var x_string
                                if (xtitle.includes('Mission')) {
                                    x_string = 'MET = ' + this.x
                                } else if (xtitle.includes('Modified Julian Date')) {
                                    x_string = 'MJD = ' + this.x
                                } else if (xtitle.includes('Julian Date')) {
                                    x_string = 'JD = ' + this.x
                                } else if (xtitle.includes('Date')) {
                                    x_string = 'Date = ' + Highcharts.dateFormat('%e %b %Y', this.x)
                                }

                                // Define the yaxis tooltip
                                if (flux_type.includes('photon')) {
                                    var y_string = 'Photon Flux = ' + this.y.toExponential(2);
                                } else {
                                    var y_string = 'Energy Flux = ' + this.y.toExponential(2);
                                }

                                // Combine the tooltip elements
                                var tooltip_string =  x_string + '<br>' + y_string

                                return tooltip_string

                            }

                        }
                    }
                },

                series: [{
                    name: 'Detection',
                    // color: 'rgba(119, 152, 191, .5)',
                    color: 'rgba(57, 66, 100, .80)',
                    // data: data[0],
                    data: flux_forPlotting,
                    zIndex: 100,
                    lineWidth: data_line_width,
                    lineColor: "#9ba0b1",
                    animation: true,
                    // step: 'center',
                    states: {
                        hover: {
                            lineWidthPlus: 0.25
                        },
                        inactive: {
                            opacity: 1
                        }
                    }
                }, {
                    name: 'Upper Limit',
                    // color: 'rgba(119, 152, 191, .5)',
                    color: 'rgba(57, 66, 100, .80)',
                    data: flux_upper_limits,
                    zIndex: 99,
                    lineWidth: 0,
                    lineColor: "#9ba0b1",
                    animation: true,
                    visible: show_upper_limits,
                    marker: {
                        symbol: 'triangle-down'
                    },
                    states: {
                        hover: {
                            enable: false,
                            lineWidthPlus: 0.25
                        },
                        inactive: {
                            opacity: 1
                        }                           
                    }
                }, {
                    name: 'Error',
                    type: 'errorbar',
                    color: 'rgba(57, 66, 100, .40)',
                    data: flux_error,
                    zIndex: 0,
                    marker: {
                        radius: "square",
                        symbol: 0
                    },  
                    stickyTracking: true,   
                    animation: false,
                    visible: show_error_bars,
                    whiskerWidth: 0.5,
                    states: {
                        hover: {
                            enable: false,
                            lineWidthPlus: 0.25
                        },
                        inactive: {
                            opacity: 1
                        }   
                    },

                    tooltip: {
                        followPointer: false,
                        headerFormat: '<b>{series.name}</b><br>',
                        xDateFormat: '%Y-%m-%d',
                        // pointFormat: 'y-max: {point.high}<br>y-min: {point.low}'

                        pointFormatter: function() {

                            var tooltip_string =  'y-max: ' + this.high.toExponential(2) + '<br>y-min: ' + this.low.toExponential(2)
                            return tooltip_string

                        },

                    }
                }],

                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: ["downloadPNG", "downloadJPEG", "downloadPDF", "downloadSVG", 'downloadCSV','downloadXLS']
                        }
                    }
                }
            });

            chart2 = new Highcharts.chart('chart2', {
                chart: {
                    type: 'scatter',
                    zoomType: 'xy',
                    borderWidth: 0,
                    plotBorderWidth: 1.5,
                    plotBorderColor: "#000000",
                    animation: false
                },
                title: {
                    text: '4FGL J0924.0+2816',
                        style: {
                            color: "#333333",
                            fontSize: "14px",
                            display: "none"
                        }
                },
                subtitle: {
                    text: null
                },
                xAxis: [{
                    type: chartType,
                    title: {
                        enabled: true,
                        text: xtitle,
                        style: {
                            fontSize: "14px",
                            color: "#333333",

                        }
                    },
                    startOnTick: false,
                    endOnTick: false,
                    showLastLabel: true,
                    gridLineWidth: 0,
                    tickWidth: 1,
                    tickLength: 5,
                    tickColor: "#000000",
                    tickPosition: "inside",
                    lineColor: "#000000",
                    // tickInterval: 30240000,
                    labels: {
                        style: {
                            color: "#333333",
                            fontSize: "14px"
                        }
                    }, 
                    dateTimeLabelFormats: {  
                        day: '%e %b \'%Y', 
                        week: '%e %b \'%y',  
                        month: '%b %Y'
                    },
                    events: {
                        afterSetExtremes: function (event) {
                            var xMin = event.min;
                            var xMax = event.max;
                            
                            var ex = chart1.xAxis[0].getExtremes();

                            if (ex.min != xMin || ex.max != xMax) chart1.xAxis[0].setExtremes(xMin, xMax, true, false);
                        }
                    }
                },{
                    linkedTo: 0,
                    opposite: true,
                    type: chartType,
                    title: {
                        enabled: false,
                    },
                    tickWidth: 1,
                    tickLength: 5,
                    tickColor: "#000000",
                    tickPosition: "inside",
                    gridLineWidth: 0,
                    lineColor: "#000000",
                    labels: {
                        style: {
                            color: "#333333",
                            fontSize: "0px"
                        }
                    }
                }],

                yAxis: [{
                    title: {
                        text: ancillary_data_label,
                        style: {
                            fontSize: "14px",
                            color: "#333333",
                        }
                    },
                    tickWidth: 1,
                    tickLength: 5,
                    tickColor: "#000000",
                    tickPosition: "inside",
                    gridLineWidth: 0,
                    labels: {
                        formatter: function() {

                            // var strValue
                            // if (this.value == 0) { let strValue = '0'; console.log(strValue) } else { let strValue = this.value.toExponential(); console.log(strValue)  }
                            
                            // let strValue = this.value.toExponential();
                            let strValue = this.value;
                            return strValue;

                        },
                        style: {
                            color: "#333333",
                            fontSize: "14px"
                        }
                    }
                    // gridLineWidth: 0
                },{
                    linkedTo: 0,
                    opposite: true,
                    title: {
                        enabled: false,
                    },
                    tickWidth: 1,
                    tickLength: 5,
                    tickColor: "#000000",
                    tickPosition: "inside",
                    gridLineWidth: 0,
                    labels: {
                        style: {
                            color: "#333333",
                            fontSize: "0px"
                        }
                    }
                }],
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                    x: 100,
                    y: 70,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.chart.backgroundColor,
                    borderWidth: 1,
                    enabled: false
                },
                plotOptions: {
                    scatter: {
                        marker: {
                            radius: 3,
                            states: {
                                hover: {
                                    enabled: true,
                                    lineColor: 'rgb(100,100,100)'
                                }
                            }
                        },
                        states: {
                            hover: {
                                marker: {
                                    enabled: false
                                }
                            }
                        },

                        tooltip: {
                            distance: 25,
                            headerFormat: '<b>{series.name}</b><br>',
                            xDateFormat: '%Y-%m-%d',
                            // pointFormat: pointFormat_x1 + '<br>' + ancillary_data_label + ' = {point.y}'      

                            pointFormatter: function() {

                                var x_string
                                if (xtitle.includes('Mission')) {
                                    x_string = 'MET = ' + this.x
                                } else if (xtitle.includes('Modified Julian Date')) {
                                    x_string = 'MJD = ' + this.x
                                } else if (xtitle.includes('Julian Date')) {
                                    x_string = 'JD = ' + this.x
                                } else if (xtitle.includes('Date')) {
                                    x_string = 'Date = ' + Highcharts.dateFormat('%e %b %Y', this.x)
                                }

                                // Define the yaxis tooltip
                                if (flux_type.includes('photon')) {
                                    var y_string = 'Photon Flux = ' + this.y.toExponential(2);
                                } else {
                                    var y_string = 'Energy Flux = ' + this.y.toExponential(2);
                                }

                                // Combine the tooltip elements
                                var tooltip_string =  x_string + '<br>' + y_string

                                return tooltip_string

                            }
                        }
                    }
                },
                series: [{
                    name: '',
                    // color: 'rgba(119, 152, 191, .5)',
                    color: 'rgba(57, 66, 100, .80)',
                    data: ancillary_data_forPlotting,
                    lineWidth: 0.0,
                    lineColor: "#9ba0b1",
                    states: {
                        hover: {
                            lineWidthPlus: 0.25
                        }
                    }
                }, {
                    name: 'Error',
                    type: 'errorbar',
                    color: 'rgba(57, 66, 100, .40)',
                    data: ancillary_data_error_forPlotting,
                    zIndex: 0,
                    marker: {
                        radius: "square",
                        symbol: 0
                    },  
                    stickyTracking: true,   
                    animation: false,
                    whiskerWidth: 0.5,
                    states: {
                        hover: {
                            enable: false,
                            lineWidthPlus: 0.25
                        },
                        inactive: {
                            opacity: 1
                        }   
                    },              
                    tooltip: {
                        followPointer: false,
                        headerFormat: '<b>{series.name}</b><br>',
                        xDateFormat: '%Y-%m-%d',
                        pointFormat: 'y-min: {point.high}<br>y-max: {point.low}'
                    }
                }],

                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: ["downloadPNG", "downloadJPEG", "downloadPDF", "downloadSVG", 'downloadCSV','downloadXLS']
                        }
                    }
                }
            });

            if (flux_type.includes('photon')) {
                    chart1.yAxis[0].setTitle({ text: "Photon Flux ( 0.1-100 GeV ph cm<sup>-2</sup> s<sup>-1</sup> )" });
            } else {
                chart1.yAxis[0].setTitle({ text: "Energy Flux ( 0.1-100 GeV MeV cm<sup>-2</sup> s<sup>-1</sup> )" });
            }
            
            // Reshow the plot
            if (flux.length != 0) {
                chart1.hideLoading();
                chart2.hideLoading();
            } else {
                chart1.showLoading('Data Unavailable');
                chart2.showLoading('Data Unavailable');
            }

            // });

            // fillDoughnutPlots()


            // Hide the highcharts credit (this information will appear with other credits in the about section)
            $('.highcharts-credits')[0].innerHTML=''
            $('.highcharts-credits')[1].innerHTML=''


            // Make sure the data tooltip toggle is honored
            if (show_tooltip === false) {

                chart1.tooltip.defaultFormatter = function(){return false}
                chart2.tooltip.defaultFormatter = function(){return false}

            } 

        }

        function fillDoughnutPlots() {

            var fake_data = [0, 1, 2, 102]

            var ts = []
            fit_tolerance = []
            fit_convergance = []

            for (i = 0; i < data['ts'].length; i++) { 

                ts.push(data['ts'][i][1])
                fit_tolerance.push(data['fit_tolerance'][i][1])
                fit_convergance.push(data['fit_convergance'][i][1])

            }

            fit_tolerance_keys = [1e-8, 1e-4, 1]
            fit_tolerance_distribution = [0,0,0,0]

            fit_convergance_keys = [0, 1, 2, 101]
            fit_convergance_distribution = [0,0,0,0]

            ts_keys = ['Detections', 'Upper Limits']
            ts_distribution = [0,0]

            for (var i = 0; i < ts.length; i++) {

                if (ts[i] >= parseFloat(ts_min)) {
                    ts_distribution[0] = ts_distribution[0] + 1
                } else {
                    ts_distribution[1] = ts_distribution[1] + 1
                }

                var value = fit_tolerance[i]
                for (var j = 0; j < fit_tolerance_keys.length; j++) {

                    key = fit_tolerance_keys[j]
                    if (value === key) {
                        fit_tolerance_distribution[j] = fit_tolerance_distribution[j] + 1
                    }
                }

                var value = fit_convergance[i]
                for (var j = 0; j < fit_convergance_keys.length; j++) {

                    key = fit_convergance_keys[j]

                    if (value === key) {
                        fit_convergance_distribution[j] = fit_convergance_distribution[j] + 1
                    }
                }

            }

            // Fix the convergance keys to reflect 102 rather than 101
            fit_convergance_keys = [0, 1, 2, 102]



            // var palette = ['#142850', '#27496d', '#0c7b93', '#00a8cc', '#26689d']
            var palette = ['#27496d', '#276c6d', '#6d4b27', '#6d2749']

            colors = []
            for (var i = 0, j = fake_data.length; i < j; i++) {
                colors.push(palette[i % palette.length])
            }

            try {
                window.myDoughnut1.destroy();
            } catch(err) {
                //
            }

            // Create random data
            var randomScalingFactor = function() {
                return Math.round(Math.random() * 100);
            };

            // Configure the detector distribution plot
            var config1 = {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: fit_convergance_distribution,
                        backgroundColor: colors,
                        label: 'Dataset 1'
                    }],

                    labels: fit_convergance_keys
                    // labels: classificationsUnique
                },
                options: {
                    responsive: true,
                    legend: {
                        display: true,
                        position: 'bottom',                                 
                        labels: { 
                            fontSize: 14,
                            boxWidth: 10,
                            usePointStyle: false,
                            fullWidth: false,
                            fontFamily: 'Helvetica Neue',
                            fontColor: '#333333'
                        }
                    },
                    title: {
                        display: true,
                        text: 'MINUIT Return Code Distribution',
                        fontFamily: 'Helvetica Neue',
                        fontColor: '#333333',
                        fontSize: 14

                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    }
                }
            };

            // Configure the detector distribution plot
            var config2 = {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: fit_tolerance_distribution ,
                        backgroundColor: colors,
                        label: 'Dataset 2'
                    }],

                    labels: fit_tolerance_keys 
                    // labels: classificationsUnique
                },
                options: {
                    responsive: true,
                    legend: {
                        display: true,
                        position: 'bottom',                                 
                        labels: { 
                            fontSize: 14,
                            boxWidth: 10,
                            usePointStyle: false,
                            fullWidth: false,
                            fontFamily: 'Helvetica Neue',
                            fontColor: '#333333'
                        }
                    },
                    title: {
                        display: true,
                        text: 'PyLikelihood Fit Tolerance Distribution',
                        fontFamily: 'Helvetica Neue',
                        fontColor: '#333333',
                        fontSize: 14

                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    }
                }
            };

            // Configure the detector distribution plot
            var config3 = {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: ts_distribution,
                        backgroundColor: colors,
                        label: 'Dataset 2'
                    }],

                    labels: ts_keys
                    // labels: classificationsUnique
                },
                options: {
                    responsive: true,
                    legend: {
                        display: true,
                        position: 'bottom',                                 
                        labels: { 
                            fontSize: 14,
                            boxWidth: 10,
                            usePointStyle: false,
                            fullWidth: false,
                            fontFamily: 'Helvetica Neue',
                            fontColor: '#333333'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Detection Distribution',
                        fontFamily: 'Helvetica Neue',
                        fontColor: '#333333',
                        fontSize: 14

                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    }
                }
            };

            // // Hide the highcharts credit (this information will appear with other credits in the about section)
            // $('.highcharts-credits')[0].innerHTML=''
            // $('.highcharts-credits')[1].innerHTML=''

            // Create the detector distribution plot
            var ctx1 = document.getElementById('chart-area_1').getContext('2d');
            window.myDoughnut1 = new Chart(ctx1, config1);

            // Create the GBM coverage plot
            var ctx2 = document.getElementById('chart-area_2').getContext('2d');
            window.myDoughnut2 = new Chart(ctx2, config2);

            // Create the GBM coverage plot
            var ctx3 = document.getElementById('chart-area_3').getContext('2d');
            window.myDoughnut3 = new Chart(ctx3, config3);
        }

        function url_parser(key) {

            // Get the source name passed as a get parameter
            var url_string = window.location.href;

            // Extract the url parameters
            if (url_string.includes("?" + key + "=")) {

                url_parameters = url_string.split('?')[1]

                if (url_parameters.includes("&")) {

                    $.each(url_parameters.split("&"), function(i, url_parameter) { 
                        if (url_parameter.includes(key + "=")) {
                            value = url_parameter.replace(key + '=','')
                        }
                    })

                } else {

                    value = url_parameters.replace(key + '=','')
                }
            }

            return value
        }

        function getCatalogData() {

            var spectrumType;
            var photonIndex;

            // Get the source of interest
            var source_name = GetUrlValue('source_name')

            // de-encode the full source name
            var source_name = source_name.replace('_',' ')

            // Setup the fetch url
            var source_name_urlEncoded = encodeURIComponent(source_name);
            var magic_word_urlEncoded = encodeURIComponent(magic_word_submitted);

            var URL = "queryDB.php?typeOfRequest=sourceData&source_name=" + source_name_urlEncoded + "&magicWord=" + magic_word_urlEncoded;

            console.log(URL);

            // Get the data
            $.ajax({url: URL, success: function(responseText){

                // Parse the resulting json file
                // var data = JSON.parse(responseText);

                // Get the data
                var data;
                try {
                    data = JSON.parse(responseText);
                } catch (error) {
                    console.log('Error parsing query response.')
                    $('#main').hide()
                    return
                }

                // Update the side table with the results
                $.each( data[0], function( key, value ) {

                    // console.log(key + ": " + value );

                    if (key.includes('RAJ2000') || key.includes('DEJ2000') || key.includes('GLON') || key.includes('GLAT')) {
                        value = value + "&deg;"

                    } else if (key.includes('Flux1000')) {
                        value = parseFloat(value).toPrecision(2);
                        value = value + " ph cm<sup>-2</sup> s<sup>-1</sup>"

                    } else if (key.includes('Energy_Flux100')) {
                        value = parseFloat(value).toPrecision(2);
                        value = value + " MeV cm<sup>-2</sup> s<sup>-1</sup>"

                    } else if (key.includes('SpectrumType')) {
                        spectrumType = value

                    } else if (key.includes('Variability_Index')) {
                        value = parseFloat(value.replace(',',''));
                    }

                    if (key.includes('PL_Index') || key.includes('LP_Index') || key.includes('PLEC_Index') || key.includes('LP_beta') || key.includes('Unc_PL_Index') || key.includes('Unc_LP_Index') || key.includes('Unc_LP_beta') || key.includes('Unc_PLEC_Index')) {
                    } else {
                        document.getElementById(key).innerHTML = value
                    }

                });

                if (spectrumType.includes('PowerLaw')) {

                    var PL_Index = String(-1*parseFloat(data[0]['PL_Index']).toPrecision(2))
                    var Unc_PL_Index = String(parseFloat(data[0]['Unc_PL_Index']).toPrecision(2)) 

                    document.getElementById('SpectralIndex_label1').innerHTML = 'Photon Index &Gamma;:'
                    document.getElementById('SpectralIndex1').innerHTML = PL_Index + ' &plusmn; ' + Unc_PL_Index

                    // Set the ancillary data table y-axis label
                    ancillary_data_label = 'Photon Index &Gamma;'

                } else if  (spectrumType.includes('LogParabola')) {

                    LP_Index = String(-1*parseFloat(data[0]['LP_Index']).toPrecision(3))
                    Unc_LP_Index = String(parseFloat(data[0]['Unc_LP_Index']).toPrecision(2)) 

                    LP_beta = String(-1*parseFloat(data[0]['LP_beta']).toPrecision(2))
                    Unc_LP_beta = String(parseFloat(data[0]['Unc_LP_beta']).toPrecision(2)) 

                    document.getElementById('SpectralIndex_label1').innerHTML = 'Photon Index &alpha;:'
                    document.getElementById('SpectralIndex_label2').innerHTML = 'Photon Index &beta;:'
                    document.getElementById('SpectralIndex1').innerHTML = LP_Index + ' &plusmn; ' + Unc_LP_Index
                    document.getElementById('SpectralIndex2').innerHTML = LP_beta + ' &plusmn; ' + Unc_LP_beta
                    $('#SpectralIndex_row2').show()

                    // Set the ancillary data table y-axis label
                    ancillary_data_label = 'Photon Index &alpha;'


                } else if  (spectrumType.includes('PLSuperExpCutoff')) {

                    PLEC_Index = String(-1*parseFloat(data[0]['PLEC_Index']).toPrecision(2))
                    Unc_PLEC_Index = String(parseFloat(data[0]['Unc_PLEC_Index']).toPrecision(2)) 

                    document.getElementById('SpectralIndex_label1').innerHTML = 'Photon Index &Gamma;:'
                    document.getElementById('SpectralIndex1').innerHTML = PLEC_Index + ' &plusmn; ' + Unc_PLEC_Index

                    // Set the ancillary data table y-axis label
                    ancillary_data_label = 'Photon Index 1;'

                }

            }});
        }

        function getLightCurveData() {

            // Get the source of interest
            var source_name = GetUrlValue('source_name')

            // de-encode the full source name
            var source_name = source_name.replace('_',' ')

            // Setup the fetch url
            var source_name_urlEncoded = encodeURIComponent(source_name);
            var magic_word_urlEncoded = encodeURIComponent(magic_word_submitted);

            URL_light_curve_data = "queryDB.php?typeOfRequest=lightCurveData&source_name=" + source_name_urlEncoded + '&cadence=' + cadence + '&flux_type=' + flux_type + '&index_type=' + index_type + '&ts_min=' + ts_min + "&magicWord=" + magic_word_urlEncoded;

            console.log(URL_light_curve_data);

            // Get the data
            $.ajax({url: URL_light_curve_data, success: function(responseText){

                // Parse the resulting json file
                // data = JSON.parse(responseText);

                // Get the data
                try {
                    data = JSON.parse(responseText);
                } catch (error) {
                    console.log('Error parsing query response.')
                    console.log(error)
                    $('#main').hide()
                    return
                }

                // Make a deep copy of the original data array
                // data_original = [...data]
                data_original = JSON.parse(JSON.stringify(data))

                fillTable()

                fillDoughnutPlots()

                prepareLightCurveData() 

            }});
        }
    
        function prepareLightCurveData() {


            // Extact some useful arrays
            ts = data['ts']
            flux = data['flux']
            flux_error = data['flux_error']
            flux_upper_limits = data['flux_upper_limits']
            fit_convergance = data['fit_convergance']        
            photon_index = data['photon_index']     

            // Make an array of the METs
            mets = []
            for (var i in data['ts']) {
                mets.push(data['ts'][i][0])
            }

            // Loop through and add MET information to the arrays that encompass the entire data range
            $.each( data, function( key, value ) {
                for (i = 0; i < data[key].length; i++) { 
                    if ((data[key][i].length != 2) && (data[key][i].length != 3) && (key != 'bin_id')) {
                        var item = new Array;
                        item.push(mets[i])
                        item.push(data[key][i])
                        data[key][i] = item

                    }
                }
            });

            // Correct the flux error precision
            for (i = 0; i < flux_error.length; i++) { 
                flux_error[i][1] = parseFloat(flux_error[i][1].toPrecision(3))
                flux_error[i][2] = parseFloat(flux_error[i][2].toPrecision(3))
            }



            // Find all of the nonconvergant fits
            nonconvergant_fits = []
            if (show_nonconvergant_fits == false) {
                for (i = 0; i < ts.length; i++) { 
                    if (fit_convergance[i][1] > 0) {
                        nonconvergant_fits.push(mets[i])
                    }
                }
            }

            // Find all of the unconstrained fits
            unconstrained_fits = []
            if (show_unconstrained_fits == false) {
                for (i = 0; i < photon_index.length; i++) { 
                    if (photon_index[i][1] === 5) {
                        unconstrained_fits.push(photon_index[i][0])
                    }
                }
            }

            // Make deep copies of the original data for plotting purposes
            flux_forPlotting = [...flux];
            flux_error_forPlotting = [...flux_error];
            flux_upper_limits_forPlotting = [...flux_upper_limits]; 

            // Remove any non-convergant fits from the arrays to be plotted
            if (show_nonconvergant_fits === false) {
                for (var i = 0; i < nonconvergant_fits.length; i++) {

                    var met = nonconvergant_fits[i]
                    for (var j = 0; j < flux_forPlotting.length; j++) {
                        if (flux_forPlotting[j][0] === met) {
                            flux_forPlotting.splice(j,1)
                            flux_error_forPlotting.splice(j,1)
                            break
                        }
                    }

                    for (var j = 0; j < flux_upper_limits_forPlotting.length; j++) {
                        if (flux_upper_limits_forPlotting[i][0] === met) {
                            flux_upper_limits_forPlotting.splice(j,1)
                            flux_upper_limits_forPlotting.splice(j,1)
                            break
                        }
                    }

                }
            }

            // Remove any unconstrained fits from the arrays to be plotted
            if (show_unconstrained_fits === false) {
                for (i = 0; i < unconstrained_fits.length; i++) {

                    var met = unconstrained_fits[i]
                    for (var j = 0; j < flux_forPlotting.length; j++) {
                        if (flux_forPlotting[j][0] === met) {
                            flux_forPlotting.splice(j,1)
                            flux_error_forPlotting.splice(j,1)
                            break
                        }
                    }

                    for (var j = 0; j < flux_upper_limits.length; j++) {
                        if (flux_upper_limits_forPlotting[j][0] === met) {
                            flux_upper_limits_forPlotting.splice(j,1)
                            flux_upper_limits_forPlotting.splice(j,1)
                            break
                        }
                    }

                }
            }    


            // Update the side table with the results
            // if (xtitle.includes('Mission Elapsed Time (seconds)') === false) {
                $.each( data, function( key, value ) {

                    for (i = 0; i < data[key].length; i++) { 

                        // Extract the current MET
                        // var met = data[key][i][0]
                        var met = data_original[key][i][0]

                        // Convert the MET to a date
                        var date

                        if (xtitle.includes('Mission Elapsed Time (seconds)')) {
                        	date = met
                        }
                        if (xtitle.includes('Date (UTC)')) {
                            date = MET2date(met)
                            date = Date.parse(date)
                        } else if (xtitle.includes('Modified Julian Date')) {
                            date = MET2MJD(met)
                        } else if (xtitle.includes('Julian Date')) {
                            date = MET2JD(met)
                        }

                        // Place the new date back into the data array
                        data[key][i][0] = date

                        // Convert the METs for the non-convergant fits
                        var index = nonconvergant_fits.indexOf(met)
                        if (index !=-1) {
                            nonconvergant_fits[index] = date
                        }

                        // Convert the METs for the convergant fits
                        var index = unconstrained_fits.indexOf(met)
                        if (index !=-1) {
                            unconstrained_fits[index] = date
                        }

                    }
                })                 
            // }                     


            // Extract the ancillary data
            if (ancillary_type.includes('photon_index')) {
                ancillary_data = data['photon_index']  
                ancillary_data_error = data['photon_index_error'] 

            } else {
                ancillary_data = data[ancillary_type]
                ancillary_data_error = null;
            }


            // Make deep copies of the original data for plotting purposes
            ancillary_data_forPlotting = [...ancillary_data];
            if (ancillary_data_error === null) {
                ancillary_data_error_forPlotting = null
            } else {
            	ancillary_data_error_forPlotting = [...ancillary_data_error];
            }               

            // Remove any non-convergant fits from the arrays to be plotted
            if (show_nonconvergant_fits === false) {

                for (var i = 0; i < nonconvergant_fits.length; i++) {

                    var met = nonconvergant_fits[i]

                    for (var j = 0; j < ancillary_data_forPlotting.length; j++) {
                        if (ancillary_data_forPlotting[j][0] === met) {
                            ancillary_data_forPlotting.splice(j,1)
                            if (ancillary_data_error_forPlotting != null) {
								ancillary_data_error_forPlotting.splice(j,1)
                            }
                            break
                        }
                    }

                }
            }

            // Remove any unconstrained fits from the arrays to be plotted
            if (show_unconstrained_fits === false) {

                for (i = 0; i < unconstrained_fits.length; i++) {

                    var met = unconstrained_fits[i]

                      for (var j = 0; j < ancillary_data_forPlotting.length; j++) {
                        if (ancillary_data_forPlotting[j][0] === met) {
                            ancillary_data_forPlotting.splice(j,1)
                            if (ancillary_data_error_forPlotting != null) {
								ancillary_data_error_forPlotting.splice(j,1)
                            }
                            break
                        }
                    }

                }
            }  



            // Create the plots
            createScatterPlot()
            // fillTable()

            // Check if data was successfully retrieved. If the passphrase wasn't set before, set it to the supplied magic_word
            if (data['ts'].length != 0) {
                if (passphrase == null) {
                    setCookieData('passphrase', magic_word_submitted, 1)  // Cookie expires in 1 day
                }
            }               
        }

        // Fill the primary table
        function fillTable() {

            // Setup the row array
            var row = new Array(), j = -1;

            var flux_header
            if (flux_type.includes('energy')) {
                flux_header = 'Energy Flux [0.1-100 GeV]<BR>(MeV cm<sup>-2</sup> s<sup>-1</sup>)'
            } else {
                flux_header = 'Photon Flux [0.1-100 GeV]<BR>(photons cm<sup>-2</sup> s<sup>-1</sup>)'
            }

            // Create the header string
            var header = '<tr> \
            <th style="text-align: center; min-width:150px">Date<BR>(UTC)</th> \
            <th style="text-align: center;">Julian Date<BR></th> \
            <th style="text-align: center;">MET</th> \
            <th style="text-align: center;">TS</th> \
            <th id="flux_header" style="text-align: center;">' + flux_header + '</th> \
            <th style="text-align: center;">Photon Index</th> \
            <th style="text-align: center;">Fit Tolerance</th> \
            <th style="text-align: center;">MINUIT Return Code</th> \
            <th style="text-align: center;">Analysis Log</th> \
            '

            header = header + '</tr>'

            // Loop through each data entry and add columns to the corresponding row entry
            var j_detections = 0
            var k_nondetections = 0
            for (var i=0, size=data['ts'].length; i<size; i++) {

                var met = data['ts'][i][0]
                var date = MET2date(met)
                var date = String(date).slice(4,15)
                var jd = MET2JD(met)

                var ts = data['ts'][i][1]

                var fit_convergance = data['fit_convergance'][i][1]
                var fit_tolerance = data['fit_tolerance'][i][1]

                // Get the source name and decode it (but leave the underscore in place)
                var source_name = GetUrlValue('source_name')

                // Get the bin id 
                var bin_id = data['bin_id'][i]

                // Create the link to the log file
                var log_filename = 'bin' + bin_id + '.log'
                var log_link = 'https://www.slac.stanford.edu/~kocevski/LCR/logs/' + source_name + '/' + cadence + '/' + log_filename

                var flux_string;
                var photon_index_string;

                if (ts >= parseFloat(ts_min)) {

                    var flux = data['flux'][j_detections][1].toExponential(2)
                    var flux_error = flux - data['flux_error'][j_detections][1]
                    // var flux_error_string = flux_error.toExponential(2)
                    flux_string = flux + ' &#177; ' + flux_error.toExponential(2)


                	var photon_index = data['photon_index'][j_detections][1]
                	var photon_index_error = photon_index - data['photon_index_error'][j_detections][1]
                	photon_index_string = -photon_index + ' &#177; ' + Math.abs(photon_index_error.toFixed(2))

                    j_detections = j_detections + 1


                } else {

                    var flux_upper_limit = data['flux_upper_limits'][k_nondetections][1]
                    flux_string = '< ' + flux_upper_limit.toExponential(2)
                    photon_index_string = '-'

                    k_nondetections = k_nondetections + 1
                }

                row[++j] = '<tr>';

                row[++j] ='<td id="date" style="text-align: center;">' + date + '</td>';
                row[++j] ='<td id="jd" style="text-align: center;">' + jd + '</td>';
                row[++j] ='<td id="met" style="text-align: center;">' + met + '</td>';
                row[++j] ='<td id="ts" style="text-align: center;">' + ts + '</td>';
                row[++j] ='<td id="flux" style="text-align: center;">' + flux_string + '</td>';
                row[++j] ='<td id="photon_index" style="text-align: center;">' + photon_index_string + '</td>';
                row[++j] ='<td id="fit_tolerance" style="text-align: center;">' + fit_tolerance + '</td>';
                row[++j] ='<td id="fit_convergance" style="text-align: center;">' + fit_convergance + '</td>';
                row[++j] ='<td id="log" style="text-align: center;"><a href="' + log_link + '" onclick="window.open(this.href,\'targetWindow\'); return false;">' + log_filename + '</a></td>';

            }



            // Add the header to the start of the array
            row.unshift(header);

            // Join the row array into one long string and place it inside the table element
            // $('#dataTable_4FGL').html(row.join('')); 
            document.querySelector('#data_table').innerHTML = row.join(''); 

            // // Hide the spinner
            // $("#loadingSpinnerImage").hide();

            // Show the table
            $("#dataTable_4FGL").show();
        }

        // Check for an existing cookie
        function checkCookie() {
            
            // Check if the cookie contains the passphrase data
            // passphrase = getCookieData("passphrase");

            console.log("Cookie stored passphrase = " + passphrase)

            // passphrase = '130427A'
            console.log(passphrase)

            if (passphrase == null) {

                // Show the passphrase modal if the cookie doesn't contain the passphrase data
                $('#magic_word_dialog').modal('show');

            } else {

                // Use the passphrase in the cookie to query the trigger list
                magic_word_submitted = passphrase;

                // Get the data
                getCatalogData();
                getLightCurveData();
            }
        }

        // Query the database when the page is finished loading
        $(function() {

            // $(window).on('load',function(){
            //  $('#magic_word_dialog').modal('show');
            // });  
                

            $("#submitForm").on('click', function() {
                $("#magic_word_form").submit();
            });

            $("#dismissForm").on('click', function() {
                $('#main').hide()
            });

            $("#magic_word_form").on("submit", function(e) {

                // Get the form data
                // magic_word_submitted = document.forms["magic_word_form"]["magic_word"].value;
                magic_word_submitted = '130427A'
                
                e.preventDefault();

                $('#magic_word_dialog').modal('hide');

                getCatalogData();
                getLightCurveData();

            });


            $('.list-group li').click(function(e) {
                e.preventDefault()

                $that = $(this);

                catalog = $that.attr("id");

                $that.parent().find('li').removeClass('active');
                $that.addClass('active');

                // Hide the current table
                $("#dataTable").hide();
                $("#loadingSpinnerImage").show();
     
                // Show the loading spinner
                // $("#loadingSpinnerImage").css('display', 'block');

                queryDB('SourceList', magic_word_submitted, catalog);
            });

            $('#cadence_selector1 button').click(function() {
                $(this).removeClass('btn-default')
                $(this).addClass('btn-primary')
                $(this).addClass('active')

                $(this).siblings().removeClass('active')
                $(this).siblings().removeClass('btn-primary');
                $(this).siblings().addClass('btn-default');

                cadence = $(this).attr("id");

                // Add the loading data text
                chart1.showLoading('Loading data...');
                chart2.showLoading('Loading data...');

                // Reload the data and update the plots
                getLightCurveData();    
            })

            // Light curve data plot selector
            $('.dropdown-menu.flux-type').on('click', 'li', function() {

                $('.dropdown-menu.flux-type li.active').removeClass('active');
                $(this).addClass('active');

                // Get the label for the user selected projection and update the dropdown button
                flux_type_label = $(this).find('a')[0].innerHTML
                $('.dropdown-toggle.flux-type')[0].innerHTML =  flux_type_label + ' <span class="caret"></span>'                

                // Redefine the flux type to be displayed in the light curve plot
                flux_type = $(this).attr("id");

                // Reload the data and update the plots
                chart1.showLoading('Loading data...');
                chart2.showLoading('Loading data...');
                getLightCurveData();
            });

            // Spectral fitting type selector
            $('.dropdown-menu.index_type').on('click', 'li', function() {

                $('.dropdown-menu.index_type li.active').removeClass('active');
                $(this).addClass('active');

                // Get the label for the user selected projection and update the dropdown button
                index_type_label = $(this).find('a')[0].innerHTML
                $('.dropdown-toggle.index_type')[0].innerHTML =  index_type_label + ' <span class="caret"></span>'                

                // Redefine the flux type to be displayed in the light curve plot
                index_type = $(this).attr("id");

                // Reload the data and update the plots
                chart1.showLoading('Loading data...');
                chart2.showLoading('Loading data...');
                getLightCurveData();
            });

            // Minimum detection threshold
            $('.dropdown-menu.ts_min').on('click', 'li', function() {

                $('.dropdown-menu.ts_min li.active').removeClass('active');
                $(this).addClass('active');

                // Get the label for the user selected projection and update the dropdown button
                ts_min_label = $(this).find('a')[0].innerHTML
                $('.dropdown-toggle.ts_min')[0].innerHTML =  ts_min_label + ' <span class="caret"></span>'                

                // Redefine the flux type to be displayed in the light curve plot
                ts_min = $(this).attr("id").replace('ts','');

                // Reload the data and update the plots
                chart1.showLoading('Loading data...');
                chart2.showLoading('Loading data...');
                getLightCurveData();
            });

            // Light curve data plot selector
            $('.dropdown-menu.xaxis-type').on('click', 'li', function() {

                $('.dropdown-menu.xaxis-type li.active').removeClass('active');
                $(this).addClass('active');

                // Get the id of the element that was just set active
                xaxis_active_id = $(this).attr('id').replace('2','')

                // Add the active state to the mirrored dropdown button
                $('#' + xaxis_active_id).addClass('active')
                $('#' + xaxis_active_id + '2').addClass('active')

                // Get the label for the user selected projection and update the dropdown button
                xaxis_type_label = $(this).find('a')[0].innerHTML

                $('.dropdown-toggle.xaxis-type')[0].innerHTML =  xaxis_type_label + ' <span class="caret"></span>'      
                $('.dropdown-toggle.xaxis-type')[1].innerHTML =  xaxis_type_label + ' <span class="caret"></span>'      

                // console.log(xaxis_type)

                if (xaxis_type_label.includes('Date')) {

                    chartType = 'datetime';
                    xtitle = 'Date (UTC)'

                    // pointFormat_x1 = 'Date = {point.x:%e %b %Y}'
                    // pointFormat_chart1 = pointFormat_x1 + '<br>' + pointFormat_y1

                } else if (xaxis_type_label.includes('MET')) {

                    chartType = 'linear';
                    xtitle = 'Mission Elapsed Time (seconds)'

              //    pointFormat_x1 = 'MET = {point.x}'
                    // pointFormat_chart1 = pointFormat_x1 + '<br>' + pointFormat_y1

                } else if (xaxis_type_label.includes('MJD')) {

                    chartType = 'linear';
                    xtitle = 'Modified Julian Date'

              //    pointFormat_x1 = 'MJD = {point.x}'
                    // pointFormat_chart1 = pointFormat_x1 + '<br>' + pointFormat_y1

                } else if (xaxis_type_label.includes('JD')) {

                    chartType = 'linear';
                    xtitle = 'Julian Date'

                    // pointFormat_x1 = 'JD = {point.x}'
                    // pointFormat_chart1 = pointFormat_x1 + '<br>' + pointFormat_y1
                }

                // Add a loading message to the charts
                chart1.showLoading('Loading data...');
                chart2.showLoading('Loading data...');

                // Reload the data and update the plots
                getLightCurveData();
                // prepareLightCurveData();

            });

            // Ancillary data plot selector
            $('.dropdown-menu.ancillary-data').on('click', 'li', function() {

                $('.dropdown-menu.ancillary-data li.active').removeClass('active');
                $(this).addClass('active');

                // Get the label for the user selected projection and update the dropdown button
                ancillary_data_label = $(this).find('a')[0].innerHTML
                $('.dropdown-toggle.ancillary-data')[0].innerHTML =  ancillary_data_label + ' <span class="caret"></span>'    


                console.log('ancillary_data_label = ' + ancillary_data_label)
                // Redefine the flux type to be displayed in the light curve plot
                
                ancillary_type = $(this).attr("id");

                // Define the label for the second plot
                // var ancillary_data_label = $('.dropdown-menu.ancillary-data li.active')

                // Hide the chart and remake the plot
                // $('#chart2').addClass('hidden', function() {

                ancillary_data = data[ancillary_type]

                if (ancillary_type.includes('photon_index')) {
                    ancillary_data_error = data['photon_index_error']
                } else {
                	ancillary_data_error = null;
                }

                // ancillary_data_forPlotting = []
                // ancillary_data_error_forPlotting = []

                // if ((show_nonconvergant_fits == false) && (show_unconstrained_fits == false)) {
                //     for (i = 0; i < ancillary_data.length; i++) {
                //         var date = ancillary_data[i][0]
                //         if ((nonconvergant_fits.indexOf(date) === -1) && (unconstrained_fits.indexOf(date) === -1)) {
                            
                //             ancillary_data_forPlotting.push(ancillary_data[i])

                //             if (ancillary_data_error != null) {
                //                 ancillary_data_error_forPlotting.push(ancillary_data_error[i])
                //             }
                //         }
                //     }
                // }

                // if (ancillary_data_error === null) {
                //     ancillary_data_error_forPlotting = null
                // }

                // Make deep copies of the original data for plotting purposes
                ancillary_data_forPlotting = [...ancillary_data];
                
                if (ancillary_data_error === null) {
                    ancillary_data_error_forPlotting = null
                } else {
                	 ancillary_data_error_forPlotting = [...ancillary_data_error];
                }               

                // Remove any non-convergant fits from the arrays to be plotted
                if (show_nonconvergant_fits === false) {

                    for (var i = 0; i < nonconvergant_fits.length; i++) {

                        var met = nonconvergant_fits[i]

                        for (var j = 0; j < ancillary_data_forPlotting.length; j++) {
                            if (ancillary_data_forPlotting[j][0] === met) {
                                ancillary_data_forPlotting.splice(j,1)
                                if (ancillary_data_error_forPlotting != null) {
									ancillary_data_error_forPlotting.splice(j,1)
                                }
                                break
                            }
                        }

                    }
                }

                // Remove any unconstrained fits from the arrays to be plotted
                if (show_unconstrained_fits === false) {

                    for (i = 0; i < unconstrained_fits.length; i++) {

                        var met = unconstrained_fits[i]

                          for (var j = 0; j < ancillary_data_forPlotting.length; j++) {
                            if (ancillary_data_forPlotting[j][0] === met) {
                                ancillary_data_forPlotting.splice(j,1)
                                if (ancillary_data_error_forPlotting != null) {
									ancillary_data_error_forPlotting.splice(j,1)
                                }
                                break
                            }
                        }

                    }
                }  


                chart2.showLoading('Loading data...');

                    // Remove the existing data from the second plot
                    while (chart2.series.length > 0) {
                        chart2.series[0].remove()
                    }

                    // Set the label for the updated second plot
                    // ancillary_data_label = $('.dropdown-menu.ancillary-data li.active').contents().html();
                    chart2.yAxis[0].setTitle({ text: ancillary_data_label});  

                    // Add the new data series
                    chart2.addSeries({
                        name: '',
                        // color: 'rgba(119, 152, 191, .5)',
                        color: 'rgba(57, 66, 100, .80)',
                        // data: data[ancillary_type],
                        data: ancillary_data_forPlotting,
                        lineWidth: 0.0,
                        lineColor: "#9ba0b1",
                        states: {
                            hover: {
                                lineWidthPlus: 0.25
                            }
                        },

                    });

                    chart2.series[0].update({
                        tooltip:{

                                pointFormatter: function() {

                                    var x_string
                                    if (xtitle.includes('Mission')) {
                                        x_string = 'MET = ' + this.x
                                    } else if (xtitle.includes('Modified Julian Date')) {
                                        x_string = 'MJD = ' + this.x
                                    } else if (xtitle.includes('Julian Date')) {
                                        x_string = 'JD = ' + this.x
                                    } else if (xtitle.includes('Date')) {
                                        x_string = 'Date = ' + Highcharts.dateFormat('%e %b %Y', this.x)
                                    }

                                    // Define the yaxis tooltip
                                    var y_string = ancillary_data_label + ' = ' + this.y

                                    // Combine the tooltip elements
                                    var tooltip_string =  x_string + '<br>' + y_string

                                    return tooltip_string

                                }
                        }
                    });             

                    // chart2.yAxis[0].update({
                    //    type: 'logarithmic'
                    // });                              

                    // Add the data series error, if applicable
                    if (ancillary_type.includes('photon_index')) {
                        chart2.addSeries({
                            name: 'Error',
                            type: 'errorbar',
                            color: 'rgba(57, 66, 100, .40)',
                            // data: data['photon_index_error'],
                            data: ancillary_data_error_forPlotting,
                            zIndex: 0,
                            marker: {
                                radius: "square",
                                symbol: 0
                            },  
                            stickyTracking: true,   
                            animation: false,
                            whiskerWidth: 0.5,
                            states: {
                                hover: {
                                    enable: false,
                                    lineWidthPlus: 0.25
                                },
                                inactive: {
                                    opacity: 1
                                }   
                            },              
                            tooltip: {
                                followPointer: false,
                                pointFormat: 'y-max: <b>{point.high}</b><br>y-min: <b>{point.low}</b>'
                            }
                        });
                    }  

                    chart2.hideLoading();

                // });
            });


            $('.dropdown-menu.ancillary-data li a').click(function(event) {

                event.preventDefault();
            });

            $('#Download').click(function(){
                if ( data == '') {
                    alert('No data to download!')
                    return 
                }
                
                download_table_as_csv('data_table')
            });

            // Light curve data plot selector
            $('.dropdown-menu.data-download').on('click', 'li', function() {

                event.preventDefault();

              // Get the id of the button that was clicked
              id = $(this).attr("id");
              
              if (id.includes('csv')) {
                download_table_as_csv('data_table')
              } else if (id.includes('json')) {
                window.open(URL_light_curve_data,'_newtab');
              } else {

                var dummy = document.createElement("textarea");
                document.body.appendChild(dummy);
                dummy.value = URL_light_curve_data;
                dummy.select();

                document.execCommand("copy");
                document.body.removeChild(dummy);

                alert('API link copied to clipboard:\r\n\r\n' + 'https://fermi.gsfc.nasa.gov/ssc/data/access/lat/LightCurveRepository/' + URL_light_curve_data)
                
              }                            
            });

            $('#show_tooltip').click( function(){

            	show_tooltip = this.checked

                if (this.checked === false) {

                	// Save the default tooltip formater for later reinstatement
                	defaultFormatter1 = chart1.tooltip.defaultFormatter 
                	defaultFormatter2 = chart1.tooltip.defaultFormatter 

                    chart1.tooltip.defaultFormatter = function(){return false}
                    chart2.tooltip.defaultFormatter = function(){return false}

                } else {

                	// Reinstate the original tooltip formater
                    chart1.tooltip.defaultFormatter = defaultFormatter1;
                    chart2.tooltip.defaultFormatter = defaultFormatter2;
                }

            });
     
            $('#show_upper_limits').click( function(){

            	// Set the plot default
                show_upper_limits = this.checked

                if (this.checked === true) {

                    chart1.series[1].show()

                    if ($('#show_error_bars')[0].checked === true) {
                        chart1.series[2].show()
                    } else {
                        chart1.series[2].hide()
                    }

                } else {

                    chart1.series[1].hide()

                    if ($('#show_error_bars')[0].checked === true) {
                        chart1.series[2].show()
                    } else {
                        chart1.series[2].hide()
                    }

                }

            });

            $('#show_error_bars').click( function(){

            	// Set the plot default
            	show_error_bars = this.checked

                if (this.checked === true) {
                    chart1.series[2].show()
                } else {
                    chart1.series[2].hide()
                }

            });

            $('#show_data_line').click( function(){

                if (this.checked === true) {
					data_line_width = 0.5

                } else {
					data_line_width = 0
                }

                chart1.series[0].userOptions.lineWidth = data_line_width
                chart1.series[0].update()

            });

            $('#show_nonconvergant_fits').click( function(){

            	show_nonconvergant_fits = this.checked 

            	// Reload the light curve data
            	// getLightCurveData()
                prepareLightCurveData()

            });


            $('#show_unconstrained_fits').click( function(){

            	show_unconstrained_fits = this.checked 

            	// Reload the light curve data
            	// getLightCurveData()
                prepareLightCurveData()

            });


            $('[data-toggle="tooltip"]').tooltip({
                delay: {"show": 300, "hide": 100}
            }); 

            

            // getCatalogData();
            // getLightCurveData();
            
            // Check if a passphrase cookie exists and get the data if it does, or luanch the magic word model if it doesn't
            checkCookie()

        });



    </script>

    <!-- main starts here -->       
    <div id="main" style="width:100%;"> 

        <!-- Background container -->
        <div class="background-container">

            <!-- Start NASA Container -->
            <div id="nasa-container" style="margin:8px 0 0 10px">

                <!-- Start NASA Banner -->
                <div id="nasa-banner-plain">

                    <!-- Left - Logo -->
                    <div class="nasa-logo">
                        <a href="http://www.nasa.gov/"><img src="http://fermi.gsfc.nasa.gov/ssc/inc/img/nasa_logo.gif" width="140" height="98" border="0" alt="NASA Logo"></a>
                    </div>
                
                    <!-- Middle - Affiliations -->
                    <div id="nasa-affiliation">
                        <h1><a href="http://www.nasa.gov/">National Aeronautics and Space Administration</a></h1>
                        <h2><a href="http://www.nasa.gov/goddard">Goddard Space Flight Center</a></h2>
                    </div>
                    
                    <!-- Right - Search and Links -->
                    <div id="nasa-search-links">
                        <div id="header-links">
                            <a href="/ssc/">FSSC</a> &bull; <a href="http://heasarc.gsfc.nasa.gov/">HEASARC</a> &bull; <a href="http://science.gsfc.nasa.gov/">Sciences and Exploration</a>
                        </div>
                    </div>

                </div>
                <!-- End NASA Banner -->

            <!-- End NASA Container -->
            </div>

            <!-- Header starts here -->
            <div>
                <div style="float: left; padding-top:12px; padding-left:25px"><img middle; style="width: 100%; height: 100%" src="./img/Fermi_Small.png"></div>
                <div style="margin-left: 25px;padding-left: 75px; padding-bottom:20px; padding-top: 5px">
                    <H2>Fermi LAT Light Curve Repository - Source Report</H2>
                </div>
            </div>
            <!-- Header ends here -->

            <!-- sidebar start here -->
            <div style="width:350px; margin-left:25px; float:left;" id="sidebar">


                <!-- Light curve options panel start here -->       
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Light Curve Options</h3>
                    </div>

                    <div class="panel-body">


                        <div class="row-small-gutter">                            
                            <label for="name">Data Cadence:</label>
                        </div>

                        <div class="row-small-gutter">                            
                            <div style="float:left; margin: 10px 20px 10px 60px">
                                <div id="cadence_selector1" class="btn-group">
                                    <button id="daily" type="button" class="btn btn-default ">3 day</button>
                                    <button id="weekly" type="button" class="btn btn-primary active">1 week</button>
                                    <button id="monthly" type="button" class="btn btn-default">1 month</button>
                                </div>
                            </div>
                        </div>  

                        <BR>
                        <div class="row-small-gutter">
                            <div style="margin-top:50px"> 
                                <label for="name">Analysis Options:</label>
                            </div>
                        </div>

                        <div class="row-small-gutter">                            
    
                            <div class="col-sm-6" style="margin-left:0px; margin-top:10px">
                                <p>Minimum Detection Significance:</p>
                            </div>

                            <div class="col-sm-5" style="margin-left:0px; margin-top:6px">
                                <div class="dropdown">
                                    <button style="width:110px" id="ts_min_button" class="btn btn-default dropdown-toggle ts_min" type="button" data-toggle="dropdown">TS = 4 (2&sigma;)  <span class="caret"></span></button>
                                    <ul class="dropdown-menu ts_min" style="min-width:115px">
                                        <li id="ts4" class="active"><a href="#">TS = 4 (2&sigma;)</a></li>
                                        <li id="ts3"><a href="#">TS = 3 </a></li>
                                        <li id="ts2"><a href="#">TS = 2 </a></li>
                                        <li id="ts1"><a href="#">TS = 1 (1&sigma;)</a></li>
                                    </ul>
                                </div>
                            </div>  

                        </div>   

                        <div class="row-small-gutter" style="margin-top:60px"> 
                            <div class="col-sm-6" style="margin-left:0px; margin-top:10px">
                                <p>Spectral Fitting:</p>
                            </div>
                            <div class="col-sm-5" style="margin-left:0px; margin-top:5px">
                                <div class="dropdown">
                                    <button style="width:110px" class="btn btn-default dropdown-toggle index_type" type="button" data-toggle="dropdown">Fixed Index <span class="caret"></span></button>
                                    <ul class="dropdown-menu index_type" style="min-width:115px">
                                        <li id="fixed" class="active"><a href="#">Fixed Index </a></li>
                                        <li id="free"><a href="#">Free Index </a></li>
                                    </ul>
                                </div>
                            </div>                     
                        </div> 

                        <BR>

                        <div class="row-small-gutter">

                            <div style="margin-top:30px">
                                <label for="name">Plotting Options:</label>
                            </div>

                            <div class="col-sm-6">
                                 <label class="checkbox-inline"><input type="checkbox" value="" id="show_upper_limits" checked>Upper Limits</label>
                            </div>
                            <div class="col-sm-5">
                                <label class="checkbox-inline" title="This is a mouseover text!"><input type="checkbox" value="" id="show_error_bars" checked>Error Bars</label>
                                <!-- <label class="checkbox-inline" title="This is a mouseover text!"><input type="checkbox" value="" id="show_error_bars" checked>Error Bars</label> -->
                            </div>
                        </div>

                        <div class="row-small-gutter">
                            <div class="col-sm-6">
                                <label class="checkbox-inline"><input type="checkbox" value="" id="show_data_line" checked>Connector Line</label>
                            </div>
                            <div class="col-sm-5">
                                <label class="checkbox-inline"><input  type="checkbox" id="show_tooltip" value="" checked>Data Tooltips</label>
                            </div>
                        </div>

                        <BR>

                        <div class="row-small-gutter" style="margin-top:30px">
                            <div class="col-sm-12">
                                <label class="checkbox-inline" style="margin-right:5px"><input type="checkbox" value="" id="show_nonconvergant_fits">Show Non-Convergant Fits</label>
                            </div>
                            <div class="col-sm-12">
                                <label class="checkbox-inline" style="margin-right:5px"><input type="checkbox" value="" id="show_unconstrained_fits">Show Unconstrained Fits</label>



                            </div>

                        </div>

                            
                    </div>
                </div>
                <!-- Light curve options panel ends here -->  


                <!-- Catalog information start here -->        
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">4FGL Catalog Data</h3>
                    </div>
                    <div class="panel-body">

                        <div class="table-responsive">

                            <table class="table table-striped">
                              <thead>
                                <tr>
                                  <th>Source Information</th><th></th>
                                </tr>
                              </thead>
                              <tbody>
                                    <tr><td>Catalog Name: </td><td id="Source_Name" align="right" style="padding-right:5px"></td></tr>
                                    <tr><td>RA: </td><td id="RAJ2000" align="right" style="padding-right:18px"></td></tr>
                                    <tr><td>Dec: </td><td id="DEJ2000" align="right" style="padding-right:18px"></td></tr>
                                    <tr><td>Galactic l: </td><td id="GLON" align="right" style="padding-right:18px"></td></tr>
                                    <tr><td>Galactic b: </td><td id="GLAT" align="right" style="padding-right:18px"></td></tr>
                                    <tr><td>Variability Index: </td><td id="Variability_Index" align="right" style="padding-right:18px"></td></tr>
                              </tbody>
                            </table>  

                            <table class="table table-striped">
                              <thead>
                                <tr>
                                  <th>Flux Information</th><th></th>
                                </tr>
                              </thead>
                                <tbody>
                                    <tr><td>Photon Flux: </td><td id="Flux1000" align="right" style="padding-right:18px"></td></tr>     
                                    <tr><td>Energy_Flux: </td><td id="Energy_Flux100" align="right" style="padding-right:18px"></td></tr>   
                                    <tr><td>Average Significance: </td><td id="Signif_Avg" align="right" style="padding-right:18px"></td></tr>
                                </tbody>
                            </table>  

                            <table class="table table-striped">
                              <thead>
                                <tr>
                                  <th>Spectral Information</th><th></th>
                                </tr>
                              </thead>
                                <tbody>
                                    <tr><td>Spectral Type: </td><td id="SpectrumType" align="right" style="padding-right:18px"></td></tr>
                                    <tr id="SpectralIndex_row1"><td id="SpectralIndex_label1">Photon Index: </td><td id="SpectralIndex1" align="right" style="padding-right:18px"></td></tr>    
                                    <tr id="SpectralIndex_row2" style="display:none"><td id="SpectralIndex_label2">Photon Index: </td><td id="SpectralIndex2" align="right" style="padding-right:18px"></td></tr>                     
                  
                                </tbody>
                            </table>                    

                            <table class="table table-striped">
                              <thead>
                                <tr>
                                  <th>Associations</th><th></th>
                                </tr>
                              </thead>
                              <tbody>                       
                                    <!-- <tr><td>FAVA Association: </td><td td id="favasrc" align="right" style="padding-right:18px"></td></tr>      -->
                                    <tr><td>Classification: </td><td id="CLASS1" align="right" style="padding-right:18px"></td></tr>
                                    <tr><td>Association: </td><td td id="ASSOC1" align="right" style="padding-right:18px"></td></tr>
                                    <tr><td>Association (FGL): </td><td td id="ASSOC_FGL" align="right" style="padding-right:18px"></td></tr>
                                    <tr><td>Association (FHL): </td><td td id="ASSOC_FHL" align="right" style="padding-right:18px"></td></tr>
                              </tbody>
                            </table>  

                        </div>
                    </div>
                </div>
                <!-- Catalog information ends here -->     

    
                <!-- Download panel start here -->      
                <div id="DownloadPanel" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Data Download</h3>
                     </div>
                     <div class="panel-body" style="height:105px">

                        <BR>
                         <center>
                            <!-- <button id="Download" style="margin:5px 0px 0px 2px" id="Download" class="btn btn-default" title="Download data" rel="nofollow"> Download Data</button> -->

                            <div class="btn-group" role="group">
                                <button style="min-width:125px" class="btn btn-default dropdown-toggle data-download" type="button" data-toggle="dropdown">Select Format <span class="caret"></span></button>
                                <ul class="dropdown-menu data-download">
                                    <li id="download_csv"><a href="#">Download CSV</a></li>
                                    <li id="download_json"><a href="#">Download JSON</a></li>
                                    <li id="copy_api"><a href="#">Copy API Link</a></li>
                                </ul>
                            </div>
                        </center>

                    </div>
                </div>
                <!-- Download panel ends here -->
                                                                            
                <!-- Related resources start here -->      
                <div class="panel panel-default" style="height: 225px;">
                    <div class="panel-heading">
                        <h3 class="panel-title">Related Resources</h3>
                     </div>
                        <center>
                            <table class="table table-striped">
                            <!-- <table class="table"> -->
                              <tbody>           
                                    <tr><td><a href="https://fermi.gsfc.nasa.gov/ssc/">The Fermi Science Support Center</a></td></tr>
                                    <tr><td><a href="https://fermi.gsfc.nasa.gov/ssc/data/access/lat/FAVA/">Fermi All-Sky Variability Analysis (FAVA)</a></td></tr>           
                                    <tr><td><a href="https://fermi.gsfc.nasa.gov/ssc/data/analysis/scitools/">Fermi LAT & GBM Analysis Tutorials</a></td></tr>
                                    <tr><td><a href="https://fermi.gsfc.nasa.gov/ssc/data/access/">Fermi LAT & GBM Data Access</a></td></tr>
                                    <tr><td><a href="about.html">About the Light Curve Repository</a></td></tr>      
                              </tbody>
                            </table>  
                        </center>
                </div>
                <!-- Related resources ends here -->   

                <!-- Citation request start here -->    
                <div class="panel panel-default">   
                    <div class="panel-heading">
                        Please reference <a href="">Kocevski et al. 2021</a> for use of any results presented in the Fermi LAT Light Curve Repository.
                    </div>
                </div>
                <!--  Citation request ends here -->   

            
            </div>
            <!-- sidebar ends here -->

            <!-- Content starts here -->
            <div id="content" style="width:100%; max-width:1295px;">

                <!-- Photon flux light curve data panel start here -->  
                <div style="width:100%; max-width:100%; margin-left: 390px; padding-right:10px">
                    <div class="panel panel-default" style="height:550px">
                        <div class="panel-heading"><h3 class="panel-title">Light Curve Data</h3></div>
                        <div class="panel-body">

                            <div style="float:right; margin: 10px 20px 10px 0">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-default dropdown-toggle flux-type" type="button" data-toggle="dropdown">Photon Flux <span class="caret"></span></button>
                                    <ul class="dropdown-menu flux-type">
                                        <li id="y-axis" class="disabled"><a href="#">Y-Axis:</a></li>
                                        <li id="photon" class="active"><a href="javascript:void(0)">Photon Flux </a></li>
                                        <li id="energy"><a href="javascript:void(0)">Energy Flux </a></li>
                                    </ul>
                                </div>


                                <div class="btn-group" role="group">
                                    <button class="btn btn-default dropdown-toggle xaxis-type" type="button" data-toggle="dropdown">Date <span class="caret"></span></button>
                                    <ul class="dropdown-menu xaxis-type">
                                        <li id="x-axis" class="disabled"><a href="javascript:void(0)">X-Axis:</a></li>
                                        <li id="date" class="active"><a href="javascript:void(0)">Date</a></li>
                                        <li id="met"><a href="javascript:void(0)">MET</a></li>
                                        <li id="jd"><a href="javascript:void(0)">JD</a></li>
                                        <li id="mjd"><a href="javascript:void(0)">MJD</a></li>
                                    </ul>
                                </div>

                            </div>

                            <center>
                            <div class="plot_container" id="chart1_container">
                                <div id="chart1" style="width:100%; height:400px; padding-right:10px;position:relative"></div>
                            </div>
                            </center>


                        </div>  
                    </div>
                </div>
                <!-- Light curve data panel ends here -->

                <!-- Ancillary data panel start here -->    
                <div style="width:100%;max-width:100%; margin-left: 390px; padding-right:10px">
                    <div class="panel panel-default" style="height:550px">
                        <div class="panel-heading"><h3 class="panel-title">Ancillary Data</h3></div>
                        <div class="panel-body" style="height:150px">

                            <div style="float:right; margin: 10px 20px 10px 0">

                                <div class="btn-group" role="group">
                                    <button style="min-width:125px" class="btn btn-default dropdown-toggle ancillary-data" type="button" data-toggle="dropdown">Photon Index <span class="caret"></span></button>
                                    <ul class="dropdown-menu ancillary-data">
                                        <li id="y-axis" class="disabled"><a href="#">Y-Axis:</a></li>
                                        <li id="photon_index" class="active"><a href="#">Photon Index</a></li>
                                        <li id="ts"><a href="">TS</a></li>
                                        <li id="fit_convergance"><a href="">Fit Convergance</a></li>
                                        <li id="fit_tolerance"><a href="">Fit Tolerance</a></li>
                                        <li id="GAL"><a href="">GAL</a></li>
                                        <li id="EG"><a href="">EG</a></li>
                                        <li id="dlogl"><a href="">dlogL</a></li>
                                    </ul>
                                </div>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-default dropdown-toggle xaxis-type" type="button" data-toggle="dropdown">Date <span class="caret"></span></button>
                                    <ul class="dropdown-menu xaxis-type">
                                        <li id="x-axis" class="disabled"><a href="javascript:void(0)">X-Axis:</a></li>
                                        <li id="date2" class="active"><a href="javascript:void(0)">Date</a></li>
                                        <li id="met2"><a href="javascript:void(0)">MET</a></li>
                                        <li id="jd2"><a href="javascript:void(0)">JD</a></li>
                                        <li id="mjd2"><a href="javascript:void(0)">MJD</a></li>
                                    </ul>
                                </div>
                            </div>

                            <center>
                            <div class="plot_container" id="chart2_container">
                                <div id="chart2" style="width:100%; height:400px; padding-left:10px; padding-right:10px"></div>
                            </div>
                            </center>

                        </div>  
                    </div>
                </div>
                <!-- Ancillary data panel ends here -->

                <!-- Doughnut plots panels start here -->   
                <div class="container" style="width:100%; height: 425px; max-width:100%; margin-left: 390px; padding-right:10px">
                    <div class="row" style="margin-left:-30px; height: 425px;">

                                <div class="col-md-4">  
                                    <!-- Fit convergance panels start here -->  
                                    <div class="panel panel-default" style="width:408px; height:425px;">
                                        <div class="panel-heading"><h3 class="panel-title">Fit Convergance</h3></div>
                                        <div class="panel-body">
                                            <center>
                                                <!-- Detector distribution plot beings here -->
                                                <div id="canvas-holder1" style="width:90%; padding:10px 0 0 0px">
                                                    <canvas id="chart-area_1"></canvas>
                                                </div>
                                                <!-- Detector distribution plot ends here -->
                                            </center>
                                        </div>  
                                    </div>
                                    <!-- Fit convergance panels ends here -->   
                                </div>

                                <div class="col-md-4">
                                    <!-- Fit tolerance panels start here -->    
                                    <div class="panel panel-default" style="width:408px; height:425px;">
                                        <div class="panel-heading"><h3 class="panel-title">Fit Tolerance</h3></div>
                                        <div class="panel-body">
                                            <center>
                                                <!-- Detector distribution plot beings here -->
                                                <div id="canvas-holder2" style="width:90%; padding:10px 0 0 0px">
                                                    <canvas id="chart-area_2"></canvas>
                                                </div>
                                                <!-- Detector distribution plot ends here -->
                                            </center>
                                        </div>  
                                    </div>
                                    <!-- Fit tolerance panels ends here --> 
                                </div>

                                <div class="col-md-4"> 
                                    <!-- Detection distribution panels start here -->   
                                    <div class="panel panel-default" style="width:408px; height:425px;">
                                        <div class="panel-heading"><h3 class="panel-title">Detection Ratio</h3></div>
                                        <div class="panel-body">
                                            <center>
                                                <!-- Detector distribution plot beings here -->
                                                <div id="canvas-holder3" style="width:90%; padding:10px 0 0 0px">
                                                    <canvas id="chart-area_3"></canvas>
                                                </div>
                                                <!-- Detector distribution plot ends here -->
                                            </center>
                                        </div>  
                                    </div>
                                    <!-- Detection distribution panels ends here -->    
                                </div>

                    </div>
                </div>


                <!-- Photon flux light curve data panel start here -->  
                <div style="width:100%; max-width:100%; margin-left: 390px; margin-top:20px; padding-right:10px">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3 class="panel-title">Likelihood Fit Data Table</h3></div>
                        <div class="panel-body">
                            
                            <center>
                                <table class="table table-striped table-condensed table-bordered data-table" id="data_table" style="width:1000px;"></table>  
                            </center>

                        </div>  
                    </div>
                </div>
                <!-- Light curve data panel ends here -->


            </div>
            <!-- Content ends here -->

        <!-- Background container ends here -->
        </div>


        <!-- footer starts here --> 
        <div id="footer">
            <div id="footer-content">
            
                <p>
                    <hr>
                    Fermi LAT Light Curve Repository - Support Contact:<a href="mailto:daniel.kocevski@nasa.gov"> Daniel Kocevski</a>
                </p>

            </div>
        </div>
        <!-- footer ends here -->


    <!-- Main ends here -->
    </div>

    <!-- Magic word dialog modal view starts here -->
    <div id="magic_word_dialog" class="modal fade">
        <div class="modal-dialog" style="width:800px; margin: auto; margin-top:10%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="text-align: center;">Welcome to the Fermi LAT Light Curve Repository</h4>
                </div>

                <div class="modal-body center-block" style="text-align: center; height:200px; margin-top:5%; margin-bottom:5%">
                    <center>
                        <!-- <span style="font-size: 50px;" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> -->
                        <img width="75px" src="./img/spaceship.png">
                    </center>
                    
                    <BR>

                    This page is currently under developement.<BR>A public announcement will be made when the light curve data is available.<BR>Feedback on the light curve repository data portal is certainly welcomed!
                    
                    <BR>
                    <BR>
                
                    <form id="magic_word_form" name='MagicWordForm' style="display:none">
                        <input id="magic_word" type="text" class="input-small" placeholder="">
                        </form>

                </div> <!-- /.modal-body -->

                <div class="modal-footer">             
                    <div id="SaveButtonDiv" style="float: right;">
                        <button type="button" id="submitForm" class="btn btn-primary" style="width:60px; color:white;font-size: 12px;margin:10px">Ok</button>
                    </div>  
<!--                     <div style="float: right;"> 
                        <button type="button" id="dismissForm" class="btn btn-default" data-dismiss="modal" style="font-size: 12px;margin-top:10px">Close</button>
                    </div>  -->
                </div> <!-- /.modal-footer -->

            </div> <!-- /.modal-content -->
        </div> <!-- /.modal-dialog -->
    </div> <!-- /.modal -->  



    <!-- Magic word dialog modal view starts here -->
    <div id="exampleModalCenter" class="modal fade">
        <div class="modal-dialog" style="width:800px; margin: auto; margin-top:10%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="text-align: center;">Welcome to the Fermi LAT Light Curve Repository</h4>
                </div>

                <div class="modal-body center-block" style="text-align: center; height:200px; margin-top:10%">

                    
                    This page is currently under developement.<BR>Please enter the passphrase to view the draft page:
                    
                    <BR>
                    <BR>
               

                </div> <!-- /.modal-body -->

                <div class="modal-footer">             
                    <div style="float: right;"> 
                        <button type="button" id="dismissForm" class="btn btn-default" data-dismiss="modal" style="font-size: 12px;margin-top:10px">Close</button>
                    </div> 
                </div> <!-- /.modal-footer -->

            </div> <!-- /.modal-content -->
        </div> <!-- /.modal-dialog -->
    </div> <!-- /.modal -->  



	<!-- Info modal -->
<!-- 	<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        ...
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Save changes</button>
	      </div>
	    </div>
	  </div>
	</div> -->




    <!-- tippy -->
<!--     <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

    <script>
	    tippy('#ts_min_button', {
			theme: 'light',
        	content: 'Set the minimum detction significance. Time intervals with a detection signifiance below this level will result in the reporting of an upper limit'
    	});
	</script> -->

</body>


