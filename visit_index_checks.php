<?php

function check_dependencies($oParents) {
//    global $is_form_inserted;
    $dependencies = false;
//    if (!$is_form_inserted && count($oParents) > 0) {
    if (count($oParents) > 0) {
        foreach ($oParents as $oParent) {
            if (!$oParent->is_completed) {
                $dependencies .= $oParent->title . ', ';
            }
        }
        if ($dependencies) {
            $dependencies = Strings::remove_last($dependencies, 2);
            $dependencies .= ' first';
        }
    }
    return $dependencies;
}

function check_optional_required($type) {
    $required = false;
    switch ($type) {
        case 'test': $required = is_required_test();
            break;
    }
    return $required ? FORM_REQUIRED : FORM_OPTIONAL;
}

//--------------------------------------CHECK SPECIFIC REQUIRED FUNCTIONS
function is_required_test() {
    return true;
}