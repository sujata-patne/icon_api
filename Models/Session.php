<?php

namespace VOP\Models;

use VOP\Utils\PdoUtils;
use VOP\Utils\UuidUtils;
use VOP\Utils\Logger;
use VOP\Daos\SessionDao;
use Respect\Validation\Validator as v;

class Session extends BaseModel {

    public $sessionId;
    public $clientId;
    public $userId;
    
    public function __construct(){
        
    }

    public  function generateSessionId() {
        $udid = UuidUtils::uuid();

        $newUuid = str_replace('-', '', $udid);

        return $newUuid;
    }

    public  function generateClientId() {
        $udid = UuidUtils::uuid();
        $newUuid = str_replace('-', '', $udid);

        return $newUuid;
    }

    public  function sessionBySessionId($sessionId) {
        $dbConnection = PdoUtils::obtainConnection();

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $session = null;

        try {
            $sessionDao = new SessionDao($dbConnection);
            $session = $sessionDao->sessionBySessionId($sessionId);

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            Logger::logException($e);
        }

        PdoUtils::closeConnection($dbConnection);
        return $session;
    }

    public  function sessionByClientId($clientId) {
        $dbConnection = PdoUtils::obtainConnection();

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $session = null;

        try {
            $sessionDao = new sessionDao($dbConnection);
            $session = $sessionDao->sessionByClientId($clientId);

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            Logger::logException($e);
        }

        PdoUtils::closeConnection($dbConnection);
        return $session;
    }

    public function deleteSession() {

        $dbConnection = PdoUtils::obtainConnection();

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $result = false;

        try {
            $sessionDao = new SessionDao($dbConnection);
            $result = $sessionDao->deleteSession($this->sessionId);

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            Logger::logException($e);
        }

        PdoUtils::closeConnection($dbConnection);
        return $result;
    }

    public function save() {
        $dbConnection = PdoUtils::obtainConnection();

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $result = false;

        try {
            $sessionDao = new SessionDao($dbConnection);
            $result = $sessionDao->save($this);

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            Logger::logException($e);
        }

        PdoUtils::closeConnection($dbConnection);
        return $result;
    }

    public function validateBeforeSave() {
        $sessionValidator = v::attribute('sessionId', v::string()->length(1, 32))
                ->attribute('userId', v::string()->length(1, 34))
                ->setName('SessionValidator');

        try {
            $sessionValidator->assert($this);
            return Message::SUCCESS;
        } catch (\Exception $ex) {

            if (method_exists($ex, 'findMessages')) {
                $result = $ex->findMessages(array('sessionId', 'userId'));
                return $result;
            }

            return Message::ERROR_VALIDATION_FAILED;
        }
    }

    public function getSessionByUser() {
        $dbConnection = PdoUtils::obtainConnection('CMS');
        
        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $session = null;

        try {
            $sessionDao = new SessionDao($dbConnection);
            $session = $sessionDao->getSessionByUser($this);

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            Logger::logException($e);
        }

        PdoUtils::closeConnection($dbConnection);
        return $session;
    }

    public function update() {
        $dbConnection = PdoUtils::obtainConnection();

        if ($dbConnection == null) {
            return Message::ERROR_NO_DB_CONNECTION;
        }

        $dbConnection->beginTransaction();

        $result = false;

        try {
            $sessionDao = new SessionDao($dbConnection);
            $result = $sessionDao->update($this);

            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollBack();
            Logger::logException($e);
        }

        PdoUtils::closeConnection($dbConnection);
        return $result;
    }

}
