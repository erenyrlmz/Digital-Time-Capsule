<?php
/**
 * Merkezi Rozet (Rank) Sistemi
 * Tüm sayfalarda require_once ile dahil edilir.
 */

function getRankInfo(int $total): array {
    $tiers = [
        ['min' => 1000, 'title' => 'Ölümsüz Efsane',    'icon' => '👑', 'color' => '#ffd700', 'label' => 'IMMORTAL'],
        ['min' => 500,  'title' => 'Evren Aşırısı',      'icon' => '🌀', 'color' => '#e879f9', 'label' => 'TRANSCENDENT'],
        ['min' => 100,  'title' => 'Paradoks Ustası',    'icon' => '⏳', 'color' => '#f59e0b', 'label' => 'MASTER'],
        ['min' => 50,   'title' => 'Zaman Mühendisi',    'icon' => '🔭', 'color' => '#fb923c', 'label' => 'ENGINEER'],
        ['min' => 20,   'title' => 'Kronik Seyyah',      'icon' => '⚡', 'color' => '#60a5fa', 'label' => 'VETERAN'],
        ['min' => 10,   'title' => 'Galaksi Kâşifi',     'icon' => '🌌', 'color' => '#b300ff', 'label' => 'EXPLORER'],
        ['min' => 1,    'title' => 'Zaman Yolcusu',      'icon' => '🛸', 'color' => '#00f2fe', 'label' => 'ROOKIE'],
        ['min' => 0,    'title' => 'Zaman Çırağı',       'icon' => '🥚', 'color' => '#94a3b8', 'label' => 'NOVICE'],
    ];

    foreach ($tiers as $i => $tier) {
        if ($total >= $tier['min']) {
            $rank = $tier;
            $rank['current_min'] = $tier['min'];
            $rank['total']       = $total;
            $rank['tier_index']  = $i;

            $prevTier = ($i > 0) ? $tiers[$i - 1] : null;

            if ($prevTier !== null) {
                $range = $prevTier['min'] - $tier['min'];
                $done  = $total - $tier['min'];
                $rank['next_min']         = $prevTier['min'];
                $rank['next_title']       = $prevTier['title'];
                $rank['next_icon']        = $prevTier['icon'];
                $rank['next_color']       = $prevTier['color'];
                $rank['progress_pct']     = min(100, round(($done / $range) * 100));
                $rank['progress_needed']  = $prevTier['min'] - $total;
            } else {
                // Maksimum rozet
                $rank['next_min']        = null;
                $rank['next_title']      = null;
                $rank['next_icon']       = null;
                $rank['next_color']      = null;
                $rank['progress_pct']    = 100;
                $rank['progress_needed'] = 0;
            }

            return $rank;
        }
    }

    return array_merge($tiers[count($tiers) - 1], [
        'current_min' => 0, 'total' => 0, 'tier_index' => count($tiers) - 1,
        'next_min' => 1, 'next_title' => 'Zaman Yolcusu', 'next_icon' => '🛸',
        'next_color' => '#00f2fe', 'progress_pct' => 0, 'progress_needed' => 1,
    ]);
}

/**
 * Tüm rozet kademelerini döndürür (UI'da rozet yol haritası için).
 */
function getAllTiers(): array {
    return [
        ['min' => 0,    'title' => 'Zaman Çırağı',    'icon' => '🥚', 'color' => '#94a3b8'],
        ['min' => 1,    'title' => 'Zaman Yolcusu',   'icon' => '🛸', 'color' => '#00f2fe'],
        ['min' => 10,   'title' => 'Galaksi Kâşifi',  'icon' => '🌌', 'color' => '#b300ff'],
        ['min' => 20,   'title' => 'Kronik Seyyah',   'icon' => '⚡', 'color' => '#60a5fa'],
        ['min' => 50,   'title' => 'Zaman Mühendisi', 'icon' => '🔭', 'color' => '#fb923c'],
        ['min' => 100,  'title' => 'Paradoks Ustası', 'icon' => '⏳', 'color' => '#f59e0b'],
        ['min' => 500,  'title' => 'Evren Aşırısı',   'icon' => '🌀', 'color' => '#e879f9'],
        ['min' => 1000, 'title' => 'Ölümsüz Efsane',  'icon' => '👑', 'color' => '#ffd700'],
    ];
}
?>
