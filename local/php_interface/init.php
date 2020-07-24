<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restHandler/exchange.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/local/restHandler/events.php');

AddEventHandler('rest', 'OnRestServiceBuildDescription', array('\ExchangeOdinS', 'createRestMethods'));
AddEventHandler("crm", 'OnBeforeCrmCompanyUpdate', array('\Events', 'updateCompany'));
AddEventHandler("crm", 'OnAfterRequisiteUpdate', array('\Events', 'updateRequisite'));
AddEventHandler("crm", 'OnAfterCrmDealUpdate', array('\Events', 'updateDeal'));
AddEventHandler('main', 'OnEpilog', array('\Events', 'disabler_ui'));
