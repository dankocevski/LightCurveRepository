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

    if (isset($_GET['typeOfRequest']) == false) {

        echo '<BR><B>Usage Examples:</B><BR>';
        echo 'queryDB.php?typeOfRequest=MapData&ra=0&dec=0&radius=12<BR>';
        return;
    } else {

        // Determine the type of data requested
        $typeOfRequest = $_GET['typeOfRequest'];

        if (isset($_GET['catalog'])) { 
            $catalog = $_GET['catalog'];
        }
    }

    if (isset($_GET['keyword'])) { 

        // Get the URL parameter
        $SearchQuery = $_GET['keyword'];

        // Sanitize the input
        $SearchQuery = stripslashes($SearchQuery);
        $SearchQuery = strip_tags($SearchQuery); 

        // Add wild cards
        $SearchQuery = "%" . $SearchQuery . "%";
    }

    // Get any search coordinates
    if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

        // Get the URL parameters
        $raROI = $_GET['ra']; 
        $decROI = $_GET['dec']; 
        $radius = $_GET['radius']; 
    }

    // Return detailed information on a limited number of sources to be displayed in the data table
    if ($typeOfRequest === 'SourceList') { 

        if ($catalog === '4FGL') {

            // Open the database
            $db = new SQLite3('./db/gll_psc_v27.db');

            if (isset($_GET['keyword'])) { 

                // Prepare the query statement 
                $queryStatement = $db->prepare('SELECT Source_Name, ASSOC_FGL, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux1000, Unc_Flux1000, Energy_Flux100, Unc_Energy_Flux100, SpectrumType, PL_Index, LP_Index, PLEC_Index, Variability_Index, CLASS1 FROM Catalog_4FGL WHERE Source_Name LIKE :binding1 OR ASSOC1 LIKE :binding2 OR ASSOC_TEV LIKE :binding3 or CLASS1 LIKE :binding4 COLLATE NOCASE ORDER BY RAJ2000;');

                // Bind the statement parameters
                $queryStatement->bindValue(':binding1', $SearchQuery, SQLITE3_TEXT);
                $queryStatement->bindValue(':binding2', $SearchQuery, SQLITE3_TEXT);
                $queryStatement->bindValue(':binding3', $SearchQuery, SQLITE3_TEXT);
                $queryStatement->bindValue(':binding4', $SearchQuery, SQLITE3_TEXT);

                // Query the database
                $results = $queryStatement->execute();

            } else { 

                $queryStatement = 'SELECT Source_Name, ASSOC_FGL, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux1000, Unc_Flux1000, Energy_Flux100, Unc_Energy_Flux100, SpectrumType, PL_Index, LP_Index, PLEC_Index, Variability_Index, CLASS1 FROM Catalog_4FGL ORDER BY RAJ2000;';

                // Query the database
                $results = $db->query($queryStatement);

            }
        }

        if ($catalog === '2FLGC') {

            // Open the database
            $db = new SQLite3('./db/gll_2flgc.db');

            if (isset($_GET['keyword'])) { 

                // Prepare the query statement 
                $queryStatement = 'SELECT GCNNAME, GRBNAME, GRBDATE, GRBMET, RA, DEC, ERR, GBMT90, ARR, LIKE_BEST_TS_GRB, LIKE_BEST_FLUX_ENE, LIKE_BEST_FLUENCE_ENE FROM Catalog_2FLGC WHERE GCNNAME LIKE :binding1 OR GRBNAME LIKE :binding2 COLLATE NOCASE';

                // $queryStatement->bindParam('sss', $SearchQuery, $SearchQuery, $SearchQuery);
                $queryStatement->bindValue(':binding1', $SearchQuery, SQLITE3_TEXT);
                $queryStatement->bindValue(':binding2', $SearchQuery, SQLITE3_TEXT);

                // Query the database
                $results = $queryStatement->execute();

            } else {

                // Construct the query statement 
                $queryStatement = 'SELECT GCNNAME, GRBNAME, GRBDATE, GRBMET, RA, DEC, ERR, GBMT90, ARR, LIKE_BEST_TS_GRB, LIKE_BEST_FLUX_ENE, LIKE_BEST_FLUENCE_ENE FROM Catalog_2FLGC ORDER BY GRBMET;';

                // Query the database
                $results = $db->query($queryStatement);

            }
        }

        if ($catalog === 'IceCube') {

            // Open the database
            $db = new SQLite3('./db/IceCubeGCNs.db');

            if (isset($_GET['keyword'])) { 

                // Construct the query statement 
                $queryStatement = 'SELECT NOTICE_TYPE, EVENT_NUM, DISCOVERY_DATE, DISCOVERY_DATE_TJD, DISCOVERY_TIME, SRC_RA, SRC_DEC, SRC_ERROR, ENERGY, FAR, NOTICE_TYPE, URL FROM ICeCubeEvents WHERE EVENT_NUM LIKE :binding1 OR COMMENT LIKE :binding2 COLLATE NOCASE';

                // $queryStatement->bindParam('sss', $SearchQuery, $SearchQuery, $SearchQuery);
                $queryStatement->bindValue(':binding1', $SearchQuery, SQLITE3_TEXT);
                $queryStatement->bindValue(':binding2', $SearchQuery, SQLITE3_TEXT);

                // Query the database
                $results = $queryStatement->execute();

            } else {

                // Construct the query statement 
                $queryStatement = 'SELECT NOTICE_TYPE, EVENT_NUM, DISCOVERY_DATE, DISCOVERY_DATE_TJD, DISCOVERY_TIME, SRC_RA, SRC_DEC, SRC_ERROR, ENERGY, FAR, NOTICE_TYPE, URL FROM ICeCubeEvents ORDER BY DISCOVERY_DATE_TJD DESC;';

                // Query the database
                $results = $db->query($queryStatement);

            }
        }

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

    // Return light curve data for one source
    if ($typeOfRequest === 'lightCurveData') { 

        // Extract the url parameters
        if (isset($_GET['source_name']) && (isset($_GET['cadence'])) && (isset($_GET['flux_type']))) { 

            # Get the url parameters
            $source_name = $_GET['source_name']; 
            $cadence = $_GET['cadence']; 
            $flux_type = $_GET['flux_type'];
            $index_type = $_GET['index_type']; 
            $ts_min = floatval($_GET['ts_min']);

            $source_name = stripslashes($source_name);
            $source_name = strip_tags($source_name); 
            $cadence = stripslashes($cadence);
            $cadence = strip_tags($cadence); 

            if (($flux_type === "photon") or ($flux_type === "energy")) {

                    // Fixed index values
                    $flux = $flux_type . '_flux';
                    $flux_error = $flux_type . '_flux_error';

                    // Free index values
                    $flux2 = $flux_type . '_flux2';
                    $flux_error2 = $flux_type . '_flux_error2';

                    $flux_upper_limit = $flux_type . '_flux_upper_limit';

            }

        } else {

            echo '<BR><B>Usage Examples:</B><BR>';
            echo 'queryDB.php?typeOfRequest=LightCurveData&source=&cadence=daily&cadence=photon';

            return;
        }

 
        if ($index_type === 'fixed') {

            // Establish the database connection
            $db = new SQLite3('./db/lc_repository_v2.db');

            // Prepare the query statement 
            $queryStatement = $db->prepare('SELECT tmin, tmax, ts, ' . $flux . ', ' . $flux_error . ', ' . $flux_upper_limit . ', photon_index, photon_index_error, fit_tolerance, return_code, dlogl, EG, GAL, bin_id FROM lightcurve_data_v2 WHERE source_name == :source_name AND cadence == :cadence ORDER BY tmin ASC');

                // Bind the statement parameters
                $queryStatement->bindValue(':source_name', $source_name, SQLITE3_TEXT);
                $queryStatement->bindValue(':cadence', $cadence, SQLITE3_TEXT);

            if (isset($_GET['verbose'])) {
                echo  $queryStatement;
                echo "<BR><BR>";
            }

            // Query the database
            $results = $queryStatement->execute();

            // Create the data array to store the values
            $data = array();
            $data['ts'] = array();
            $data['flux'] = array();
            $data['flux_upper_limits'] = array();
            $data['flux_error'] = array();
            $data['photon_index'] = array();
            $data['photon_index_interval'] = array();           
            $data['fit_tolerance'] = array();
            $data['fit_convergence'] = array();
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

                    array_push($data['photon_index_interval'], array($met, $index_error_max, $index_error_min));
                }

                // Fit Tolerance
                if ((empty($row['ts']) == FALSE)) {
                    array_push($data['fit_tolerance'], array($met, $row['fit_tolerance']));
                }

                // Fit Convergence
                if ((empty($row['ts']) == FALSE)) {
                    array_push($data['fit_convergence'], array($met, $row['return_code']));
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
            $data = str_replace('photon_index_interval:', '"photon_index_interval":', $data);          
            $data = str_replace('fit_tolerance:', '"fit_tolerance":', $data);
            $data = str_replace('fit_convergence:', '"fit_convergence":', $data);
            $data = str_replace('bin_id:', '"bin_id":', $data);
            $data = str_replace('dlogl:', '"dlogl":', $data);
            $data = str_replace('EG:', '"EG":', $data);
            $data = str_replace('GAL:', '"GAL":', $data);

            echo $data;
        }

        if ($index_type === 'free') {

            // Establish the database connection
            $db = new SQLite3('./db/lc_repository_v2.db');

            // Prepare the query statement 
            $queryStatement = $db->prepare('SELECT tmin, tmax, ts2, ' . $flux2 . ', ' . $flux_error2 . ', ' . $flux_upper_limit . ', photon_index2, photon_index_error2, fit_tolerance, return_code2, dlogl, EG2, GAL2, bin_id FROM lightcurve_data_v2 WHERE source_name == :source_name AND cadence == :cadence ORDER BY tmin ASC');

            // $queryStatement = 'SELECT tmin, tmax, ts2, ' . $flux_type . '_flux2, ' . $flux_type . '_flux_error2, ' . $flux_type . '_flux_upper_limit, photon_index2, photon_index_error2, fit_tolerance, return_code2,  dlogl, EG2, GAL2, bin_id FROM lightcurve_data_v2 WHERE source_name == \'' . $source_name . '\' AND cadence == \'' . $cadence . '\' ORDER BY tmin ASC';

            // Bind the statement parameters
            $queryStatement->bindValue(':source_name', $source_name, SQLITE3_TEXT);
            $queryStatement->bindValue(':cadence', $cadence, SQLITE3_TEXT);

            if (isset($_GET['verbose'])) {
                echo  $queryStatement;
                echo "<BR><BR>";
            }

            // Establish the database connection
            $results = $queryStatement->execute();

            // Create the data array to store the values
            $data = array();
            $data['ts'] = array();
            $data['flux'] = array();
            $data['flux_upper_limits'] = array();
            $data['flux_error'] = array();
            $data['photon_index'] = array();
            $data['photon_index_interval'] = array();           
            $data['fit_tolerance'] = array();
            $data['fit_convergence'] = array();
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

                    array_push($data['photon_index_interval'], array($met, $index_error_max, $index_error_min));
                }

                // Fit Tolerance
                if (empty($row['ts2']) == FALSE) {
                    array_push($data['fit_tolerance'], array($met, $row['fit_tolerance']));
                }

                // Fit Convergence
                if ((empty($row['ts2']) == FALSE)) {
                    array_push($data['fit_convergence'], array($met, $row['return_code2']));
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
            $data = str_replace('photon_index_interval:', '"photon_index_interval":', $data);          
            $data = str_replace('fit_tolerance:', '"fit_tolerance":', $data);
            $data = str_replace('fit_convergence:', '"fit_convergence":', $data);
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

            $source_name = stripslashes($source_name);
            $source_name = strip_tags($source_name); 

        } else {
            echo '<BR><B>Usage Examples:</B><BR>';
            echo 'queryDB.php?typeOfRequest=sourceData&source=4FGL_J0237d8p2848';
            return;
        }

        // Establish the database connection
        $db = new SQLite3('./db/gll_psc_v27.db');

        // Construct the query statement
        $queryStatement = $db->prepare('SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux1000, Energy_Flux100, SpectrumType, PL_Index, Unc_PL_Index, LP_Index, Unc_LP_Index, LP_beta, Unc_LP_beta, PLEC_Index, Unc_PLEC_Index, Variability_Index, CLASS1, ASSOC1, ASSOC_FGL, ASSOC_FHL FROM Catalog_4FGL WHERE source_name = :source_name;');

        // Bind the statement parameters
        $queryStatement->bindValue(':source_name', $source_name, SQLITE3_TEXT);

        // Query the database
        $results = $queryStatement->execute();

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






