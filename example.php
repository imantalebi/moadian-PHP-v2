<?php

use Imantalebi\MoadianPhpSdk\Moadian;

date_default_timezone_set('Asia/Tehran');

$privateKey = file_get_contents('./privateKey.key');
$cert = file_get_contents('./cert.csr');

$invoiceGregorianDatetTime = date("Y-m-d H:i:s");

$clientId = '######';

try {
    $sandBox = true; //جهت تست در سندباکس سامانه مودیان
    $moadian = new Moadian($clientId, $privateKey, $cert, $sandBox);
    $invoiceId = 1; //شماره فاکتور
    $invoiceDateTime = new \DateTime($invoiceGregorianDatetTime);
    $inno = str_pad(dechex($invoiceId), 10, '0', STR_PAD_LEFT);
    $invoiceHeader = [
        'taxid' => $moadian->generateInvoiceId($invoiceDateTime, $invoiceId),
        'inno' => $inno,
        'indatim' => $invoiceDateTime->getTimestamp() * 1000,
        'inty' => 2, // 1|2|3
        'ins' => 1, //اصلی / اصلاحی / ...
        'inp' => 1,
        'tins' => '00000000000', //شناسه ملی فروشنده
        'tob' => 2, // نوع شخص خریدار در الگوی نوع دوم اختیاریه
        'bid' => '',
        'tinb' => '', // شماره اقتصادی خریدار
        'tprdis' => 10000, // مجموع مبلغ قبل از کسر تخفیف
        'tdis' => 0, // مجموع تخفیف
        'tadis' => 0, // مجموع مبلغ پس از کسر تخفیف
        'tvam' => 900, // مجموع مالیات ارزش افزوده
        'tbill' => 10900, //مجموع صورتحساب
        'setm' => 1, // روش تسویه
    ];
    $invoiceBody = [[
    'sstid' => '2720000114542',
    'sstt' => 'بسته نرم افزار ماشین حساب',
    'mu' => 1627, //واحد اندازه گیری
    'am' => 1, //تعداد
    'fee' => 10000,
    'prdis' => 10000, //قبل از تخفیف
    'dis' => 0, //تخفیف
    'adis' => 0, //بعد از تخفیف
    'vra' => 9, //نرخ مالیات
    'vam' => 900, //مالیات
    'tsstam' => 10900, //مبلغ کل
    ]];

    $invoicePayment = [];

    $invoicePackets = [];

    $uid = SimpleGuidv4Service::generate();

    $invoicePackets[] = $moadian->createInvoicePacket($uid, $invoiceHeader, $invoiceBody, $invoicePayment);

    $res = $moadian->sendInvoice($invoicePackets);

    if ($res && is_array($res) && isset($res['result'])) {

        sleep(3);

        $datetime = new DateTime();

        $todayDate = $datetime->format('Y-m-d');

        var_dump($moadian->inquiryByUId([$uid],
                        $todayDate . 'T00:00:00.000000000+03:30',
                        $todayDate . 'T23:59:59.123456789+03:30'
        ));
    }
} catch (\Exception $ex) {

    echo 'ERROR: Cannot send invoice';
    echo "\r\n";
    var_dump($ex->getMessage());
}
