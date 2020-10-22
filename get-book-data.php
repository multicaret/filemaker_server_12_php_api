<?php

include('init.php');


if (!empty($fm)) {
    if (isset($_GET["barcode"])) {
        $barcode = $_GET["barcode"];
    } else {
        echo json_encode(array('error' => array('message' => 'Please fill "barcode" first then try again.')));
        die();
    }
    $findCommand = $fm->newFindCommand('new_book_list');

    $findCommand->addFindCriterion('BARKOD', $barcode);
    $result = $findCommand->execute();

    if (FileMaker::isError($result)) {
        printf(json_encode(array('data' => array(), 'error' => array('message' => $result->getMessage()))));
        die();
    }
    $record = $result->getFirstRecord();
    $tmpRecords = array();
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
    $jsonData = array('data' => array());

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
    }
    echo json_encode($jsonData);

} else {
    echo json_encode(array('error' => array('message' => 'FM not initialized')));
}

