<?php

namespace app\controller;

class z4mtsts_ui_ctl extends \AppController {
    
    static public function isActionAllowed($action) {
        $status = parent::isActionAllowed($action);
        if ($status === FALSE) {
            return FALSE;
        }
        if ($action === 'not_allowed') {
            return FALSE;
        }
        return TRUE;
    }

    static protected function action_ajax1() {
        $request = new \Request();
        $response = new \Response();
        if (!\UserSession::isUITokenValid()) {
            $response->setFailedMessage(NULL, 'UI token is invalid.');
        } else {
            $response->success = $request->value1 === '456' 
                && $request->value2 === 'ABC';
        }
        return $response;
    }
    
    static protected function action_file() {
        $response = new \Response();
        $filePath = CFG_DOCUMENTS_DIR . DIRECTORY_SEPARATOR . 'datafile.txt';
        $response->setFileToDownload($filePath);
        return $response;
    }
    
    static protected function action_csv() {
        $response = new \Response();
        $response->setDataForCsv([['Content of the CSV file.']], 'datafile.csv', ['Column'], FALSE);
        return $response;
    }
    
    static protected function action_not_allowed() {
        $response = new \Response();
        $response->success = TRUE;
        return $response;
    }
    
    static protected function action_ajaxexception() {
        throw new \Exception('This is a not catched exception.');
    }
    
    static protected function action_ajax_runtime_error() {
        unknownFunction();
    }
    
    static protected function action_autocomplete1() {
        $request = new \Request();
        $response = new \Response();
        $suggestions = array();
        if ($request->query === 'a' 
                || $request->query === 'ab' 
                || $request->query === 'abc') {
            $suggestions[] = array('label' => 'abc');
            $suggestions[] = array('label' => 'abcd');
            $suggestions[] = array('label' => 'abcde');
        }
        $response->setResponse($suggestions);
        return $response;
    }
    
    /* Returns form data */
    static protected function action_form1() {
        $request = new \Request();
        $response = new \Response();
        if ($request->id === '57') {
            $response->setResponse([
                'input_text' => 'Hello John',
                'input_checkbox_1' => '',
                'input_checkbox_2' => '2',
                'input_checkbox_multiple[]' => ['CB2', 'CB3'],
                'input_radio' => '3',
                'textarea' => 'How do you do?',
                'select_one' => '2',
                'select_multiple[]' => ['A','C']
            ]);
        } else {
            $response->setResponse([]);
        }
        return $response;
    }
    
    /* Submit form data */
    static protected function action_form2() {
        $request = new \Request();
        $isOK = $request->input_text === 'Hello John'
                    && $request->input_checkbox_1 === NULL
                    && $request->input_checkbox_2 === '2'
                    && $request->input_checkbox_multiple === ['CB2', 'CB3']
                    && $request->input_radio === '3'
                    && $request->textarea === 'How do you do?'
                    && $request->select_one === '2'
                    && $request->select_multiple === ['A', 'C'];
        $response = new \Response();
        if ($isOK) {
            $response->setSuccessMessage(NULL, 'Form submit succeeded.');
        } else {
            $response->setFailedMessage(NULL, 'Form submit failed.');
        }
        return $response;
    }
    
    static protected function action_datalist1() {
        $maxItems = 100; // Display of 100 items max.
        $request = new \Request();
        $first = $request->first; // The first item number (zero-based value)
        $rowCount = $request->count; // The number of items requested (40 rows in this example)
        $items = array();
        for ($index = $first; $index < $first+$rowCount && $index < $maxItems; $index++) {
            $id = $index +1;
            $items[] = array('id' => $id, 'label' => "Item {$id}");
        }
        $response = new \Response();
        $response->total = $maxItems;
        $response->rows = $items;
        return $response; // As JSON format
    }
    
    static protected function action_datalistdetail1() {
        $request = new \Request();        
        $response = new \Response();
        $response->id = $request->id;
        $response->label = "Item {$request->id}";
        return $response;
    }
    
    static protected function action_modal1() {
        $request = new \Request();
        $response = new \Response();
        $response->success = $request->label === 'Hello' && $request->value === '18';
        return $response;
    }

}
