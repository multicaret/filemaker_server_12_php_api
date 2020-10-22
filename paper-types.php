<?php
include('init.php');

if (!empty($fm)) {

    $findCommand = $fm->newFindCommand('paper_types');
    $result = $findCommand->execute();
    if (FileMaker::isError($result)) {
        printf(json_encode(array('data' => array(), 'error' => array('message' => $result->getMessage()))));
        die();
    }
    $paperTypesKeys = array('id', 'ar', 'tr');
    $records = $result->getRecords();
    $tmpRecords = array();
    foreach ($records as $record) {
        $attribute = array();
        $timer = 0;
        foreach ($record->_impl->_fields as $tempIndex => $tempItem) {
            $attributeValue = array_values($tempItem);
            $attribute[$paperTypesKeys[$timer]] = $attributeValue[0];
            $timer++;
        }
        array_push($tmpRecords, $attribute);
    }
    $jsonData = array('data' => $tmpRecords);
    echo json_encode($jsonData);
} else {
    echo json_encode(array('error' => array('message' => 'FM not initialized')));
}

