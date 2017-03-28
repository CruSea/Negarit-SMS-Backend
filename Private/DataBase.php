<?php
require_once "DB_Access.php";
abstract class userTable {
    const ID = 'userID';
    const USER_NAME = 'userName';
    const USER_PASS = 'userPass';
    const USER_EMAIL = 'userEmail';
    const USER_FullName = 'userFullName';
    const USER_UPDATE = 'update';
}
abstract class companyTable {
    const ID = 'ID';
    const NAME = 'Name';
    const DESCRIPTION = 'Description';
    const ISACTIVE = 'isActive';
}
abstract class deviceTable {
    const ID = 'ID';
    const DEVICE_NAME = 'deviceName';
    const DEVICE_DESCRIPTION = 'deviceDescription';
    const DEVICE_PASS = 'devicePass';
    const DEVICE_PHONE = 'devicePhone';
    const UPDATED_DATE = 'updatedDate';
}
abstract class userROLE {
    const SUPER_ADMIN = '1';
    const ADMIN = '2';
    const EDITOR = '3';
    const VIEWER = '4';
    const NOTHING = '5';
}
abstract class roleTable{
    const ID = 'ID';
    const SUPER_ADMIN = 'superAdmin';
    const ADMIN = 'admin';
    const EDITOR = 'editor';
    const VIEWER = 'viewer';
    const NOTHING = 'nothing';
}
abstract class messageTable {
    const MESSAGE_ID = 'messageID';
    const MESSAGE_FROM = 'messageFrom';
    const MESSAGE_CONTENT = 'message';
    const MESSAGE_SENT_TO = 'messageSentTo';
    const MESSAGE_DEVICE_ID = 'deviceID';
    const MESSAGE_IS_DELIVERED = 'delivered';
    const MESSAGE_IS_OUTGOING = 'outgoingSMS';
    const MESSAGE_UPDATE_DATE = 'updatedDate';
}
abstract class sendingSMS {
    const MessageTo = 'to';
    const Message = 'message';
    const UUID = 'uuid';
}
class DataBase{
    private $connection;
    private $database;
    public function __construct(){
        $this->database = new DB_Access();
        $this->connection = $this->database->getConnection();
        $this->connection->query("USE Negarit");
    }
    private function logException($exceptions){
//        print_r("Exception found!! \n");
//        print_r($exceptions);
    }
    public function addUser($data){
        try {
            $sql = "INSERT INTO Users(userName, userPass, userEmail, FullName, updatedDate) VALUES(:Uname, password(:Upass), :Uemai, :Ufull, :Uupda)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $data[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $data[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $data[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->bindvalue(':Ufull', $data[userTable::USER_FullName], PDO::PARAM_INT);
            $stmt->bindvalue(':Uupda', $data[userTable::USER_UPDATE], PDO::PARAM_STR);
            $stmt->execute();
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function checkUser($data){
        try {
            $sql = "SELECT * FROM Users WHERE userPass= password(:Upass) AND (userName= :Uname OR userEmail= :Uemai)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $data[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $data[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $data[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                return $row;
            }
            return null;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getAllUsers(){
        try {
            $sql = "SELECT * FROM Users ORDER  BY id";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }

    public function addMessage($messageData){
        try {
            $sql = "INSERT INTO Messages(messageID, messageFrom, message, messageSentTo, deviceID, delivered, outgoingSMS, updatedDate) VALUES(:Mesid, :Mfrom, :Mmess, :Msent, :Mdevc, :Mdeli, :Moutg, :Mupd)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Mesid', $messageData[messageTable::MESSAGE_ID], PDO::PARAM_STR);
            $stmt->bindvalue(':Mfrom', $messageData[messageTable::MESSAGE_FROM], PDO::PARAM_STR);
            $stmt->bindvalue(':Mmess', $messageData[messageTable::MESSAGE_CONTENT], PDO::PARAM_STR);
            $stmt->bindvalue(':Msent', $messageData[messageTable::MESSAGE_SENT_TO], PDO::PARAM_INT);
            $stmt->bindvalue(':Mdevc', $messageData[messageTable::MESSAGE_DEVICE_ID], PDO::PARAM_STR);
            $stmt->bindvalue(':Mdeli', $messageData[messageTable::MESSAGE_IS_DELIVERED], PDO::PARAM_STR);
            $stmt->bindvalue(':Moutg', $messageData[messageTable::MESSAGE_IS_OUTGOING], PDO::PARAM_STR);
            $stmt->bindvalue(':Mupd', $messageData[messageTable::MESSAGE_UPDATE_DATE], PDO::PARAM_STR);
            $stmt->execute();
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function addNewMessage($messageData,$deviceData){
        try {
            $sql = "INSERT INTO Messages(messageID, messageFrom, message, messageSentTo, deviceID, delivered, outgoingSMS, updatedDate) VALUES(:Mesid, :Mfrom, :Mmess, :Msent, :Mdevc, :Mdeli, :Moutg, :Mupd)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Mesid', $messageData[messageTable::MESSAGE_ID], PDO::PARAM_STR);
            $stmt->bindvalue(':Mfrom', $messageData[messageTable::MESSAGE_FROM], PDO::PARAM_STR);
            $stmt->bindvalue(':Mmess', $messageData[messageTable::MESSAGE_CONTENT], PDO::PARAM_STR);
            $stmt->bindvalue(':Msent', $messageData[messageTable::MESSAGE_SENT_TO], PDO::PARAM_INT);
            $stmt->bindvalue(':Mdevc', $deviceData[deviceTable::ID], PDO::PARAM_STR);
            $stmt->bindvalue(':Mdeli', $messageData[messageTable::MESSAGE_IS_DELIVERED], PDO::PARAM_STR);
            $stmt->bindvalue(':Moutg', $messageData[messageTable::MESSAGE_IS_OUTGOING], PDO::PARAM_STR);
            $stmt->bindvalue(':Mupd', $messageData[messageTable::MESSAGE_UPDATE_DATE], PDO::PARAM_STR);
            $stmt->execute();
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getSentMessages($data){
        try {
            $sql = "SELECT * FROM Messages WHERE outgoingSMS= 0 AND deviceID= :Mdevc";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Mdevc', $data[messageTable::MESSAGE_DEVICE_ID], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getToSentMessages($data){
        try {
            $sql = "SELECT * FROM Messages WHERE outgoingSMS= 1 AND deviceID= :Mdevc AND delivered= 0";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Mdevc', $data[messageTable::MESSAGE_DEVICE_ID], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getPendingDevice(){
        try {
            $sql = "SELECT * FROM Messages WHERE outgoingSMS= 1 AND delivered= 0";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                $device = $this->getDeviceByID($row["deviceID"]);
                return $device;
            }
            return null;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getMessagesByCompany($userData){
        try {
            $sql = "SELECT * FROM Messages WHERE deviceID IN (SELECT DeviceID FROM CompanyDevices WHERE CompanyID = :Cid)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Cid', $userData[companyTable::ID], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $device = array();
                $device[deviceTable::ID] = $row[messageTable::MESSAGE_DEVICE_ID];
                $row['Device'] = $this->getDeviceByID($device);
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getMessagesByUser($userData){
        try {
            $sql = "SELECT * FROM Messages WHERE deviceID IN (SELECT DeviceID FROM CompanyDevices WHERE CompanyID IN (SELECT CompanyID FROM CompanyUsers WHERE UserID IN (SELECT ID FROM Users WHERE userPass= password(:Upass) AND (userName= :Uname OR userEmail= :Uemai))))";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $userData[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $userData[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $userData[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $device = array();
                $device[deviceTable::ID] = $row[messageTable::MESSAGE_DEVICE_ID];
                $row['Device'] = $this->getDeviceByID($device);
                $row['Company'] = $this->getCompanyByDevice($device);
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getSentMessagesByUser($userData){
        try {
            $sql = "SELECT * FROM Messages WHERE delivered = 1 AND outgoingSMS = 1 AND deviceID IN (SELECT DeviceID FROM CompanyDevices WHERE CompanyID IN (SELECT CompanyID FROM CompanyUsers WHERE UserID IN (SELECT ID FROM Users WHERE userPass= password(:Upass) AND (userName= :Uname OR userEmail= :Uemai))))";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $userData[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $userData[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $userData[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $device = array();
                $device[deviceTable::ID] = $row[messageTable::MESSAGE_DEVICE_ID];
                $row['Device'] = $this->getDeviceByID($device);
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getPendingMessagesByUser($userData){
        try {
            $sql = "SELECT * FROM Messages WHERE delivered = 0 AND outgoingSMS = 1 AND deviceID IN (SELECT DeviceID FROM CompanyDevices WHERE CompanyID IN (SELECT CompanyID FROM CompanyUsers WHERE UserID IN (SELECT ID FROM Users WHERE userPass= password(:Upass) AND (userName= :Uname OR userEmail= :Uemai))))";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $userData[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $userData[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $userData[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getPendingSMSByDeviceID($data){
        try {
            $sql = "SELECT * FROM Messages WHERE outgoingSMS= 1 AND delivered= 0 AND deviceID= :Did";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Did', $data[deviceTable::ID], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $set[] = $this->getMessage($row);
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function deleteMessageByMessageIDDeviceID($data){
        try {
            $sql = "DELETE FROM Messages WHERE messageID = :Mid AND deviceID = :Did";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Mid', $data[messageTable::MESSAGE_ID], PDO::PARAM_STR);
            $stmt->bindvalue(':Did', $data[messageTable::MESSAGE_DEVICE_ID], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return TRUE;
            }
            return FALSE;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getMessage($data){
        $sms = array();
        $sms[sendingSMS::MessageTo] = $data[messageTable::MESSAGE_SENT_TO];
        $sms[sendingSMS::Message] = $data[messageTable::MESSAGE_CONTENT];
        $sms[sendingSMS::UUID] = $data[messageTable::MESSAGE_ID];
        return $sms;
    }




    


    /// Company Table

    public function addCompany($data){
        try {
            $sql = "INSERT INTO Company(CompanyName, Description, isActive) VALUES(:Cname, :Cdesc, :Cactv)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Cname', $data[companyTable::NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Cdesc', $data[companyTable::DESCRIPTION], PDO::PARAM_STR);
            $stmt->bindvalue(':Cactv', $data[companyTable::ISACTIVE], PDO::PARAM_STR);
            $stmt->execute();
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getCompanyByDevice($userData){
        try {
            $sql = "SELECT * FROM Company WHERE ID IN (SELECT CompanyID FROM CompanyDevices WHERE DeviceID = :Did)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Did', $userData[deviceTable::ID], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $set = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getAll_ByUser($userData){
        try {
            $sql = "SELECT * FROM Company WHERE ID IN (SELECT CompanyID FROM CompanyUsers WHERE UserID IN (SELECT ID FROM Users WHERE userPass= password(:Upass) AND (userName= :Uname OR userEmail= :Uemai)))";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $userData[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $userData[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $userData[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $company = array();
                $company[companyTable::ID] = $row[companyTable::ID];
                $row['Devices'] = $this->getDeviceByCompany($company);
                $row['Messages'] = $this->getMessagesByCompany($company);
                $row['Users'] = $this->getMessagesByCompany($company);
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getCompaniesByUser($userData){
        try {
            $sql = "SELECT * FROM Company WHERE ID IN (SELECT CompanyID FROM CompanyUsers WHERE UserID IN (SELECT ID FROM Users WHERE userPass= password(:Upass) AND (userName= :Uname OR userEmail= :Uemai)))";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $userData[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $userData[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $userData[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $company = array();
                $company[companyTable::ID] = $row[companyTable::ID];
                $row['Devices'] = $this->getDeviceByCompany($company);
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getCompaniesDevicesByUser($userData){
        try {
            $sql = "SELECT * FROM Company WHERE ID IN (SELECT CompanyID FROM CompanyUsers WHERE UserID IN (SELECT ID FROM Users WHERE userPass= password(:Upass) AND (userName= :Uname OR userEmail= :Uemai)))";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $userData[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $userData[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $userData[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->execute();
            $set = array();
            while ($row = $stmt->fetch()) {
                $set[] = $row;
            }
            return $set;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }

    /// Company User Table

    public function addCompanyUser($data){
        try {
            $sql = "INSERT INTO CompanyUsers(CompanyID, UserID) VALUES(:Cid, :Uid)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Cid', $data[companyTable::ID], PDO::PARAM_STR);
            $stmt->bindvalue(':Uid', $data[userTable::ID], PDO::PARAM_STR);
            $stmt->execute();
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }

    
    /// User Company Role

    public function addUserCompanyRole($data){
        try {
            $sql = "INSERT INTO UserCompanyRole(CompanyID, UserID, RoleID) VALUES(:Cid, :Uid, :Rid)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Cid', $data[companyTable::ID], PDO::PARAM_STR);
            $stmt->bindvalue(':Uid', $data[userTable::ID], PDO::PARAM_STR);
            $stmt->bindvalue(':Rid', $data[roleTable::ID], PDO::PARAM_STR);
            $stmt->execute();
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }

    /// Devices Table

    public function addNewDevice($data){
        try {
            $sql = "INSERT INTO Devices(DeviceName, DeviceDescription, DevicePass, DevicePhone) VALUES(:Dname, :Ddesc, :Dpass, :Dphon)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Dname', $data[deviceTable::DEVICE_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Ddesc', $data[deviceTable::DEVICE_DESCRIPTION], PDO::PARAM_STR);
            $stmt->bindvalue(':Dpass', $data[deviceTable::DEVICE_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Dphon', $data[deviceTable::DEVICE_PHONE], PDO::PARAM_STR);
            $stmt->execute();
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getDeviceByID($data){
        try {
            $sql = "SELECT * FROM Devices WHERE ID= :Did";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Did', $data[deviceTable::ID], PDO::PARAM_STR);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                return $row;
            }
            return null;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getDevicesByUser($userData){
        try {
            $sql = "SELECT * FROM Devices WHERE ID IN (SELECT DeviceID FROM CompanyDevices WHERE CompanyID IN (SELECT CompanyID FROM CompanyUsers WHERE UserID IN (SELECT ID FROM Users WHERE userPass = password(:Upass) AND (userName = :Uname OR userEmail = :Uemai))))";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $userData[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $userData[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $userData[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->execute();
            $found = array();
            while ($row = $stmt->fetch()) {
                $found[] = $row;
            }
            return $found;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getDeviceByUserDeviceName($userData,$deviceData){
        try {
            $sql = "SELECT * FROM Devices WHERE DeviceName = :Dname AND ID IN (SELECT DeviceID FROM CompanyDevices WHERE CompanyID IN (SELECT CompanyID FROM CompanyUsers WHERE UserID IN (SELECT ID FROM Users WHERE userPass = password(:Upass) AND (userName = :Uname OR userEmail = :Uemai))))";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Uname', $userData[userTable::USER_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Upass', $userData[userTable::USER_PASS], PDO::PARAM_STR);
            $stmt->bindvalue(':Uemai', $userData[userTable::USER_EMAIL], PDO::PARAM_STR);
            $stmt->bindvalue(':Dname', $deviceData[deviceTable::DEVICE_NAME], PDO::PARAM_STR);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                return $row;
            }
            return null;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getDeviceByCompany($data){
        try {
            $sql = "SELECT * FROM Devices WHERE ID IN (SELECT DeviceID FROM CompanyDevices WHERE CompanyID = :Cid)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Cid', $data[companyTable::ID], PDO::PARAM_STR);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                return $row;
            }
            return null;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    public function getDeviceByNameAndPass($data){
        try {
            $sql = "SELECT * FROM Devices WHERE devicePass= :Dpass AND deviceName= :Dname";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindvalue(':Dname', $data[deviceTable::DEVICE_NAME], PDO::PARAM_STR);
            $stmt->bindvalue(':Dpass', $data[deviceTable::DEVICE_PASS], PDO::PARAM_STR);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                return $row;
            }
            return null;
        } catch (PDOException $e) {
            $this->logException($e);
            return null;
        }
    }
    
}