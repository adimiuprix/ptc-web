<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;

class M_ptc extends Model
{
    protected $table      = 'ptc_ads';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'owner', 'title', 'description', 'reward', 'timer', 'url', 'total_view', 'views', 'status', 'option_id'
    ];

    public function availableAds(int $userId): array
    {
        $past = time() - 86400;

        return $this->db->table('ptc_ads')
        ->where('views < total_view')
        ->where('status', 'active')
        ->whereNotIn('id', function(BaseBuilder $builder) use ($past, $userId) {
            return $builder->select('ads_id')
                ->from('ptc_histories')
                ->where('claim_time >', $past)
                ->where('user_id', $userId);
        })
        ->orderBy('reward', 'DESC')
        ->get()
        ->getResultArray();
    }

    public function getAllAds(): array
    {
        return $this->findAll();
    }

    public function getAdById(int $id): ?array
    {
        return $this->find($id);
    }

    public function verify(int $userId, int $adId): bool
    {
        $past = time() - 86400;
        $ipAddress = service('request')->getIPAddress();

        $sql = "
            SELECT ad_id FROM ptc_history 
            WHERE ad_id = ? 
                AND claim_time > ? 
                AND (ip_address = ? OR user_id = ?)
        ";

        $query = $this->db->query($sql, [$adId, $past, $ipAddress, $userId]);

        return $query->getNumRows() === 0;
    }

    public function updateUser(int $userId, float $amount): void
    {
        $builder = $this->db->table('users');
        $builder->where('id', $userId);
        $builder->set('balance', "balance + {$amount}", false);
        $builder->set('total_earned', "total_earned + {$amount}", false);
        $builder->set('last_active', time());
        $builder->set('token', random_string('alnum', 30));
        $builder->update();
    }

    public function addView(int $adId): void
    {
        $this->update($adId, [
            'views' => new \CodeIgniter\Database\RawSql('views + 1'),
        ]);
    }

    public function setCompleted(int $adId): void
    {
        $this->update($adId, [
            'status' => 'completed',
        ]);
    }

    public function insertHistory(int $userId, int $adId, float $amount): void
    {
        $this->db->table('ptc_history')->insert([
            'user_id'    => $userId,
            'ip_address' => service('request')->getIPAddress(),
            'ad_id'      => $adId,
            'amount'     => $amount,
            'claim_time' => time(),
        ]);
    }
}
