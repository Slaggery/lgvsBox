<?php
header('Content-type:application/json;charset=utf-8');
include($_SERVER["DOCUMENT_ROOT"] . '/local/lib/apiBX.php');
include($_SERVER["DOCUMENT_ROOT"] . '/local/lib/callURL.php');


class ExchangeOdinS
{
    public static function createRestMethods()
    {
        return array(
            'lgvsExchange' => array(
                'lgvs.exchange.add.company' => array(
                    'callback' => array(__CLASS__, 'addCompany'),
                    'options' => array(),
                ),
                'lgvs.exchange.add.user' => array(
                    'callback' => array(__CLASS__, 'addUser'),
                    'options' => array(),
                ),
                'lgvs.exchange.get.company' => array(
                    'callback' => array(__CLASS__, 'getCompany'),
                    'options' => array(),
                ),
                'lgvs.exchange.add.region' => array(
                    'callback' => array(__CLASS__, 'addRegion'),
                    'options' => array(),
                ),
                'lgvs.exchange.get.leads' => array(
                    'callback' => array(__CLASS__, 'getLeads'),
                    'options' => array(),
                ),
                'lgvs.exchange.add.invoice' => array(
                    'callback' => array(__CLASS__, 'addInvoice'),
                    'options' => array(),
                ),
                'lgvs.exchange.get.deals' => array(
                    'callback' => array(__CLASS__, 'getDeals'),
                    'options' => array(),
                ),
            ),
        );
    }

    public static function getDeals($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/getDeals.php');

        return Deals::whatDoDeals($query);
    }


    public static function addInvoice($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/addInvoice.php');

        return Invoice::whatDoInvoice($query);
    }

    public static function getLeads($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/getLeads.php');

        return Leads::whatDoLeads();
    }

    public static function addUser($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/addUser.php');

        return User::whatDoUser($query);
    }

    public static function addRegion($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/addRegion.php');

        return Region::whatDoRegion($query);
    }

    public static function addCompany($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/addCompany.php');

        return Company::whatDoCompany($query);
    }

    public static function getCompany($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/getCompany.php');

        return Company::getCompany();
    }
}
