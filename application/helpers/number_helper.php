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
 * Output the amount as a currency amount, e.g. 1.234,56 €
 *
 * @param $amount
 * @return string
 */
function format_currency($amount)
{
    global $CI;
    $currency_symbol = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    } elseif ($currency_symbol_placement == 'afterspace') {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
    } else {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $currency_symbol;
    }
}
function format_currency_indo($amount)
{

    return number_format($amount,0,',','.');

}

/**
 * Output the amount as a currency amount, e.g. 1.234,56
 *
 * @param null $amount
 * @return null|string
 */
function format_amount($amount = null)
{
    if ($amount) {
        $CI =& get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point = $CI->mdl_settings->setting('decimal_point');

        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    }
    return null;
}

/**
 * Standardize an amount based on the system settings
 *
 * @param $amount
 * @return mixed
 */
function standardize_amount($amount)
{
    $CI =& get_instance();
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    $amount = str_replace($thousands_separator, '', $amount);
    $amount = str_replace($decimal_point, '.', $amount);

    return $amount;
}

function penyebut2($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = penyebut2($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = penyebut2($nilai/10)." puluh". penyebut2($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . penyebut2($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = penyebut2($nilai/100) . " ratus" . penyebut2($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . penyebut2($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = penyebut2($nilai/1000) . " ribu" . penyebut2($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = penyebut2($nilai/1000000) . " juta" . penyebut2($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = penyebut2($nilai/1000000000) . " milyar" . penyebut2(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = penyebut2($nilai/1000000000000) . " trilyun" . penyebut2(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
 
	function terbilang($nilai) {
		if($nilai<0) {
			$hasil = "minus ". trim(penyebut2($nilai));
		} else {
			$hasil = trim(penyebut2($nilai));
		}     		
		return $hasil;
	}