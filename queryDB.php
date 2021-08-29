<?php

    $magicWord = '130427A';

    define('MYSQL_ASSOC',MYSQLI_ASSOC);

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
    function AngularDistance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {

        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        $angle_degrees = $angle * (180/pi());

        return $angle_degrees;
    }

    if ((isset($_GET['typeOfRequest']) == false) and (isset($_GET['magicWord']) == false)) {

        echo '<BR><B>Usage Examples:</B><BR>';
        echo 'queryDB.php?typeOfRequest=MapData&ra=0&dec=0&radius=12<BR>';
        return;
    } else if ((isset($_GET['typeOfRequest']) == true) and (isset($_GET['magicWord']) == false)) {
        echo "You didn't say the magic word"; 
        return;
    } else {

        // Determine the type of data requested
        $typeOfRequest = $_GET['typeOfRequest'];
        $magicWord_submitted = $_GET['magicWord'];

        if ($magicWord_submitted != $magicWord) {
            echo null;
            return;
        }

        if (isset($_GET['catalog'])) { 
            $catalog = $_GET['catalog'];
        }
    }

    // Return basic information on all sources to be displayed in the map
    if ($typeOfRequest === 'MapData') { 

       if (isset($_GET['Class'])) { 
            $ClassValues = $_GET['Class']; 
            $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
        } else { 
            $CLASSTYPE = '';
        }

        if (isset($_GET['Name'])) { 

            $SearchQuery = $_GET['Name'];
            $SearchQuery = "'%" . $SearchQuery . "%'";

            if (isset($_GET['Class'])) { 
                $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
            } else {
                $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
            }

        } else { 
            $NAME = '';
        }

        if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
            $raROI = $_GET['ra']; 
            $decROI = $_GET['dec']; 
            $radius = $_GET['radius']; 
        }
        
        $queryStatement = 'SELECT Source_Name, ASSOC1, Type, RAJ2000, DEJ2000, GLON, GLAT, Size FROM Catalog ' . $CLASSTYPE . $NAME ;

        $db = new SQLite3('./db/gll_psc_v14.db');
        $results = $db->query($queryStatement);

        $data = array();

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

            if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

                // Get the ra and dec of each source
                $raSource = $row['RAJ2000'];
                $decSource = $row['DEJ2000'];

                // Find the distance to the user specified coordinates
                $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

                if ($distance < $radius) {
                    $data[] = $row;
                }

            } else {

                $data[] = $row;

            }

        }  

        echo json_encode($data);
    } 

    // Return detailed information on a limited number of sources to be displayed in the data table
    if ($typeOfRequest === 'TableData' || $typeOfRequest === 'ReloadTableData') { 

        if (isset($_GET['lines'])) { 
            $lines = $_GET['lines'];
            $LIMIT = ' LIMIT ' . $lines;
        } else { 
            $lines = 100; 
            $LIMIT = ' LIMIT ' . $lines;
        }

        if (isset($_GET['offset'])) { 
            $offset = $_GET['offset']; 
            $OFFSET = ' OFFSET ' . $offset;
        } else { 
            $offset = 0; 
            $OFFSET = ' OFFSET ' . $offset;
        }

        if (isset($_GET['Class'])) { 
            $ClassValues = $_GET['Class']; 
            $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
        } else { 
            $CLASSTYPE = '';
        }

        if (isset($_GET['Name'])) { 

            $SearchQuery = $_GET['Name'];
            $SearchQuery = "'%" . $SearchQuery . "%'";

            if (isset($_GET['Class'])) { 
                $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
            } else {
                $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
            }

        } else { 
            $NAME = '';
        }


        if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
            $raROI = $_GET['ra']; 
            $decROI = $_GET['dec']; 
            $radius = $_GET['radius']; 
        }

        $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux_Density, Unc_Flux_Density, Flux1000, Unc_Flux1000, SpectrumType, Spectral_Index, Unc_Spectral_Index, Variability_Index, CLASS1, TEVCAT_FLAG, ASSOC_TEV, Flags, Size, Type FROM Catalog ' . $CLASSTYPE . $NAME . $LIMIT . $OFFSET ;
        // echo $queryStatement;
        // echo "<BR><BR>";

        $db = new SQLite3('./db/gll_psc_v14.db');
        $results = $db->query($queryStatement);

        $data = array();
        $features = array();
        $properties = array();

        $data['type'] = "FeatureCollection";
        $features["id"] = $row['3FGL'];
        $data['features'] = $features;

        // while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

        //     if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

        //         // Get the ra and dec of each source
        //         $raSource = $row['RAJ2000'];
        //         $decSource = $row['DEJ2000'];

        //         // Find the distance to the user specified coordinates
        //         $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

        //         if ($distance < $radius) {
        //             $data[] = $row;
        //         }

        //     } else {

        //         $data[] = $row;

        //     }
        // }  

        echo json_encode($data);
    } 

    if ($typeOfRequest === 'ROISearchCelestial') { 

        if (isset($_GET['ra'])) { 
            $raROI = $_GET['ra']; 
        } 
        if (isset($_GET['dec'])) { 
            $decROI = $_GET['dec']; 
        } 
        if (isset($_GET['radius'])) { 
            $radius = $_GET['radius']; 
        } 

        if (isset($_GET['Class'])) { 
            $ClassValues = $_GET['Class']; 
            $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
        } else { 
            $CLASSTYPE = '';
        }

        if (isset($_GET['Name'])) { 

            $SearchQuery = $_GET['Name'];
            $SearchQuery = "'%" . $SearchQuery . "%'";

            if (isset($_GET['Class'])) { 
                $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
            } else {
                $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
            }

        } else { 
            $NAME = '';
        }

        $db = new SQLite3('./db/gll_psc_v14.db');
        $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux_Density, Unc_Flux_Density, Flux1000, Unc_Flux1000, SpectrumType, Spectral_Index, Unc_Spectral_Index, Variability_Index, CLASS1, TEVCAT_FLAG, ASSOC_TEV, Flags, Size, Type FROM Catalog ' . $CLASSTYPE . $NAME;
        $results = $db->query($queryStatement);

        $data = array();

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

            // Get the ra and dec of each source
            $raSource = $row['RAJ2000'];
            $decSource = $row['DEJ2000'];

            // Find the distance to the user specified coordinates
            $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

            if ($distance < $radius) {
                $data[] = $row;
            }

            // $data[] = $row;


        }  

        echo json_encode($data);
    } 

    // Return detailed information on a limited number of sources to be displayed in the data table
    if ($typeOfRequest === 'd3') { 

        if (isset($_GET['lines'])) { 
            $lines = $_GET['lines'];
            $LIMIT = ' LIMIT ' . $lines;
        } else { 
            $lines = 100; 
            $LIMIT = ' LIMIT ' . $lines;
        }

        if (isset($_GET['offset'])) { 
            $offset = $_GET['offset']; 
            $OFFSET = ' OFFSET ' . $offset;
        } else { 
            $offset = 0; 
            $OFFSET = ' OFFSET ' . $offset;
        }

        if (isset($_GET['Class'])) { 
            $ClassValues = $_GET['Class']; 
            $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
        } else { 
            $CLASSTYPE = '';
        }

        if (isset($_GET['Name'])) { 

            $SearchQuery = $_GET['Name'];
            $SearchQuery = "'%" . $SearchQuery . "%'";

            if (isset($_GET['Class'])) { 
                $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
            } else {
                $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
            }

        } else { 
            $NAME = '';
        }

        if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
            $raROI = $_GET['ra']; 
            $decROI = $_GET['dec']; 
            $radius = $_GET['radius']; 
        }

        // $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux_Density, Unc_Flux_Density, Flux1000, Unc_Flux1000, SpectrumType, Spectral_Index, Unc_Spectral_Index, Variability_Index, CLASS1, TEVCAT_FLAG, ASSOC_TEV, Flags, Size, Type FROM Catalog ' . $CLASSTYPE . $NAME . $LIMIT . $OFFSET ;

        $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux_Density, Unc_Flux_Density, Flux1000, Unc_Flux1000, SpectrumType, Spectral_Index, Unc_Spectral_Index, Variability_Index, CLASS1, TEVCAT_FLAG, ASSOC_TEV, Flags, Size, Type FROM Catalog ';

        // echo $queryStatement;
        // echo "<BR><BR>";

        $db = new SQLite3('./db/gll_psc_v14.db');
        $results = $db->query($queryStatement);

        $data = array();
        $data['type'] = "FeatureCollection";

        $features = array();
        
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

            $feature = array();
            $feature["type"] = "Feature";
            $feature["id"] = "3FGL";

            $properties = array();
            $properties["Source_Name"] = $row["Source_Name"];          
            $properties["Flux1000"] = $row["Flux1000"];
            $properties["Signif_Avg"] = $row["Signif_Avg"];
            $properties["CLASS1"] = $row["CLASS1"];
            $properties["ASSOC1"] = $row["ASSOC1"];

            $geometry = array();
            $geometry["type"] = "Point";

            $coordinates_equatorial = array();
            $coordinates_equatorial[] = $row["RAJ2000"];
            $coordinates_equatorial[] = $row["DEJ2000"];

            $coordinates_galactic = array();
            $coordinates_galactic[] = $row["GLON"];
            $coordinates_galactic[] = $row["GLAT"];   

            $geometry["coordinates_equatorial"] = $coordinates_equatorial;
            $geometry["coordinates_galactic"] = $coordinates_galactic;

            $feature["properties"] =  $properties;
            $feature["geometry"] =  $geometry;

            $features[] = $feature;

        }

            $data['features'] = $features;


            // echo "var jsonSN = ";
            echo json_encode($data);
    } 

    // Return detailed information on a limited number of sources to be displayed in the data table
    if ($typeOfRequest === 'SourceList') { 

        if ($catalog === '3FGL') {

           if (isset($_GET['Class'])) { 
                $ClassValues = $_GET['Class']; 
                $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
            } else { 
                $CLASSTYPE = '';
            }

            if (isset($_GET['keyword'])) { 
                $SearchQuery = $_GET['keyword'];
                $SearchQuery = "'%" . $SearchQuery . "%'";

                if (isset($_GET['Class'])) { 
                    $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
                } else {
                    $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
                }

            } else { 
                $NAME = '';
            }

            if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
                $raROI = $_GET['ra']; 
                $decROI = $_GET['dec']; 
                $radius = $_GET['radius']; 
            }

            // Construct the query statement 
            $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux1000, Unc_Flux1000, Energy_Flux100, Unc_Energy_Flux100, SpectrumType, Spectral_Index, Variability_Index, CLASS1 FROM Catalog ' . $CLASSTYPE . $NAME ;

            // Add the order by statement
            $queryStatement = $queryStatement . ' order by RAJ2000;';

            // Open the database
            $db = new SQLite3('./db/gll_psc_v14.db');

            // Query the database
            $results = $db->query($queryStatement);

            // Create an array to store the results
            $data = array();

            // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

                if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

                    // Get the ra and dec of each source
                    $raSource = $row['RAJ2000'];
                    $decSource = $row['DEJ2000'];

                    // Find the distance to the user specified coordinates
                    $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

                    if ($distance < $radius) {
                        $data[] = $row;
                    }

                } else {

                    $data[] = $row;

                }

            }  

            // Encode the PHP associative array into a JSON associative array
            echo json_encode($data);
        }

        if ($catalog === '4FGL') {

           if (isset($_GET['Class'])) { 
                $ClassValues = $_GET['Class']; 
                $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
            } else { 
                $CLASSTYPE = '';
            }

            if (isset($_GET['keyword'])) { 
                $SearchQuery = $_GET['keyword'];
                $SearchQuery = "'%" . $SearchQuery . "%'";

                if (isset($_GET['Class'])) { 
                    $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
                } else {
                    $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
                }

            } else { 
                $NAME = '';
            }

            if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
                $raROI = $_GET['ra']; 
                $decROI = $_GET['dec']; 
                $radius = $_GET['radius']; 
            }

            // Construct the query statement 
            $queryStatement = 'SELECT Source_Name, ASSOC_FGL, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux1000, Unc_Flux1000, Energy_Flux100, Unc_Energy_Flux100, SpectrumType, PL_Index, LP_Index, PLEC_Index, Variability_Index, CLASS1 FROM Catalog_4FGL ' . $CLASSTYPE . $NAME ;

            // Add the order by statement
            $queryStatement = $queryStatement . ' order by RAJ2000;';

            // Open the database
            // $db = new SQLite3('./db/gll_psc_v21.db');
            $db = new SQLite3('./db/gll_psc_v27.db');

            // Query the database
            $results = $db->query($queryStatement);

            // Create an array to store the results
            $data = array();

            // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

                if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

                    // Get the ra and dec of each source
                    $raSource = $row['RAJ2000'];
                    $decSource = $row['DEJ2000'];

                    // Find the distance to the user specified coordinates
                    $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

                    if ($distance < $radius) {
                        $data[] = $row;
                    }

                } else {

                    $data[] = $row;

                }

            }  

            // Encode the PHP associative array into a JSON associative array
            echo json_encode($data);
        }

        if ($catalog === '2FLGC') {

            if (isset($_GET['keyword'])) { 
                $SearchQuery = $_GET['keyword'];
                $SearchQuery = "'%" . $SearchQuery . "%'";
                $NAME = "WHERE GCNNAME like " . $SearchQuery . " OR GRBNAME like " . $SearchQuery . " COLLATE NOCASE";

            } else { 
                $NAME = '';
            }

            if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
                $raROI = $_GET['ra']; 
                $decROI = $_GET['dec']; 
                $radius = $_GET['radius']; 
            }

            // Construct the query statement 
            $queryStatement = 'SELECT GCNNAME, GRBNAME, GRBDATE, GRBMET, RA, DEC, ERR, GBMT90, ARR, LIKE_BEST_TS_GRB, LIKE_BEST_FLUX_ENE, LIKE_BEST_FLUENCE_ENE FROM Catalog_2FLGC '. $NAME ;

            // Add the order by statement
            $queryStatement = $queryStatement . ' order by GRBMET;';

            // Open the database
            $db = new SQLite3('./db/gll_2flgc.db');

            // Query the database
            $results = $db->query($queryStatement);

            // Create an array to store the results
            $data = array();

            // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

                if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

                    // Get the ra and dec of each source
                    $raSource = $row['RA'];
                    $decSource = $row['DEC'];

                    // Find the distance to the user specified coordinates
                    $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

                    if ($distance < $radius) {
                        $data[] = $row;
                    }

                } else {

                    $data[] = $row;

                }

            }  

            // Encode the PHP associative array into a JSON associative array
            echo json_encode($data);
        }

        if ($catalog === 'IceCube') {

            if (isset($_GET['keyword'])) { 
                $SearchQuery = $_GET['keyword'];
                $SearchQuery = "'%" . $SearchQuery . "%'";
                $NAME = "WHERE EVENT_NUM like " . $SearchQuery . " OR COMMENT like " . $SearchQuery . " COLLATE NOCASE";

            } else { 
                $NAME = '';
            }

            if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
                $raROI = $_GET['ra']; 
                $decROI = $_GET['dec']; 
                $radius = $_GET['radius']; 
            }

            // Construct the query statement 
            $queryStatement = 'SELECT NOTICE_TYPE, EVENT_NUM, DISCOVERY_DATE, DISCOVERY_DATE_TJD, DISCOVERY_TIME, SRC_RA, SRC_DEC, SRC_ERROR, ENERGY, FAR, NOTICE_TYPE, URL FROM ICeCubeEvents '. $NAME ;

            // Add the order by statement
            $queryStatement = $queryStatement . ' order by DISCOVERY_DATE_TJD DESC;';

            // Open the database
            $db = new SQLite3('./db/IceCubeGCNs.db');

            // Query the database
            $results = $db->query($queryStatement);

            // Create an array to store the results
            $data = array();

            // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

                if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

                    // Get the ra and dec of each source
                    $raSource = $row['RA'];
                    $decSource = $row['DEC'];

                    // Find the distance to the user specified coordinates
                    $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

                    if ($distance < $radius) {
                        $data[] = $row;
                    }

                } else {

                    $data[] = $row;

                }

            }  

            // Encode the PHP associative array into a JSON associative array
            echo json_encode($data);

            // $test = '<h2>Hello World</h2>';
            // echo $test;
        }
    } 

    // Return light curve data for one source
    if ($typeOfRequest === 'lightCurveData') { 

        // Extract the url parameters
        if (isset($_GET['source_name']) && (isset($_GET['cadence'])) && (isset($_GET['flux_type']))) { 
            $source_name = $_GET['source_name']; 
            $cadence = $_GET['cadence']; 
            $flux_type = $_GET['flux_type']; 
            $index_type = $_GET['index_type']; 
            $ts_min = floatval($_GET['ts_min']);

            // if ($ts_min === 'ts4') {
            //     $ts_min = 4
            // } else if ($ts_min === 'ts3') {
            //     $ts_min = 3
            // } else if ($ts_min === 'ts2') {
            //     $ts_min = 2
            // } else if ($ts_min === 'ts1') {
            //     $ts_min = 1
            // }

            // $source_name = "'4FGL J0237.8+2848'";
            // $index_type = 'free';

        } else {

            echo '<BR><B>Usage Examples:</B><BR>';
            echo 'queryDB.php?typeOfRequest=LightCurveData&source=&cadence=daily&cadence=photon';

            return;
        }

        // $source_name = "'4FGL J0237.8+2848'";
        // $cadence = "'daily'";
        // $flux_type = "energy";

        // Construct the query statement
        // $queryStatement = 'SELECT tmin, tmax, ts, ' . $flux_type . '_flux, ' . $flux_type . '_flux_error, ' . $flux_type . '_flux_upper_limit, photon_index, photon_index_error, photon_index_alpha, photon_index_alpha_error, fit_tolerance, return_code, bin_id FROM lightcurve_data WHERE source_name == \'' . $source_name . '\' AND cadence == \'' . $cadence . '\' ORDER BY tmin ASC';

        if ($index_type === 'fixed') {

            $queryStatement = 'SELECT tmin, tmax, ts, ' . $flux_type . '_flux, ' . $flux_type . '_flux_error, ' . $flux_type . '_flux_upper_limit, photon_index, photon_index_error, fit_tolerance, return_code, dlogl, EG, GAL, bin_id FROM lightcurve_data_v2 WHERE source_name == \'' . $source_name . '\' AND cadence == \'' . $cadence . '\' ORDER BY tmin ASC';


            if (isset($_GET['verbose'])) {
                echo  $queryStatement;
                echo "<BR><BR>";
            }

            // Establish the database connection
            // $db = new SQLite3('./db/lc_repository.db');
            $db = new SQLite3('./db/lc_repository_v2.db');
            $results = $db->query($queryStatement);

            // Create the data array to store the values
            $data = array();
            $data['ts'] = array();
            $data['flux'] = array();
            $data['flux_upper_limits'] = array();
            $data['flux_error'] = array();
            $data['photon_index'] = array();
            $data['photon_index_error'] = array();           
            $data['fit_tolerance'] = array();
            $data['fit_convergance'] = array();
            $data['dlogl'] = array();
            $data['EG'] = array();
            $data['GAL'] = array();
            $data['bin_id'] = array();

            // // Construct the flux keyword
            $flux_key = $flux_type . '_flux';
            $flux_error_key = $flux_type . '_flux_error';
            $flux_upper_limit_key = $flux_type . '_flux_upper_limit';

            // Retrieve the data 
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

                $met = (intval($row['tmin']) + intval($row['tmax']))/2.0;

                // TS
                if (empty($row['ts']) == FALSE) {
                    $ts = sprintf("%0.2f", $row['ts']);

                    array_push($data['ts'], array($met, $ts));
                }

                // Photon flux
                if ((empty($row['ts']) == FALSE) && (empty($row[$flux_key]) == FALSE) && (floatval($row['ts']) >= $ts_min)) {

                    array_push($data['flux'], array($met, $row[$flux_key]));

                }

                // Photon flux error
                if ((empty($row['ts']) == FALSE) && (empty($row[$flux_error_key]) == FALSE) && (floatval($row['ts']) >= $ts_min)) {

                    $flux_error_max = floatval($row[$flux_key]) + floatval($row[$flux_error_key]);
                    $flux_error_min = floatval($row[$flux_key]) - floatval($row[$flux_error_key]);

                    $flux_error_max = sprintf("%0.2e", $flux_error_max);
                    $flux_error_min = sprintf("%0.2e", $flux_error_min);

                    array_push($data['flux_error'], array($met, $flux_error_min, $flux_error_max));

                }        

                // Photon flux upper limit
                if ((empty($row['ts']) == FALSE) && (empty($row[$flux_upper_limit_key]) == FALSE) && (floatval($row['ts']) < $ts_min)) {

                    $flux_upper_limit = sprintf("%0.2e", $row[$flux_upper_limit_key]);
                    array_push($data['flux_upper_limits'], array($met, $flux_upper_limit));

                }

                // Photon index and photon index error
                if ((empty($row['ts']) == FALSE) && (empty($row['photon_index']) == FALSE) && (floatval($row['ts']) >= $ts_min)) {

                    // Photon index
                    $photon_index = sprintf("%0.2f", $row['photon_index']);
                    array_push($data['photon_index'], array($met, $photon_index));

                    // Photon index error
                    $index_error_max = floatval($row['photon_index']) + floatval($row['photon_index_error']);
                    $index_error_min = floatval($row['photon_index']) - floatval($row['photon_index_error']);

                    $index_error_max = sprintf("%0.2f", $index_error_max);
                    $index_error_min = sprintf("%0.2f", $index_error_min);

                    array_push($data['photon_index_error'], array($met, $index_error_max, $index_error_min));
                }

                // Fit Tolerance
                if ((empty($row['ts']) == FALSE)) {
                    array_push($data['fit_tolerance'], array($met, $row['fit_tolerance']));
                }

                // Fit Convergance
                if ((empty($row['ts']) == FALSE)) {
                    array_push($data['fit_convergance'], array($met, $row['return_code']));
                }

                // bin id
                if (empty($row['ts']) == FALSE) {
                    array_push($data['bin_id'], $row['bin_id']);
                }

                // dlogl
                if (empty($row['ts']) == FALSE) {
                    array_push($data['dlogl'], $row['dlogl']);
                }

                // EG
                if (empty($row['ts']) == FALSE) {
                    array_push($data['EG'], $row['EG']);
                }

                // GAL
                if (empty($row['ts']) == FALSE) {
                    array_push($data['GAL'], $row['GAL']);
                }

            }

            // Encode the data into a json file
            // echo json_encode($data);

            // $data = json_encode($data);
            // echo trim($data, '"'); 

            $data = str_replace('"', '', json_encode($data));
            $data = str_replace('{ts:', '{"ts":', $data);
            $data = str_replace('flux:', '"flux":', $data);
            $data = str_replace('flux_upper_limits:', '"flux_upper_limits":', $data);
            $data = str_replace('flux_error:', '"flux_error":', $data);
            $data = str_replace('photon_index:', '"photon_index":', $data);
            $data = str_replace('photon_index_error:', '"photon_index_error":', $data);          
            $data = str_replace('fit_tolerance:', '"fit_tolerance":', $data);
            $data = str_replace('fit_convergance:', '"fit_convergance":', $data);
            $data = str_replace('bin_id:', '"bin_id":', $data);
            $data = str_replace('dlogl:', '"dlogl":', $data);
            $data = str_replace('EG:', '"EG":', $data);
            $data = str_replace('GAL:', '"GAL":', $data);

            echo $data;

        }

        if ($index_type === 'free') {

            $queryStatement = 'SELECT tmin, tmax, ts2, ' . $flux_type . '_flux2, ' . $flux_type . '_flux_error2, ' . $flux_type . '_flux_upper_limit, photon_index2, photon_index_error2, fit_tolerance, return_code2,  dlogl, EG2, GAL2, bin_id FROM lightcurve_data_v2 WHERE source_name == \'' . $source_name . '\' AND cadence == \'' . $cadence . '\' ORDER BY tmin ASC';


            if (isset($_GET['verbose'])) {
                echo  $queryStatement;
                echo "<BR><BR>";
            }

            // Establish the database connection
            // $db = new SQLite3('./db/lc_repository.db');
            $db = new SQLite3('./db/lc_repository_v2.db');
            $results = $db->query($queryStatement);

            // Create the data array to store the values
            $data = array();
            $data['ts'] = array();
            $data['flux'] = array();
            $data['flux_upper_limits'] = array();
            $data['flux_error'] = array();
            $data['photon_index'] = array();
            $data['photon_index_error'] = array();           
            $data['fit_tolerance'] = array();
            $data['fit_convergance'] = array();
            $data['dlogl'] = array();
            $data['EG'] = array();
            $data['GAL'] = array();            
            $data['bin_id'] = array();

            // // Construct the flux keyword
            $flux_key = $flux_type . '_flux2';
            $flux_error_key = $flux_type . '_flux_error2';
            $flux_upper_limit_key = $flux_type . '_flux_upper_limit';

            // Retrieve the data 
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

                $met = (intval($row['tmin']) + intval($row['tmax']))/2.0;

                // TS
                if (empty($row['ts2']) == FALSE) {
                    $ts = sprintf("%0.2f", $row['ts2']);

                    array_push($data['ts'], array($met, $ts));
                }

                // Photon flux
                if ((empty($row['ts2']) == FALSE) && (empty($row[$flux_key]) == FALSE) && (floatval($row['ts2']) >= $ts_min)) {

                    array_push($data['flux'], array($met, $row[$flux_key]));
                }

                // Photon flux error
                if ((empty($row['ts2']) == FALSE) && (empty($row[$flux_error_key]) == FALSE) && (floatval($row['ts2']) >= $ts_min)) {

                    $flux_error_max = floatval($row[$flux_key]) + floatval($row[$flux_error_key]);
                    $flux_error_min = floatval($row[$flux_key]) - floatval($row[$flux_error_key]);

                    $flux_error_max = sprintf("%0.2e", $flux_error_max);
                    $flux_error_min = sprintf("%0.2e", $flux_error_min);

                    array_push($data['flux_error'], array($met, $flux_error_min, $flux_error_max));
                }        

                // Photon flux upper limit
                if ((empty($row['ts2']) == FALSE) && (empty($row[$flux_upper_limit_key]) == FALSE) && (floatval($row['ts2']) < $ts_min)) {

                    $flux_upper_limit = sprintf("%0.2e", $row[$flux_upper_limit_key]);
                    array_push($data['flux_upper_limits'], array($met, $flux_upper_limit));
                }

                // Photon index and photon index error
                if ((empty($row['ts2']) == FALSE) && (empty($row['photon_index2']) == FALSE) && (floatval($row['ts2']) >= $ts_min)) {

                    // Photon index
                    $photon_index = sprintf("%0.2f", $row['photon_index2']);
                    array_push($data['photon_index'], array($met, $photon_index));

                    // Photon index error
                    $index_error_max = floatval($row['photon_index2']) + floatval($row['photon_index_error2']);
                    $index_error_min = floatval($row['photon_index2']) - floatval($row['photon_index_error2']);

                    $index_error_max = sprintf("%0.2f", $index_error_max);
                    $index_error_min = sprintf("%0.2f", $index_error_min);

                    array_push($data['photon_index_error'], array($met, $index_error_max, $index_error_min));
                }

                // Fit Tolerance
                if (empty($row['ts2']) == FALSE) {
                    array_push($data['fit_tolerance'], array($met, $row['fit_tolerance']));
                }

                // Fit Convergance
                if ((empty($row['ts2']) == FALSE)) {
                    array_push($data['fit_convergance'], array($met, $row['return_code2']));
                }

                // bin id
                if (empty($row['ts2']) == FALSE) {
                    array_push($data['bin_id'], $row['bin_id']);
                }

                // dlogl
                if (empty($row['ts2']) == FALSE) {
                    array_push($data['dlogl'], $row['dlogl']);
                }

                // EG
                if (empty($row['ts2']) == FALSE) {
                    array_push($data['EG'], $row['EG2']);
                }

                // GAL
                if (empty($row['ts2']) == FALSE) {
                    array_push($data['GAL'], $row['GAL2']);
                }


            }

            // Encode the data into a json file
            // echo json_encode($data);

            // $data = json_encode($data);
            // echo trim($data, '"'); 

            $data = str_replace('"', '', json_encode($data));
            $data = str_replace('{ts:', '{"ts":', $data);
            $data = str_replace('flux:', '"flux":', $data);
            $data = str_replace('flux_upper_limits:', '"flux_upper_limits":', $data);
            $data = str_replace('flux_error:', '"flux_error":', $data);
            $data = str_replace('photon_index:', '"photon_index":', $data);
            $data = str_replace('photon_index_error:', '"photon_index_error":', $data);          
            $data = str_replace('fit_tolerance:', '"fit_tolerance":', $data);
            $data = str_replace('fit_convergance:', '"fit_convergance":', $data);
            $data = str_replace('bin_id:', '"bin_id":', $data);
            $data = str_replace('dlogl:', '"dlogl":', $data);
            $data = str_replace('EG:', '"EG":', $data);
            $data = str_replace('GAL:', '"GAL":', $data);

            echo $data;

        }
    }

    if ($typeOfRequest === 'sourceData') { 

        // Extract the url parameters
        if (isset($_GET['source_name'])) { 

            $source_name = $_GET['source_name']; 

        } else {
            echo '<BR><B>Usage Examples:</B><BR>';
            echo 'queryDB.php?typeOfRequest=sourceData&source=4FGL_J0237d8p2848';
            return;
        }

        // Construct the query statement
        // $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux1000, Unc_Flux1000, Energy_Flux100, Unc_Energy_Flux100, SpectrumType, PL_Index, LP_Index, PLEC_Index, Variability_Index, CLASS1, ASSOC1, ASSOC_FGL, ASSOC_FHL FROM Catalog_4FGL WHERE source_name =\'' . $source_name . '\'';
        $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux1000, Energy_Flux100, SpectrumType, PL_Index, Unc_PL_Index, LP_Index, Unc_LP_Index, LP_beta, Unc_LP_beta, PLEC_Index, Unc_PLEC_Index, Variability_Index, CLASS1, ASSOC1, ASSOC_FGL, ASSOC_FHL FROM Catalog_4FGL WHERE source_name =\'' . $source_name . '\'';

        if (isset($_GET['verbose'])) {
            echo  $queryStatement;
            echo "<BR><BR>";
        }

        // Establish the database connection
        $db = new SQLite3('./db/gll_psc_v21.db');
        $results = $db->query($queryStatement);

        // Create an array to store the results
        $data = array();

        // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

            // Prepare the data
            $row['RAJ2000'] = number_format($row['RAJ2000'], 3);
            $row['DEJ2000'] = number_format($row['DEJ2000'], 3);
            $row['GLON']= number_format($row['GLON'], 3);
            $row['GLAT'] = number_format($row['GLAT'], 3);

            $row['Signif_Avg'] = number_format($row['Signif_Avg'], 2);
            // $row['Flux1000'] = $row['Flux1000'];
            // $row['Unc_Flux1000'] = $row['Unc_Flux1000'];
            // $row['Energy_Flux100'] = $row['Energy_Flux100'];
            // $row['Unc_Energy_Flux100'] = $row['Unc_Energy_Flux100'];

            $row['SpectrumType'] = $row['SpectrumType'];
            $row['PL_Index'] = number_format($row['PL_Index'], 2);
            $row['LP_Index'] = number_format($row['LP_Index'], 2);
            $row['PLEC_Index'] = number_format($row['PLEC_Index'], 2);

            $row['Variability_Index'] = number_format($row['Variability_Index'], 2);
            // $row['CLASS1'] = $row['CLASS1'];
            // $row['ASSOC1'] = $row['ASSOC1'];
            // $row['ASSOC_FGL'] = $row['ASSOC_FGL'];
            // $row['ASSOC_FHL'] = $row['ASSOC_FHL'];

            $data[] = $row;

         
        }  

        // Encode the PHP associative array into a JSON associative array
        echo json_encode($data);
    }

?>  






