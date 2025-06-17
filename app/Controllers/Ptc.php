<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_Ptc;

class Ptc extends BaseController
{
    protected $m_ptc;

    public function __construct()
    {
        $this->m_ptc = new M_Ptc();
    }

    public function index()
    {
        // Mengambil user_id dari session
		$userId = session()->get('user_id');

        // Mula-mula  total reward nilainya 0
		$totalReward = 0;

        // Mengambil ads yang tersedia dan belum di click
		$availableAds = $this->m_ptc->availableAds($userId);

        // Menjumlahkan ads yang tersedia untuk di claim
		$totalAds = count($availableAds);

        // Menampilkan semua ads yang tersedia untuk di render ke view
		foreach ($availableAds as $ads) {
			$totalReward += $ads['reward'];
		}

		$data = [
			'total_ads' => $totalAds,
			'ads' => $availableAds,
		];

        return view('ptc', $data);
    }

    public function view($id = 0)
    {
        // cek apakah id iklan berupa integer
        if (!is_numeric($id)) {
            return redirect()
                ->to('/ptc')
                ->with('message', $this->ptc_alert('danger', 'Invalid Ad'));
        }

        // Menampilkan data dari iklan yang di view berdasarkan id
        $adsDetail = $this->m_ptc->getAdById($id);

        // Memeriksa apakah nilainya ada atau tidak null
        if (! $adsDetail) {
            return redirect()
                ->to('ptc')
                ->with('message', $this->ptc_alert('danger', 'Invalid Ad'));
        }

        // set kunci 'start_view' dengan nilai timestamp saat ini dan simpan ke session
        session()->set('start_view', time());

        return $this->response->setBody(view('ptc_view_ads', [
            'ads' => $adsDetail
        ]));
    }

    public function verify($id = 0)
    {
        // Mengambil user_id dari session aktif
        $user_id = session()->get('user_id');

        // Jika id iklan bukan angka, lempar ke halaman sebelumnya dengan pesan
        if (!is_numeric($id)) {
            return redirect()->to('ptc')->with('message', $this->ptc_alert('danger', 'Invalid Click'));
        }

        // Menapilkan isi dari iklan yang di pilih
        $adsDetail = $this->m_ptc->getAdById($id);

        // Mendapatkan nilai "start_view" dari session
        $startTime = session()->get('start_view') ?? time();

        // Periksa apakah iklan bisa di claim
        if (time() - $startTime < $adsDetail['timer']) {
            return redirect()->to('ptc')->with('message', $this->ptc_alert('danger', 'Invalid Click'));
        }

        // Hapus setelah pengecekan berhasil
        session()->remove('start_view');

        // Periksa apakah 'views' lebih besar dari 'total_view'
        if ($adsDetail['views'] >= $adsDetail['total_view']) {
            session()->setFlashdata('message', $this->ptc_alert('danger', 'This Ad has reached maximum views'));
            return redirect()->to('/ptc');
        }

        // Periksa apakah ads bisa di claim pengguna
        $check = $this->m_ptc->verify($user_id, $id);

        // Jika ads tidak bisa di claim
        if (!$check) {
            session()->setFlashdata('message', $this->ptc_alert('danger', 'Invalid Ad'));
            return redirect()->to('ptc');
        }

        // Setelah pengecekan iklan berhasil, update saldo pengguna sebagai reward
        $this->m_ptc->updateUser($user_id, $adsDetail['reward']);

        // Menambahkan jumlah views +1
        $this->m_ptc->addView($adsDetail['id']);

        // Periksa jika views = total_view maka tandai status sebagai completed
        if (($adsDetail['views'] + 1) == $adsDetail['total_view']) {
            $this->m_ptc->setCompleted($adsDetail['id']);
        }

        // Memasukkan data baru ke table ptc_histories
        $this->m_ptc->insertHistory($user_id, $adsDetail['id'], $adsDetail['reward']);

        // Membuat pesan flash message dengan sweatallert2
        session()->setFlashdata('sweet_message', $this->sweetAlert('success', '40 token has been added to your balance'));
        
        return redirect()->to('ptc');
    }

    protected function ptc_alert($type, $content): string
    {
        $icon = ($type === 'success') ? '<i class="far fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
        return '<div class="alert text-center alert-' . $type . '">' . $icon . ' ' . $content . '</div>';
    }

    protected function sweetAlert($type, $content): string
    {
        $title = ($type === 'success') ? 'Good job!' : 'Error!';
        return "<script> Swal.fire('" . $title . "', '" . $content . "', '" . $type . "')</script>";
    }

    protected function verifyCloudFlareTurnstile(): bool
    {
        $Captcha_result = service('curlrequest')->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            ['form_params' => [
                'secret'   => '0x4AAAAAABhLBfHeufkuSHDTc85C5F8keX8',
                'response' => $this->request->getPost('cf-turnstile-response'),
                'remoteip' => $this->request->getServer('HTTP_CF_CONNECTING_IP'),
            ]]
            )->getBody(true);
        return json_decode($Captcha_result)->success;
    }

    protected function verifyRecaptchaV3($response, $secretKeys): bool
    {
        $Captcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $Captcha_data = ['secret' => $secretKeys, 'response' => $response];
        $Captcha_options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($Captcha_data),
            ]
        ];
        $Captcha_context = stream_context_create($Captcha_options);
        $Captcha_result = file_get_contents($Captcha_url, false, $Captcha_context);

        $result = json_decode($Captcha_result);
        return ($result->success && $result->score >= 0.3);
    }

    protected function verifyRecaptchaV2($response, $secretKeys): bool
    {
        $Captcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $Captcha_data = ['secret' => $secretKeys, 'response' => $response];
        $Captcha_options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($Captcha_data),
            ]
        ];
        $Captcha_context = stream_context_create($Captcha_options);
        $Captcha_result = file_get_contents($Captcha_url, false, $Captcha_context);

        return json_decode($Captcha_result)->success;
    }
}
