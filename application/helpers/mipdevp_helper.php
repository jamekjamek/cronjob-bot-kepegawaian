<?php
//LOGIN TEMPLATE

use PHPMailer\PHPMailer\PHPMailer;

function templateAuth($page = '', $data = [])
{
  $mip = get_instance();
  $mip->load->view('authentication/template/header', $data);
  $mip->load->view('authentication/' . $page, $data);
  $mip->load->view('authentication/template/footer');
}


function PHPMailer()
{
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->SMTPDebug  = 0;
  $mail->Debugoutput = 'html';
  $mail->Host       = 'smtp.gmail.com';
  $mail->Port       = 465;
  $mail->SMTPSecure = 'ssl';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'templatecrud@gmail.com';
  $mail->Password   = 'ReactNative1234%';
  return $mail;
}

function tgl_indo($tanggal)
{
  $bulan = array(
    1 =>   'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
  );
  $pecahkan = explode('-', $tanggal);

  // variabel pecahkan 0 = tanggal
  // variabel pecahkan 1 = bulan
  // variabel pecahkan 2 = tahun

  return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}
