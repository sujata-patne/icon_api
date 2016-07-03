<?php

class Message {

    const SUCCESS = 'SUCCESS';
    const ERROR = 'ERROR';
    const ERROR_INVAID_REQUEST_BODY = 'Invalid request body';
    const ERROR_NO_DB_CONNECTION = 'Unable to obtain db connection';
    const ERROR_VALIDATION_FAILED = 'Validation failed';
    const ERROR_DB_OPERATION_FAILED = 'Database operation failed.';
    const ERROR_INVALID_USER_ID = "Invalid User Id";
    const ERROR_INVALID_SESSION_KEY = "Session Expired, Please log again!.";
    const ERROR_BAD_REQUEST = 'Bad Request';
    const ERROR_DEVICE_LOAD_FAILED = 'Failed to load device info';
    const ERROR_SESSION_EXPIRED = "Session Expired, Please log again!.";
    const ERROR_FORBIDDEN_ACCESS = "Forbidden Access";
    const ERROR_INVALID_USER_PASSWORD = "Invalid user password.";
    const ERROR_INVALID_SUBSCRIPTION = "User not subscribed";
    const ERROR_PERMISSION_DENIED = "Permission Denied";
    const ERROR_INVALID_DEVICE_ID = 'Invalid Device Id!.';
    const ERROR_EMPTY_DEVICE_ID = 'Device Id can not be empty!.';
    const ERROR_PAGE_NOT_FOUND = 'The requested page route does not exist';
    const ERROR_BLANK_PAGENAME = 'Page name is required !.';
    const ERROR_BLANK_PAGE_ID = 'Page Id is required !.';
    const ERROR_BLANK_PAGE_TITLE = 'Page Title is required !.';
    const ERROR_BLANK_STORE_ID = 'Store Id is required !.';
    const ERROR_BLANK_DEVICE_SIZE_ID = 'Device Size is required !.';
    const ERROR_BLANK_EVENT_ID = 'EventId / PricePoint is required !.';
    const ERROR_BLANK_DEVICE_HEIGHT = 'Device height is required !.';
    const ERROR_BLANK_DEVICE_WIDTH = 'Device width is required !.';
    const ERROR_STORE_LOAD = 'Failed to load store info';
    const ERROR_PACKAGE_LOAD = 'Failed to load package info';
    const ERROR_BLANK_LIMIT = 'Content limit is required !.';
    const ERROR_BLANK_CONTENT_TYPE = 'Content type is required !.';
    const ERROR_BLANK_PACKAGE_ID = 'Package Id is required !.';
    const ERROR_CAMPAIGN_LOAD = 'Failed to load package info';
    const ERROR_BLANK_PROMO_ID = 'PromoId is required !.';
    const ERROR_BLANK_OPERATOR_ID = 'Operator Id is required !.';
    const ERROR_BLANK_MSISDN = 'MSISDN is required !.';
    const ERROR_BLANK_SEARCHKEY = 'Search Key is required !.';
    const ERROR_BLANK_USER_ID = 'User Id is required !.';
    const ERROR_BLANK_APP_ID = 'App Id is required !.';
    const ERROR_BLANK_CMD_ID = 'Content Metadata Id is required !.';
    const ERROR_BLANK_CD_ID = 'cd_id is required !.';
    const ERROR_BLANK_SUB_START_DATE = 'Subscription Start Date is required !.';
    const ERROR_BLANK_CD_DOWNLOAD_COUNT = 'Download Count is required !.';
    const ERROR_BLANK_CD_DOWNLOAD_DATE = 'Download Date is required !.';
    const ERROR_BLANK_SINGLE_DAY_LIMIT = 'Single Day limit is required !.';

    const ERROR_BLANK_CF_ID = 'cf_id is required !.';
    const ERROR_BLANK_CF_BASE_URL = 'cf_url_base is required !.';
    const ERROR_BLANK_CF_URL = 'cf_url is required !.';
    const ERROR_BLANK_CF_TEMPLATE_ID = 'cf_template_id is required !.';
    const ERROR_BLANK_CF_USER_NAME = 'cf_name is required !.';
    const ERROR_BLANK_CF_USER_ALIAS = 'cf_name_alias is required !.';
    const ERROR_BLANK_VCODE = 'vcode is required !.';
    const ERROR_BLANK_VO_ID = 'vo_id is required !.';

    public $message = '';
    public $type = '';

    public static function successMessage($messageText) {
        $msg = new Message();
        $msg->message = $messageText;
        $msg->type = Message::SUCCESS;

        return $msg;
    }

    public static function errorMessage($messageText) {
        $msg = new Message();
        $msg->message = $messageText;
        $msg->type = Message::ERROR;

        return $msg;
    }

}
