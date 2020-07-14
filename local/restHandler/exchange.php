<?php
header('Content-type:application/json;charset=utf-8');
include ($_SERVER["DOCUMENT_ROOT"] . '/local/lib/apiBX.php');

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
            ),
        );
    }

    public static function addUser($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/addUser.php');

        return User::whatDoUser($query);
    }

    public static function addCompany($query, $n, \CRestServer $server)
    {
        if ($query['error']) throw new \Bitrix\Rest\RestException('Global Error', 'ERR_GLOBAL', \CRestServer::STATUS_PAYMENT_REQUIRED);
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restApi/addCompany.php');

        return Company::whatDoCompany($query);
    }
}
