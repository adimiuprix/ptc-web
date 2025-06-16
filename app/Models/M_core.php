<?php
namespace App\Models;

use CodeIgniter\Model;

class M_core extends Model
{
    protected $db;
    protected $request;

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = service('request');
    }

    public function getSettings(): array
    {
        $settings = $this->db->table('settings')->get()->getResultArray();
        $result = [];
        foreach ($settings as $value) {
            $result[$value['name']] = $value['value'];
        }
        return $result;
    }

    public function countLinkHistory($user_id): int
    {
        $past = time() - 86400;
        $ip_address = $this->request->getIPAddress();
        $query = "SELECT COUNT(link_id) as cnt FROM link_history WHERE claim_time > $past AND (ip_address = '$ip_address' OR user_id = $user_id)";
        return (int) $this->db->query($query)->getRowArray()['cnt'];
    }

    public function countAllLinksView(): int
    {
        return (int) $this->db->query("SELECT IFNULL(SUM(view_per_day), 0) as cnt FROM links")->getRowArray()['cnt'];
    }

    public function countAvailableAds($user_id): int
    {
        $past = time() - 86400;
        $ip_address = $this->request->getIPAddress();
        $query = "SELECT COUNT(*) AS cnt FROM ptc_ads WHERE views < total_view AND status = 'active' AND id NOT IN (SELECT ad_id FROM ptc_history WHERE claim_time > $past AND (ip_address = '$ip_address' OR user_id = $user_id))";
        return (int) $this->db->query($query)->getRowArray()['cnt'];
    }

    public function countAvailableTasks($user_id): int
    {
        $query = "SELECT COUNT(*) AS cnt FROM tasks WHERE id NOT IN (SELECT task_id FROM task_history WHERE user_id = $user_id) AND id NOT IN (SELECT task_id FROM task_submission WHERE user_id = $user_id)";
        return (int) $this->db->query($query)->getRowArray()['cnt'];
    }

    public function get_user_from_id($id): array|false
    {
        $user = $this->db->table('users')->getWhere(['id' => $id]);
        return $user->getNumRows() > 0 ? $user->getRowArray() : false;
    }

    public function get_user_from_email($email): array|false
    {
        $user = $this->db->table('users')->getWhere(['email' => $email]);
        return $user->getNumRows() > 0 ? $user->getRowArray() : false;
    }

    public function update_referral($id, $amount): void
    {
        $this->db->table('users')
            ->where('id', $id)
            ->set('balance', "balance + $amount", false)
            ->set('total_earned', "total_earned + $amount", false)
            ->update();
    }

    public function lastActive($userId): int
    {
        $query = $this->db->query("SELECT last_active FROM users WHERE id = $userId");
        return $query->getNumRows() > 0 ? (int) $query->getRowArray()['last_active'] : 0;
    }

    public function updateIsocode($id, $isocode, $country): void
    {
        $this->db->table('users')->where('id', $id)->update([
            'isocode' => $isocode,
            'country' => $country
        ]);
    }

    public function ban($id, $reason): void
    {
        $this->db->table('users')->where('id', $id)->update(['status' => $reason]);
    }

    public function banIp($ip, $reason): void
    {
        $this->db->table('users')->where('ip_address', $ip)->update(['status' => $reason]);
    }

    public function claim_fail($id, $status): void
    {
        if (is_numeric($status)) {
            $set = ($status >= 4) ? ['status' => 'Cheating'] : ['status' => $status + 1];
            $this->db->table('users')->where('id', $id)->update($set);
        }
    }

    public function newIp(): bool
    {
        $ip = $this->request->getIPAddress();
        $cnt = $this->db->query("SELECT COUNT(*) as cnt FROM ip_addresses WHERE ip_address = '$ip'")->getRowArray()['cnt'];
        return ((int) $cnt === 0);
    }

    public function newIpUser($userId): bool
    {
        $ip = $this->request->getIPAddress();
        $cnt = $this->db->query("SELECT COUNT(*) as cnt FROM ip_addresses WHERE ip_address = '$ip' AND user_id = $userId")->getRowArray()['cnt'];
        return ((int) $cnt === 0);
    }

    public function insertNewIp($userId): void
    {
        $ip = $this->request->getIPAddress();
        $sub = (strlen($ip) < 20) ? implode('.', array_slice(explode('.', $ip), 0, 3)) : implode(':', array_slice(explode(':', $ip), 0, 4));

        $insert = [
            'user_id' => $userId,
            'ip_address' => $ip,
            'last_use' => time(),
            'sub' => $sub
        ];

        $this->db->table('ip_addresses')->insert($insert);
    }

    public function updateIpLastUse($userId): void
    {
        $this->db->table('ip_addresses')
            ->where('user_id', $userId)
            ->where('ip_address', $this->request->getIPAddress())
            ->update(['last_use' => time()]);
    }

    public function getPages(): array
    {
        return $this->db->query("SELECT title, slug FROM pages WHERE priority <> 0 ORDER BY priority DESC")->getResultArray();
    }

    public function getCurrency($id): array|false
    {
        $row = $this->db->table('currencies')->getWhere(['id' => $id]);
        return $row->getNumRows() > 0 ? $row->getRowArray() : false;
    }

    public function insertCheatLog($userId, $log, $ipAddress = null): int
    {
        if (!$ipAddress) {
            $ipAddress = $this->request->getIPAddress();
        }

        $insert = [
            'user_id' => $userId,
            'log' => $log,
            'ip_address' => $ipAddress,
            'create_time' => time()
        ];

        $this->db->table('cheat_logs')->insert($insert);
        return $this->db->insertID();
    }

    public function countLottery(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) as cnt FROM lotteries")->getRowArray()['cnt'];
    }

    public function getCurrencies(): array
    {
        return $this->db->table('currencies')->get()->getResultArray();
    }

    public function firewallLock($userId): void
    {
        $this->db->table('users')->where('id', $userId)->update(['status' => 'firewall']);
    }

    public function unlockFirewall($userId): void
    {
        $this->db->table('users')
            ->where('id', $userId)
            ->update(['status' => 'ok', 'last_firewall' => time()]);
    }

    public function wrongCaptcha($userId): void
    {
        $this->db->table('users')
            ->where('id', $userId)
            ->set('fail', 'fail + 1', false)
            ->update();
    }

    public function resetFail($userId): void
    {
        $this->db->table('users')->where('id', $userId)->update(['fail' => 0]);
    }

    public function addNotification($userId, $content, $type): void
    {
        $insert = [
            'user_id' => $userId,
            'content' => $content,
            'type' => $type,
            'create_time' => time()
        ];
        $this->db->table('notifications')->insert($insert);
    }
}