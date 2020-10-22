<?php

include('init.php');

if (!empty($fm)) {

    $findCommand = $fm->newFindCommand('new_book_list');
    $time = date('m.d.Y', strtotime("-0 day"));
    $findCommand->addFindCriterion('status', "1");

    if (isset($_GET["per_page"])) {
        $perPage = $_GET["per_page"];
    } else {
        $perPage = 10;
    };
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
    } else {
        $page = 1;
    };

    $start_from = ($page - 1) * $perPage;
//    $total = $start_from + $perPage;
    $findCommand->setRange($start_from, intval($perPage));
    $result = $findCommand->execute();
    if (FileMaker::isError($result)) {
        printf(json_encode(array('data' => array(),'date' => $time, 'error' => array('message' => $result->getMessage()))));
        die();
    }
    $records = $result->getRecords();
    $tmpRecords = array();
    foreach ($records as $record) {
        $tempBook = array();
        foreach ($record->_impl->_fields as $indexS => $tempItem) {
            $attributeValue = array_values($tempItem);
            if (!empty($allKeys)) {
                if (!is_null(keyValidation($indexS, $allKeys)) && $allKeys[$indexS]) {
                    $tempBook[$indexS] = trim(array_shift($attributeValue));
                }
            }
        }
        array_push($tmpRecords, $tempBook);
    }
    $jsonData = array(
        'data' => array(),
        'page' => $page,
        'range' => $findCommand->getRange(),
        'total_records' => 0,
        'filters' => $findCommand->getRelatedSetsFilters()
    );

    if (isset($allKeys) && !empty($objectTemplate) && count($tmpRecords) > 0) {
        foreach ($tmpRecords as $rawItem) {
            $finalBook = array();
            foreach ($objectTemplate as $itemKey => $objectKey) {
                foreach ($objectKey as $irshadColumnKey) {
                    if ($allKeys[$irshadColumnKey]) {
                        if($itemKey === 'width' || $itemKey === 'height') {
                            $finalBook[$itemKey] .= ' ' . strtolower($rawItem[$irshadColumnKey]);
                        } else {
                            $finalBook[$itemKey] .= ' ' . $rawItem[$irshadColumnKey];
                        }
                    }
                }
                $finalBook[$itemKey] = trim($finalBook[$itemKey]);
            }
            array_push($jsonData['data'], $finalBook);
        }
        $jsonData['total_records'] = count($tmpRecords);
    }
    echo json_encode($jsonData);

} else {
    echo json_encode(array('error' => array('message' => 'FM not initialized')));
}

