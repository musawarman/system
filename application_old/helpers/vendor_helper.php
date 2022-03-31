<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * @param object $client
 * @return string
 */
function format_vendor($client)
{
    if ($client->vendor_surname != "") {
        return $client->vendor_name . " " . $client->vendor_surname;
    }

    return $client->vendor_name;
}

/**
 * @param string $gender
 * @return string
 */
function format_gender_vendor($gender)
{
    if ($gender == 0) {
        return trans('gender_male');
    }

    if ($gender == 1) {
        return trans('gender_female');
    }

    return trans('gender_other');
}
