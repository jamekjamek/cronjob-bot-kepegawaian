<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Cronjob extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Cronjob_model', 'CRONJOB');
  }

  public function cronjobcreate()
  {
    $employee = $this->CRONJOB->getEmployee()->result();
    $insert   = 0;
    foreach ($employee as $em) {
      $dataInsert = [
        'employee_id' => $em->id,
        'employee_name' => $em->full_name,
        'telegram_id' => $em->telegram_id,
        'setting_id'  => null,
        'days'        => $this->hari_ini(),
        'waktu_kirim_manual' => null,
      ];
      $insert += $this->CRONJOB->insertTBot($dataInsert);
    }
    if ($insert > 0) {
      $apilink  = $this->apiTelegram();
      $telegramId = '1374218169';
      $telegram = @file_get_contents($apilink . "sendmessage?chat_id=" . $telegramId . "&text=Berhasil menyimpan " . $insert . " Data di Bot Message dari " . count($employee) . " Pegawai yang terdaftar&parse_mode=HTML");
      $telegram = json_decode($telegram, TRUE);
    }
  }

  public function absenpagi()
  {
    $hariini    = $this->hari_ini();
    $cekSetting = $this->CRONJOB->getSettingRow(['days' => $hariini, 'is_active =' => '1']);
    if ($cekSetting->num_rows() > 0) {
      $jamsekarang  = date('H:i:s', time() + 60 * 60);
      $jamsekarang  = strtotime($jamsekarang);
      // if()
      $jampagi      = $cekSetting->row()->jam_datang;
      $jampagi      = strtotime($jampagi);
      $jampagiplus1 = strtotime($jampagi) + 60;

      if ($jamsekarang === $jampagi || $jamsekarang > $jampagi) {
        if ($jamsekarang < $jampagiplus1) {
          $cekMessage     = $this->CRONJOB->getMessage(['days' => $hariini, 'waktu_kirim_manual =' => null])->result();
          $update     = 0;
          foreach ($cekMessage as $message) {
            $pesan    = "Selamat Pagi Bapak/Ibu <b>" . $message->employee_name . "</b>, <br> Jangan Lupa untuk presensi kehadiran di pagi hari ini <b>" . $hariini . " - " . tgl_indo(date('Y-m-d')) . "</b>, 
            <br><b> Abaikan Pesan ini Jika sudah presensi </b>";
            $apilink  = $this->apiTelegram();
            $telegram = @file_get_contents($apilink . "sendmessage?chat_id=" . $message->telegram_id . "&text=" . urlencode($pesan)  . "&parse_mode=HTML");
            $telegram = json_decode($telegram, TRUE);
            $dataUpdate = [
              'message' => $pesan,
              'waktu_pagi'  => '1',
              'updated_at'  => date('Y-m-d H:i:s')
            ];
            $where    =
              [
                'id'    => $message->id,
              ];
            $update  += $this->CRONJOB->updateMessage($dataUpdate, $where);
            if ($update === count($cekMessage)) {
              break;
            }
          }
          if ($update > 0) {
            $apilink  = $this->apiTelegram();
            $telegramId = '1374218169';
            $telegram = @file_get_contents($apilink . "sendmessage?chat_id=" . $telegramId . "&text=Berhasil mengirim sebanyak " . $update . " Data di Bot Message dari " . count($cekMessage) . " Pegawai yang terdaftar&parse_mode=HTML");
            $telegram = json_decode($telegram, TRUE);
          }
        } else {
          $apilink  = $this->apiTelegram();
          $telegramId = '1374218169';
          $telegram = @file_get_contents($apilink . "sendmessage?chat_id=" . $telegramId . "&text=Jam Sudah Lewat&parse_mode=HTML");
          $telegram = json_decode($telegram, TRUE);
        }
      }
    }
  }

  private function hari_ini()
  {
    $hari = date("D");

    switch ($hari) {
      case 'Sun':
        $hari_ini = "Minggu";
        break;

      case 'Mon':
        $hari_ini = "Senin";
        break;

      case 'Tue':
        $hari_ini = "Selasa";
        break;

      case 'Wed':
        $hari_ini = "Rabu";
        break;

      case 'Thu':
        $hari_ini = "Kamis";
        break;

      case 'Fri':
        $hari_ini = "Jumat";
        break;

      case 'Sat':
        $hari_ini = "Sabtu";
        break;

      default:
        $hari_ini = "Tidak di ketahui";
        break;
    }

    return $hari_ini;
  }

  private function apiTelegram()
  {
    $token  = "5623000868:AAEE-EuMsh1LR6Kd5iHnMlwsGZVkX54ovmA";
    $apilink  = "https://api.telegram.org/bot" . $token . "/";
    return $apilink;
  }
}
