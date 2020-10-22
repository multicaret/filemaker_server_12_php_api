<?php
include('init.php');

if (!empty($fm)) {

    $findCommand = $fm->newFindCommand('print_color_types');
//    $findCommand->setRange(0, 100);
    $result = $findCommand->execute();
    if (FileMaker::isError($result)) {
        printf(json_encode(array('data' => array(), 'error' => array('message' => $result->getMessage()))));
        die();
    }
    $records = $result->getRecords();
    $mainTrArray = array();
    $mainArArray = array();
    $tmpRecords = array();
    foreach ($records as $record) {
        $attribute = array();
        $arValue = array_values($record->_impl->_fields['BASKI TÜRÜ ARP']);
        $clearedArValue = trim($arValue[0]);
        if (!array_key_exists($clearedArValue, $mainArArray)) {
            $trValue = array_values($record->_impl->_fields['BASKI TÜRÜ']);
            $clearedTrValue = trim($trValue[0]);
            $mainArArray[$clearedArValue] = $clearedTrValue;
            if (!empty($clearedArValue) || !empty($clearedTrValue)) {
                $attribute['BASKI TÜRÜ'] = $clearedTrValue;
                $attribute['BASKI TÜRÜ ARP'] = $clearedArValue;
                array_push($tmpRecords, $attribute);
            }
        }
    }
    $jsonData = array('data' => $tmpRecords);
    echo json_encode($jsonData);
} else {
    echo json_encode(array('error' => array('message' => 'FM not initialized')));
}

