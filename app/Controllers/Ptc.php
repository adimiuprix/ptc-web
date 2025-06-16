<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_Ptc;
use App\Models\M_Core;

class Ptc extends BaseController
{
    protected $m_ptc;
    protected $m_core;

    public function __construct()
    {
        $this->m_ptc = new M_Ptc();
        $this->m_core = new M_Core();
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
                ->with('message', $this->faucet_alert('danger', 'Invalid Ad'));
        }

        // Menampilkan data dari iklan yang di view berdasarkan id
        $ads = $this->m_ptc->getAdById($id);

        // Memeriksa apakah nilainya ada atau tidak null
        if (! $ads) {
            return redirect()
                ->to('/ptc')
                ->with('message', $this->faucet_alert('danger', 'Invalid Ad'));
        }

        // set kunci 'start_view' dengan nilai timestamp saat ini dan simpan ke session
        session()->set('start_view', time());

        return view('ptc_view_ads');
    }

    public function verify($id = 0)
    {
        helper('text');

        session()->remove('start_view');

        if (!is_numeric($id)) {
            session()->setFlashdata('message', faucet_alert('danger', 'Invalid Click'));
            return redirect()->to('/ptc');
        }

        $ad = $this->m_ptc->get_ads_from_id($id);
        $startTime = session()->get('start_view') ?? time();

        if (!$ad || (time() - $startTime < $ad['timer'])) {
            session()->setFlashdata('message', faucet_alert('danger', 'Invalid Click'));
            return redirect()->to('/ptc');
        }

        if ($ad['views'] >= $ad['total_view']) {
            session()->setFlashdata('message', faucet_alert('danger', 'This Ad has reached maximum views'));
            return redirect()->to('/ptc');
        }

        if ($this->request->getPost('token') !== $this->data['user']['token']) {
            session()->setFlashdata('message', faucet_alert('danger', 'Invalid Claim'));
            return redirect()->to('/faucet');
        }

        $captcha = $this->request->getPost('captcha');
        $Check_captcha = false;

        setcookie('captcha', $captcha, time() + 86400 * 10);

        switch ($captcha) {
            case "recaptchav3":
                $Check_captcha = $this->verifyRecaptchaV3(
                    $this->request->getPost('recaptchav3'),
                    $this->data['settings']['recaptcha_v3_secret_key']
                );
                break;
            case "recaptchav2":
                $Check_captcha = $this->verifyRecaptchaV2(
                    $this->request->getPost('g-recaptcha-response'),
                    $this->data['settings']['recaptcha_v2_secret_key']
                );
                break;
        }

        if (!$Check_captcha) {
            if ($this->data['user']['fail'] == $this->data['settings']['captcha_fail_limit']) {
                $this->m_core->insertCheatLog($this->data['user']['id'], 'Too many wrong captcha.', 0);
            } elseif ($this->data['user']['fail'] < 4) {
                $this->m_core->wrongCaptcha($this->data['user']['id']);
            }
            session()->setFlashdata('message', faucet_alert('danger', 'Invalid Captcha'));
            return redirect()->to('/ptc');
        }

        $check = $this->m_ptc->verify($this->data['user']['id'], $id);

        if (!$check) {
            session()->setFlashdata('message', faucet_alert('danger', 'Invalid Ad'));
            return redirect()->to('/ptc');
        }

        $this->m_ptc->update_user($this->data['user']['id'], $ad['reward']);
        $this->m_core->addExp($this->data['user']['id'], $this->data['settings']['ptc_exp_reward']);

        if (($this->data['user']['exp'] + $this->data['settings']['ptc_exp_reward']) >= (($this->data['user']['level'] + 1) * 100)) {
            $this->m_core->levelUp($this->data['user']['id']);
        }

        $this->m_ptc->addView($ad['id']);
        if (($ad['views'] + 1) == $ad['total_view']) {
            $this->m_ptc->completed($ad['id']);
        }

        $this->m_ptc->insert_history($this->data['user']['id'], $ad['id'], $ad['reward']);

        if ($this->data['user']['fail'] > 0) {
            $this->resetFail($this->data['user']['id']);
        }

        session()->setFlashdata('sweet_message', $this->faucet_sweet_alert('success', $this->currency($ad['reward'], $this->data['settings']['currency_rate']) . ' has been added to your balance'));

        return redirect()->to('/ptc');
    }

    protected function faucet_alert($type, $content): string
    {
        $icon = ($type === 'success') ? '<i class="far fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
        return '<div class="alert text-center alert-' . $type . '">' . $icon . ' ' . $content . '</div>';
    }

    protected function faucet_sweet_alert($type, $content): string
    {
        $title = ($type === 'success') ? 'Good job!' : 'Error!';
        return "<script> Swal.fire('" . $title . "', '" . $content . "', '" . $type . "')</script>";
    }

    public function resetFail($userId): void
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->set('fail', '0')->where('id', $userId)->update();
    }

    protected function currency($amount, $rate): string
    {
        $token = $amount / $rate;
        return $token . ($token > 1 ? ' tokens' : ' token');
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
