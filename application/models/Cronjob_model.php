<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Cronjob_model extends CI_Model
{
  private static $table         = 'm_employees';
  private static $tableBot      = 'tr_bot_message';
  private static $tableSetting  = 'tr_setting_bot';

  public function __construct()
  {
    parent::__construct();
  }

  public function getEmployee()
  {
    $this->db->where('telegram_id !=', null);
    $this->db->where('is_delete =', 0);
    return $this->db->get(self::$table);
  }

  public function insertTBot($data)
  {
    $this->db->insert(self::$tableBot, $data);
    return $this->db->affected_rows();
  }

  public function getSettingRow($data)
  {
    return $this->db->get_where(self::$tableSetting, $data);
  }

  public function getMessage($data)
  {
    return $this->db->get_where(self::$tableBot, $data);
  }

  public function updateMessage($data, $where)
  {
    $this->db->update(self::$tableBot, $data, $where);
    return $this->db->affected_rows();
  }
}
