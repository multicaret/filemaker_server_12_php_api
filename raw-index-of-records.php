<?php

include('init.php');

if (!empty($fm)) {

    $findCommand = $fm->newFindCommand('new_book_list');
    $findCommand->addSortRule('updated_at', 1,FILEMAKER_SORT_DESCEND);
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
        printf(json_encode(array('data' => array(), 'error' => array('message' => $result->getMessage()))));
        die();
    }
    $records = $result->getRecords();
    $tmpRecords = array();
    foreach ($records as $record) {
        $tempBook = array();
        foreach ($record->_impl->_fields as $indexS => $tempItem) {
            $attributeValue = array_values($tempItem);
            $tempBook[$indexS] = trim($attributeValue[0]);
        }
        array_push($tmpRecords, $tempBook);
    }
    $jsonData = array(
        'data' => $tmpRecords,
        'page' => $page,
        'range' => $findCommand->getRange(),
        'filters' => $findCommand->getRelatedSetsFilters()
    );
    echo json_encode($jsonData);

} else {
    echo json_encode(array('error' => array('message' => 'FM not initialized')));
}

