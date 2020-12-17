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

	/*	.modal-open .container-fluid, .modal-open  .container {
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
		
		var chart1;
		var chart2;

		var data;
		var flux;
		var flux_error;
		var flux_upper_limits;
		var photon_index;
		var photon_index_error;
		var ancillary_data;

		// Setting global light curve defaults 
		var cadence = 'weekly'
    	var flux_type = 'photon'
    	var ancillary_type = 'photon_index'
    	var ancillary_data_label = 'Photon Index'

    	// var chartType = 'linear';
    	var chartType = 'datetime';
    	var xtitle = 'Date (UTC)'

		var counts = {};
		var fit_tolerance;

		var met_array = new Array;

		var flux_type_label
		var flux_type

		// var xaxis_type_label;

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
			if (MET>157766400) {MET=MET-1}	// 2005 leap second
			if (MET>252460801) {MET=MET-1}	// 2008 leap second
			if (MET>362793601) {MET=MET-1}	// 2012 leap second
			if (MET>457401601) {MET=MET-1}	// 2015 leap second
			if (MET>504921601) {MET=MET-1}	// 2016 leap second

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
			// var day = date.getDate() - 1;	// Subtracting a day to make results match NASA's xtime results
			var day = date.getUTCDate() ;	// Subtracting a day to make results match NASA's xtime results
			var year = date.getUTCFullYear();
			var month = date.getUTCMonth() + 1;	// Adding a month because the javascript date object starts with month 0?

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

			// Highcharts.getJSON('./data/flux_nan.json', function (data) {
			// Highcharts.getJSON('./data/Test3.json', function (data) {

			chart1 = new Highcharts.chart('chart1', {
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
			            },
			            tooltip: {
			            	distance: 25,
							headerFormat: '<b>{series.name}</b><br>',
							xDateFormat: '%Y-%m-%d',
							// pointFormat: pointFormat_chart1,

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
			        data: flux,
					zIndex: 100,
			        lineWidth: 0.5,
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

						}


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
			    //         tooltip: {
			    //         	distance: 25,
							// // headerFormat: '<b>{series.name}</b><br>',
							// // pointFormat: 'MET = {point.x}<br>Value = {point.y}',

							// headerFormat: '<b>{series.name}</b><br>',
							// xDateFormat: '%Y-%m-%d',
							// pointFormat: pointFormat,
							// valueDecimals: 2,

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
			        data: ancillary_data,
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
					data: ancillary_data_error,
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
            	chart1.yAxis[0].setTitle({ text: "Photon Flux ( ph cm<sup>-2</sup> s<sup>-1</sup> )" });
            } else {
            	chart1.yAxis[0].setTitle({ text: "Energy Flux ( MeV cm<sup>-2</sup> s<sup>-1</sup> )" });
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

			fillDoughnutPlots()
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


			fit_tolerance_keys = [0.001, 0.01, 0.1, 1]
			fit_tolerance_distribution = [0,0,0,0]

			fit_convergance_keys = [0, 1, 2, 102]
			fit_convergance_distribution = [0,0,0,0]

			ts_keys = ['Detections', 'Upper Limits']
			ts_distribution = [0,0]

			for (var i = 0; i < ts.length; i++) {

				if (ts[i] >= 9) {
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

			// console.log(fit_tolerance_values)


			// counts1 = {};
			// counts2 = {};

			// for (var i = 0; i < fit_tolerance.length; i++) {
			// 	var value1 = fit_tolerance[i];
			// 	var value2 = fit_convergance[i];
			// 	counts1[value1] = counts1[value1] ? counts1[value1] + 1 : 1;
			// 	counts2[value2] = counts2[value2] ? counts2[value2] + 1 : 1;
			// }			

			// var fit_tolerance_keys = Object.keys(counts1)
			// var fit_convergance_keys = Object.keys(counts2)

			// for (var i = 0; i < fit_tolerance_keys.length; i++) {
			// var fit_tolerance_values = Object.values(counts1)
			// var fit_convergance_values = Object.values(counts2)


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

			// Hide the highcharts credit (this information will appear with other credits in the about section)
			$('.highcharts-credits')[0].innerHTML=''
			$('.highcharts-credits')[1].innerHTML=''

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

					console.log(key + ": " + value );

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

					var PL_Index = String(parseFloat(data[0]['PL_Index']).toPrecision(2))
					var Unc_PL_Index = String(parseFloat(data[0]['Unc_PL_Index']).toPrecision(2)) 

					document.getElementById('SpectralIndex_label1').innerHTML = 'Photon Index &Gamma;:'
					document.getElementById('SpectralIndex1').innerHTML = PL_Index + ' &plusmn; ' + Unc_PL_Index

				} else if  (spectrumType.includes('LogParabola')) {

					LP_Index = String(parseFloat(data[0]['LP_Index']).toPrecision(2))
					Unc_LP_Index = String(parseFloat(data[0]['Unc_LP_Index']).toPrecision(2)) 

					LP_beta = String(parseFloat(data[0]['LP_beta']).toPrecision(2))
					Unc_LP_beta = String(parseFloat(data[0]['Unc_LP_beta']).toPrecision(2)) 

				  	document.getElementById('SpectralIndex_label1').innerHTML = 'Photon Index &alpha;:'
				  	document.getElementById('SpectralIndex_label2').innerHTML = 'Photon Index &beta;:'
					document.getElementById('SpectralIndex1').innerHTML = LP_Index + ' &plusmn; ' + Unc_LP_Index
					document.getElementById('SpectralIndex2').innerHTML = LP_beta + ' &plusmn; ' + Unc_LP_beta
					$('#SpectralIndex_row2').show()

				} else if  (spectrumType.includes('PLSuperExpCutoff')) {

					PLEC_Index = String(parseFloat(data[0]['PLEC_Index']).toPrecision(2))
					Unc_PLEC_Index = String(parseFloat(data[0]['Unc_PLEC_Index']).toPrecision(2)) 

				  	document.getElementById('SpectralIndex_label1').innerHTML = 'Photon Index &Gamma;:'
					document.getElementById('SpectralIndex1').innerHTML = PLEC_Index + ' &plusmn; ' + Unc_PLEC_Index
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

	        var URL = "queryDB.php?typeOfRequest=lightCurveData&source_name=" + source_name_urlEncoded + '&cadence=' + cadence + '&flux_type=' + flux_type  + "&magicWord=" + magic_word_urlEncoded;

	        console.log(URL);

	        // Get the data
			$.ajax({url: URL, success: function(responseText){

				// Parse the resulting json file
                // data = JSON.parse(responseText);

                // Get the data
                // var data;
                try {
                	data = JSON.parse(responseText);
                } catch (error) {
                    console.log('Error parsing query response.')
                    $('#main').hide()
                    return
                }


                // Extact the flux information
                flux = data['flux']
                flux_error = data['flux_error']
                flux_upper_limits = data['flux_upper_limits']                 

                // Make an array of the METs
                met_array = []
                for (var i in data['ts']) {
                	met_array.push(data['ts'][i][0])
                }

                // Correct the flux error precision
				for (i = 0; i < flux_error.length; i++) { 
					flux_error[i][1] = parseFloat(flux_error[i][1].toPrecision(3))
					flux_error[i][2] = parseFloat(flux_error[i][2].toPrecision(3))
				}

                ancillary_data = data[ancillary_type]

                var ancillary_data_label = $('.dropdown-menu.ancillary-data li.active') 
                if (ancillary_type.includes('photon_index')) {
                	ancillary_data_error = data['photon_index_error']
                } else {
					ancillary_data_error = null;
                }

                // Update the side table with the results
                if (xtitle.includes('Mission Elapsed Time (seconds)') === false) {

					$.each( data, function( key, value ) {

						for (i = 0; i < data[key].length; i++) { 

							// Extract the current MET
							var met = data[key][i][0]

							// Convert the MET to a date
							var date
							if (xtitle.includes('Date (UTC)')) {
								date = MET2date(met)
								date = Date.parse(date)
							} else if (xtitle.includes('Modified Julian Date')) {
								date = MET2MJD(met)
							} else {
								date = MET2JD(met)
							}

							// Place the new date back into the data array
							data[key][i][0] = date
						}

					})
				}

				createScatterPlot()

				fillTable(data)

                // Check if data was successfully retrieved. If the passphrase wasn't set before, set it to the supplied magic_word
                if (data['ts'].length != 0) {
                    if (passphrase == null) {
                        setCookieData('passphrase', magic_word_submitted, 1)  // Cookie expires in 1 day
                    }
                }				

			}});
		}

        // Fill the primary table
        function fillTable(data) {

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

                var met = met_array[i]
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

                if (ts >= 9) {

                	var flux = data['flux'][j_detections][1].toExponential(2)
                	var flux_error = flux - data['flux_error'][j_detections][1]
                	// var flux_error_string = flux_error.toExponential(2)
                	flux_string = flux + ' &#177; ' + flux_error.toExponential(2)

                	var photon_index = data['photon_index'][j_detections][1]
                	var photon_index_error = photon_index - data['photon_index_error'][j_detections][1]
                	photon_index_string = photon_index + ' &#177; ' + photon_index_error.toPrecision(2)

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
            console.log('done.')
            document.querySelector('#data_table').innerHTML = row.join(''); 

            // // Hide the spinner
            // $("#loadingSpinnerImage").hide();

            // Show the table
            $("#dataTable_4FGL").show();
        }

        // Check for an existing cookie
        function checkCookie() {
            
            // Check if the cookie contains the passphrase data
            passphrase = getCookieData("passphrase");

            console.log("Cookie stored passphrase = " + passphrase)

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
			// 	$('#magic_word_dialog').modal('show');
			// });	
				

		    $("#submitForm").on('click', function() {

		        $("#magic_word_form").submit();

		    });

		    $("#dismissForm").on('click', function() {

                $('#main').hide()
		        
		    });

		    $("#magic_word_form").on("submit", function(e) {

		        // Get the form data
		        magic_word_submitted =document.forms["magic_word_form"]["magic_word"].value;

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
				console.log($(this))
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

			  //   	pointFormat_x1 = 'MET = {point.x}'
					// pointFormat_chart1 = pointFormat_x1 + '<br>' + pointFormat_y1

    			} else if (xaxis_type_label.includes('MJD')) {

					chartType = 'linear';
    				xtitle = 'Modified Julian Date'

			  //   	pointFormat_x1 = 'MJD = {point.x}'
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

            });

            // Ancillary data plot selector
            $('.dropdown-menu.ancillary-data').on('click', 'li', function() {

                $('.dropdown-menu.ancillary-data li.active').removeClass('active');
                $(this).addClass('active');

                // Get the label for the user selected projection and update the dropdown button
                var ancillary_data_label = $(this).find('a')[0].innerHTML
                $('.dropdown-toggle.ancillary-data')[0].innerHTML =  ancillary_data_label + ' <span class="caret"></span>'    


                console.log('ancillary_data_label = ' + ancillary_data_label)
                // Redefine the flux type to be displayed in the light curve plot
                
                ancillary_type = $(this).attr("id");

                // Define the label for the second plot
                // var ancillary_data_label = $('.dropdown-menu.ancillary-data li.active')

                // Hide the chart and remake the plot
				// $('#chart2').addClass('hidden', function() {

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
						data: data[ancillary_type],
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
							data: data['photon_index_error'],
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

				<!-- Position information start here -->		
				<div class="panel panel-default">
					<div class="panel-heading">
				        <h3 class="panel-title">Catalog Data</h3>
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
	   								<!-- <tr><td>FAVA Association: </td><td td id="favasrc" align="right" style="padding-right:18px"></td></tr>		 -->
									<tr><td>Classification: </td><td id="CLASS1" align="right" style="padding-right:18px"></td></tr>
									<tr><td>Association: </td><td td id="ASSOC1" align="right" style="padding-right:18px"></td></tr>
									<tr><td>Association (FGL): </td><td td id="ASSOC_FGL" align="right" style="padding-right:18px"></td></tr>
									<tr><td>Association (FHL): </td><td td id="ASSOC_FHL" align="right" style="padding-right:18px"></td></tr>
				              </tbody>
				            </table>  

	 				    </div>
					</div>
				</div>
				<!-- Position information ends here -->		

		
				<!-- Download panel start here -->		
				<div id="DownloadPanel" class="panel panel-default">
					<div class="panel-heading">
				        <h3 class="panel-title">Download</h3>
				     </div>
				     <div class="panel-body">

				     <center>
						<button id="Download" style="margin:5px 0px 0px 2px" id="Download" class="btn btn-default" title="Download data" rel="nofollow"> Download Data</button>
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
	                    Please reference <a href="">Kocevski et al. 2020</a> for use of any results presented in the Fermi LAT Light Curve Repository.
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

								<div id="cadence_selector1" class="btn-group">
									<button id="daily" type="button" class="btn btn-default ">3 day</button>
									<button id="weekly" type="button" class="btn btn-primary active">1 week</button>
									<button id="monthly" type="button" class="btn btn-default">1 month</button>
								</div>

								<div class="btn-group" role="group">
								    <button class="btn btn-default dropdown-toggle flux-type" type="button" data-toggle="dropdown">Photon Flux <span class="caret"></span></button>
								    <ul class="dropdown-menu flux-type">
                                		<li id="photon" class="active"><a href="javascript:void(0)">Photon Flux</a></li>
                                		<li id="energy"><a href="javascript:void(0)">Energy Flux</a></li>
									</ul>
								</div>

								<div class="btn-group" role="group">
								    <button class="btn btn-default dropdown-toggle xaxis-type" type="button" data-toggle="dropdown">Date <span class="caret"></span></button>
								    <ul class="dropdown-menu xaxis-type">
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
					    <div class="panel-body">

							<div style="float:right; margin: 10px 20px 10px 0">

								<div class="btn-group" role="group">
								    <button style="min-width:125px" class="btn btn-default dropdown-toggle ancillary-data" type="button" data-toggle="dropdown">Photon Index <span class="caret"></span></button>
								    <ul class="dropdown-menu ancillary-data">
                                		<li id="photon_index" class="active"><a href="#">Photon Index</a></li>
                                		<li id="ts"><a href="">TS</a></li>
                                		<li id="fit_convergance"><a href="">Convergance</a></li>
                                		<li id="fit_tolerance"><a href="">Fit Tolerance</a></li>
									</ul>
								</div>
								<div class="btn-group" role="group">
								    <button class="btn btn-default dropdown-toggle xaxis-type" type="button" data-toggle="dropdown">Date <span class="caret"></span></button>
								    <ul class="dropdown-menu xaxis-type">
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
				<div class="container" style="width:100%; max-width:100%; margin-left: 390px; padding-right:10px">
				    <div class="row" style="margin-left:-30px">
<!-- 				        <div class="col-md-12">
				            <div class="row"> -->

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
<!-- 
				            </div>
				        </div> -->
				    </div>
				</div>


				<!-- Photon flux light curve data panel start here -->	
			    <div style="width:100%; max-width:100%; margin-left: 390px; padding-right:10px">
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

                <div class="modal-body center-block" style="text-align: center; height:200px; margin-top:10%">

                    
                    This page is currently under developement.<BR>Please enter the passphrase to view the draft page:
                    
                    <BR>
                    <BR>
                
                    <form id="magic_word_form" name='MagicWordForm'>
                        <input id="magic_word" type="text" class="input-small" placeholder="">
                        </form>

                </div> <!-- /.modal-body -->

                <div class="modal-footer">             
                    <div id="SaveButtonDiv" style="float: right;">
                        <button type="button" id="submitForm" class="btn btn-primary" style="color:white;font-size: 12px;margin:10px">Submit</button>
                    </div>  
                    <div style="float: right;"> 
                        <button type="button" id="dismissForm" class="btn btn-default" data-dismiss="modal" style="font-size: 12px;margin-top:10px">Close</button>
                    </div> 
                </div> <!-- /.modal-footer -->

            </div> <!-- /.modal-content -->
        </div> <!-- /.modal-dialog -->
    </div> <!-- /.modal -->  






</body>


