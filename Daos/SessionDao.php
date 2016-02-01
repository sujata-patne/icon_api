<?php

namespace VOP\Daos;

use VOP\Models\Session;
use PDO;

class SessionDao extends BaseDao {

    public function __construct($dbConn) {
        parent::__construct($dbConn);
    }

    private function sessionFromRow($row) {
        $session = new Session();

        $session->sessionId = $row['cd_id'];
        $session->userId = $row['cd_name'];
        //$session->clientId = $row['client_id'];
        //$session->fbAccessToken = $row['fb_access_token'];
        //$session->fbExchangeToken = $row['fb_exchange_token'];
        //$this->ccuuFromRow($session, $row);

        return $session;
    }

    public function sessionBySessionId($sessionId) {
        $query = "SELECT * FROM sessions WHERE session_id = :session_id";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':session_id', $sessionId);
        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $session = null;

        if (($row = $statement->fetch()) != FALSE) {
            $session = $this->sessionFromRow($row);
        }
        return $session;
    }

    public function sessionByClientId($clientId) {
        $query = "SELECT * FROM sessions WHERE client_id = :client_id";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':client_id', $clientId);
        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $session = null;

        if (($row = $statement->fetch()) != FALSE) {
            $session = $this->sessionFromRow($row);
        }

        return $session;
    }

    public function deleteSession($sessionId) {
        $query = "DELETE FROM sessions WHERE session_id = :session_id";
        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':session_id', $sessionId);
        $result = $statement->execute();

        return $result && ($statement->rowCount() == 1);
    }

    /* public function save($session) {
      $query =	"INSERT INTO sessions (
      session_id, user_id, client_id,
      fb_access_token, fb_exchange_token,
      created_at, created_by,
      updated_at, updated_by
      ) VALUES (
      :session_id, :user_id, :client_id,
      :fb_access_token, :fb_exchange_token,
      :created_at, :created_by,
      :updated_at, :updated_by
      );";

      $statement = $this->dbConnection->prepare($query);
      $statement->bindParam(':session_id', $session->sessionId);
      $statement->bindParam(':user_id', $session->userId);
      $statement->bindParam(':client_id', $session->clientId);
      $statement->bindParam(':fb_access_token', $session->fbAccessToken);
      $statement->bindParam(':fb_exchange_token', $session->fbExchangeToken);

      $this->bindCCUU($statement, $session);

      $result = $statement->execute();

      return $result && ($statement->rowCount() == 1);
      } */

    public function save($session) {
        $query = "INSERT INTO sessions (
                                            session_id, client_id, user_id,
                                            created_at, created_by,
                                            updated_at, updated_by
                                    ) VALUES (
                                            :session_id, :client_id, :user_id,
                                            :created_at, :created_by,
                                            :updated_at, :updated_by
                                    );";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':session_id', $session->sessionId);
        $statement->bindParam(':client_id', $session->clientId);
        $statement->bindParam(':user_id', $session->userId);
        // $statement->bindParam(':fb_access_token', $session->fbAccessToken);
        //$statement->bindParam(':fb_exchange_token', $session->fbExchangeToken);

        $this->bindCCUU($statement, $session);

        $result = $statement->execute();

        return $result && ($statement->rowCount() == 1);
    }

    public function getSessionByUser($session_obj) {
    	
    	$query = "SELECT * FROM catalogue_detail";
    	$clientId = 715;
    	
    	$query = "SELECT * FROM catalogue_detail WHERE cd_id = :client_id";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':client_id', $clientId);
        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $session = null;

        if (($row = $statement->fetch()) != FALSE) {
            $session = $this->sessionFromRow($row);
        }
        
        print_r( $session );
        exit;

        return $session;
    }

    public function update($session_obj) {
        $query = "UPDATE sessions
                        SET
                            session_id = :session_id
                        WHERE
                            user_id = :user_id";

        $statement = $this->dbConnection->prepare($query);
        $statement->bindParam(':session_id', $session_obj->sessionId);
        $statement->bindParam(':user_id', $session_obj->userId);

        $result = $statement->execute();

        return $result && ($statement->rowCount() == 1);
    }

}
