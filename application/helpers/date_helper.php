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
 * Available date formats
 * The setting value represents the PHP date() formatting, the datepicker value represents the
 * DatePicker formatting (see http://bootstrap-datepicker.readthedocs.io/en/stable/options.html#format)
 *
 * @return array
 */
function date_formats()
{
    return array(
        'm/d/Y' => array(
            'setting' => 'm/d/Y',
            'datepicker' => 'mm/dd/yyyy'
        ),
        'm-d-Y' => array(
            'setting' => 'm-d-Y',
            'datepicker' => 'mm-dd-yyyy'
        ),
        'm.d.Y' => array(
            'setting' => 'm.d.Y',
            'datepicker' => 'mm.dd.yyyy'
        ),
        'Y/m/d' => array(
            'setting' => 'Y/m/d',
            'datepicker' => 'yyyy/mm/dd'
        ),
        'Y-m-d' => array(
            'setting' => 'Y-m-d',
            'datepicker' => 'yyyy-mm-dd'
        ),
        'Y.m.d' => array(
            'setting' => 'Y.m.d',
            'datepicker' => 'yyyy.mm.dd'
        ),
        'd/m/Y' => array(
            'setting' => 'd/m/Y',
            'datepicker' => 'dd/mm/yyyy'
        ),
        'd-m-Y' => array(
            'setting' => 'd-m-Y',
            'datepicker' => 'dd-mm-yyyy'
        ),
        'd-M-Y' => array(
            'setting' => 'd-M-Y',
            'datepicker' => 'dd-M-yyyy'
        ),
        'd.m.Y' => array(
            'setting' => 'd.m.Y',
            'datepicker' => 'dd.mm.yyyy'
        ),
        'j.n.Y' => array(
            'setting' => 'j.n.Y',
            'datepicker' => 'd.m.yyyy'
        ),
        'd M,Y' => array(
            'setting' => 'd M,Y',
            'datepicker' => 'dd M,yyyy'
        ),
    );
}

/**
 * @param $date
 * @param bool $ignore_post_check
 * @return bool|DateTime|string
 */
function date_from_mysql($date, $ignore_post_check = false)
{
    if ($date <> '0000-00-00') {
        if (!$_POST or $ignore_post_check) {
            $CI = &get_instance();

            $date = DateTime::createFromFormat('Y-m-d', $date);
            return $date->format($CI->mdl_settings->setting('date_format'));
        }
        return $date;
    }
    return '';
}

/**
 * @param $timestamp
 * @return string
 */
function date_from_timestamp($timestamp)
{
    $CI = &get_instance();

    $date = new DateTime();
    $date->setTimestamp($timestamp);
    return $date->format($CI->mdl_settings->setting('date_format'));
}

/**
 * @param $date
 * @return string
 */
function date_to_mysql($date)
{
    $CI = &get_instance();

    $date = DateTime::createFromFormat($CI->mdl_settings->setting('date_format'), $date);
    return $date->format('Y-m-d');
}

/**
 * @param $date
 * @return bool
 */
function is_date($date)
{
    $CI = &get_instance();
    $format = $CI->mdl_settings->setting('date_format');
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * @return string
 */
function date_format_setting()
{
    $CI = &get_instance();

    $date_format = $CI->mdl_settings->setting('date_format');

    $date_formats = date_formats();

    return $date_formats[$date_format]['setting'];
}

/**
 * @return string
 */
function date_format_datepicker()
{
    $CI = &get_instance();

    $date_format = $CI->mdl_settings->setting('date_format');

    $date_formats = date_formats();

    return $date_formats[$date_format]['datepicker'];
}

/**
 * Adds interval to user formatted date and returns user formatted date
 * To be used when date is being output back to user.
 *
 * @param $date - user formatted date
 * @param $increment - interval (1D, 2M, 1Y, etc)
 * @return string
 */
function increment_user_date($date, $increment)
{
    $CI = &get_instance();

    $mysql_date = date_to_mysql($date);

    $new_date = new DateTime($mysql_date);
    $new_date->add(new DateInterval('P' . $increment));

    return $new_date->format($CI->mdl_settings->setting('date_format'));
}

/**
 * Adds interval to yyyy-mm-dd date and returns in same format
 *
 * @param $date
 * @param $increment
 * @return string
 */
function increment_date($date, $increment)
{
    $new_date = new DateTime($date);
    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format('Y-m-d');
}

function bulan($bln)
{
    $b=array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    );
    return $b[$bln];
}

function romawi($bln)
{
    $b=array('I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');
    return $b[($bln-1)];
}


function itempajak()
{
    $pajak=['Pajak PPH 22','Pajak PPH 23','Pajak PPH 25','Pajak Lain (Jika Ada)'];
    return $pajak;
}

function itembiayalain()
{
    $biaya=['Backup Fund','Komisi Sales External','Administrasi Project',
    'I. Biaya Tagihan/Bendahara/KPPN dll',
    'II. Biaya Jaminan Pelaksanaan Asuransi',
    'III. Biaya Entertain',
    'IV. Biaya Donate (Pembelian Ayam)','Cost of Fund / Interest (COF)'];
    return $biaya;
}

function createSlug($str, $delimiter = '-'){

    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return $slug;

}

function hitunghari($dates,$days)
{
    $date1 = strtotime("+30 days", strtotime($dates));
    $newdate=date("Y-m-d", $date1);

    $begin = new DateTime($dates);
    $end = new DateTime($newdate);

    $count=0;
    
    $daterange  = new DatePeriod($begin, new DateInterval('P1D'), $end);
    foreach($daterange as $date){
        $daterange     = $date->format("Y-m-d");
        $datetime     = DateTime::createFromFormat('Y-m-d', $daterange);

        $day         = $datetime->format('D');

        if($day!="Sun" && $day!="Sat") {
            $count++;
        }

        if($count==$days)
        {
            break;
        }
    }  
    return $daterange;  
}
function jlhharitanpaweekend($date1,$date2)
{
    $begin = new DateTime($date1);
    $end = new DateTime($date2);

    $daterange     = new DatePeriod($begin, new DateInterval('P1D'), $end);
    //mendapatkan range antara dua tanggal dan di looping
    $i=0;
    $x     =    0;
    $end     =    1;

    foreach($daterange as $date){
        $daterange     = $date->format("Y-m-d");
        $datetime     = DateTime::createFromFormat('Y-m-d', $daterange);

        //Convert tanggal untuk mendapatkan nama hari
        $day         = $datetime->format('D');

        //Check untuk menghitung yang bukan hari sabtu dan minggu
        if($day!="Sun" && $day!="Sat") {
            //echo $i;
            $x    +=    $end-$i;
            
        }
        $end++;
        $i++;
    }    
    return $x;
}