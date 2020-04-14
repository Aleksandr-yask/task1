<?php

use MVC\Model;

class ModelsContacts extends Model
{

    /**
     * add contact
     *
     * @param $name
     * @param $phone
     * @param $email
     * @param $sourceId
     * @param $time
     * @return bool
     */
    public function addContact(string $name, int $phone, string $email, int $sourceId, int $time): bool
    {
        $sql = "INSERT INTO contacts (name, phone, email, source_id, add_time) 
                VALUES (:name, :phone, :email, :sourceId, :time)";
        $res = $this->db->query($sql, [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'sourceId' => $sourceId,
            'time' => $time
            ]);
        return $res ? true : false;
    }

    /**
     * checks if a contact has been added in the last 24 hours
     *
     * @param int $phone
     * @param int $sourceId
     * @param int $time
     * @return bool
     */
    public function isContactAdded(int $phone, int $sourceId, int $time): bool
    {
        $sql = "select id from contacts where phone = :phone and source_id = :sId 
                         and (:time - add_time) < 84000";
        $res = $this->db->column($sql, [
            'phone' => $phone,
            'sId' => $sourceId,
            'time' => $time
        ]);

        return $res === false ? false : true;
    }

    public function getByNumber(int $num)
    {
        return $this->db->row("select * from contacts where phone = :phone", ['phone' => $num]);
    }

}