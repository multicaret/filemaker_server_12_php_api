<?php

include('init.php');

$allKeys = array(
    "BARKOD" => true,
    "STOK" => true,
    "created_at" => true,
    "updated_at" => true,
    "status" => true,
    "LİSTE SATIŞ FİYATI___" => true
);

$objectTemplate = array(
    "isbn" => array("BARKOD"),
    "created_at" => array("created_at"),
    "updated_at" => array("updated_at"),
    "status" => array("status"),
    "price" => array("LİSTE SATIŞ FİYATI___"),
    "quantity" => array("STOK"),
);


if (!empty($fm)) {

    if (isset($_GET["previous_day_count"])) {
        $beforeAt = $_GET["previous_day_count"];
    } else {
        $beforeAt = 1;
    }
    if (isset($_GET["column_name"])) {
        $updateSourceColumn = $_GET["column_name"];
    } else {
        $updateSourceColumn = 'stock_updated_at'; // sold_at
    }

    $findCommand = $fm->newFindCommand('new_book_list');
    $dateValue = date('m.d.Y', strtotime("-$beforeAt day"));
    $findCommand->addFindCriterion($updateSourceColumn, "$dateValue");
    $result = $findCommand->execute();

    if (FileMaker::isError($result)) {
        printf(json_encode(array('data' => array(), 'error' => array('message' => $result->getMessage()))));
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
        'total_records' => 0,
        'range' => $findCommand->getRange()
    );

    if (isset($allKeys) && !empty($objectTemplate) && count($tmpRecords) > 0) {
        foreach ($tmpRecords as $rawItem) {
            $finalBook = array();
            foreach ($objectTemplate as $itemKey => $objectKey) {
                foreach ($objectKey as $irshadColumnKey) {
                    if ($allKeys[$irshadColumnKey]) {
                        $finalBook[$itemKey] .= ' ' . $rawItem[$irshadColumnKey];
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

