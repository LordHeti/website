<?php
namespace Destiny\Api;

use Destiny\Common\Application;
use Destiny\Common\Utils\Date;
use Destiny\Common\Service;
use Doctrine\DBAL\DBALException;

/**
 * @method static ApiAuthenticationService instance()
 */
class ApiAuthenticationService extends Service {
    
    /**
     * @var string
     */
    protected $authTokenSalt = '_r52Ax_';

    /**
     * @param int $userId
     * @return string
     * @throws DBALException
     */
    public function createAuthToken($userId) {
        $token = md5 ( $this->authTokenSalt . microtime ( true ) . $userId );
        if ($this->getAuthTokenExists ( $token )) {
            $token = $this->createAuthToken ( $userId );
        }
        return $token;
    }

    /**
     * @param int $userId
     * @param string $authToken
     * @throws DBALException
     */
    public function addAuthToken($userId, $authToken) {
        $conn = Application::getDbConn();
        $conn->insert ( 'dfl_users_auth_token', array (
            'userId' => $userId,
            'authToken' => $authToken,
            'createdDate' => Date::getDateTime ( 'NOW' )->format ( 'Y-m-d H:i:s' ) 
        ) );
    }

    /**
     * @param int $id
     * @throws DBALException
     */
    public function removeAuthToken($id) {
        $conn = Application::getDbConn();
        $conn->delete ( 'dfl_users_auth_token', array (
            'authTokenId' => $id 
        ) );
    }

    /**
     * @param int $userId
     * @param int $limit
     * @param int $start
     * @return array
     * @throws DBALException
     */
    public function getAuthTokensByUserId($userId, $limit = 5, $start = 0) {
        $conn = Application::getDbConn();
        $stmt = $conn->prepare ( '
            SELECT * FROM dfl_users_auth_token WHERE userId = :userId
            ORDER BY createdDate DESC
            LIMIT :start,:limit
        ' );
        $stmt->bindValue ( 'userId', $userId, \PDO::PARAM_INT );
        $stmt->bindValue ( 'start', $start, \PDO::PARAM_INT );
        $stmt->bindValue ( 'limit', $limit, \PDO::PARAM_INT );
        $stmt->execute ();
        return $stmt->fetchAll ();
    }

    /**
     * @param string $authToken
     * @return array
     * @throws DBALException
     */
    public function getAuthToken($authToken) {
        $conn = Application::getDbConn();
        $stmt = $conn->prepare ( 'SELECT * FROM dfl_users_auth_token WHERE authToken = :authToken LIMIT 0,1' );
        $stmt->bindValue ( 'authToken', $authToken, \PDO::PARAM_INT );
        $stmt->execute ();
        return $stmt->fetch ();
    }

    /**
     * Check if an auth token exists
     *
     * @param string $authToken
     * @return boolean
     * @throws DBALException
     */
    public function getAuthTokenExists($authToken) {
        $conn = Application::getDbConn();
        $stmt = $conn->prepare ( 'SELECT COUNT(*) FROM dfl_users_auth_token WHERE authToken = :authToken LIMIT 0,1' );
        $stmt->bindValue ( 'authToken', $authToken, \PDO::PARAM_INT );
        $stmt->execute ();
        return ($stmt->fetchColumn () == 1) ? true : false;
    }

    /**
     * @param int $userId
     * @param int $limit
     * @param int $start
     * @return array
     * @throws DBALException
     */
    public function getAuthSessionsByUserId($userId, $limit = 5, $start = 0) {
        $conn = Application::getDbConn();
        $stmt = $conn->prepare ( '
            SELECT * FROM dfl_users_auth WHERE userId = :userId
            ORDER BY createdDate DESC
            LIMIT :start,:limit
        ' );
        $stmt->bindValue ( 'userId', $userId, \PDO::PARAM_INT );
        $stmt->bindValue ( 'start', $start, \PDO::PARAM_INT );
        $stmt->bindValue ( 'limit', $limit, \PDO::PARAM_INT );
        $stmt->execute ();
        return $stmt->fetchAll ();
    }

    /**
     * @param int $userId
     * @param string $authProfile
     * @throws DBALException
     */
    public function deleteAuthProfileByUserId($userId, $authProfile) {
        $conn = Application::getDbConn();
        $conn->delete ( 'dfl_users_auth', array (
            'userId' => $userId,
            'authProvider' => $authProfile,
        ) );
    }

}