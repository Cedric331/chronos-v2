<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use ICal\ICal;
use Yasumi\Yasumi;

class HolidayService
{
    private const CACHE_TTL = 86400; // 24 heures

    /**
     * Génère les jours avec les informations sur les jours fériés et vacances
     */
    public function generateDaysWithHolidays(?CarbonImmutable $dateDebut = null, ?CarbonImmutable $dateFin = null): array
    {
        date_default_timezone_set('Europe/Paris');
        $year = date('Y');
        
        if ($dateDebut === null) {
            $dateDebut = CarbonImmutable::parse("{$year}-01-01");
        }
        
        if ($dateFin === null) {
            $dateFin = CarbonImmutable::parse("{$year}-12-31");
        }

        $days = [];
        $holidays = $this->getFrenchHolidays($year);
        $holidaysByZone = $this->getSchoolHolidays($year);

        $currentDate = $dateDebut;
        while ($currentDate <= $dateFin) {
            $formattedDate = $currentDate->format('Y-m-d');
            $currentDateString = $currentDate->toDateString();

            $isHoliday = isset($holidays[$currentDateString]);
            $holidayName = $isHoliday ? $holidays[$currentDateString] : '';

            [$isSchoolHoliday, $schoolHolidayZones] = $this->isSchoolHoliday($currentDateString, $holidaysByZone);

            $days[] = [
                'date' => $formattedDate,
                'jour_ferie' => [$isHoliday, $holidayName],
                'vacance' => [$isSchoolHoliday, $schoolHolidayZones],
            ];

            $currentDate = $currentDate->addDay();
        }

        return $days;
    }

    /**
     * Récupère les jours fériés français pour une année donnée (avec cache)
     */
    public function getFrenchHolidays(int $year): array
    {
        $cacheKey = "french_holidays_{$year}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year) {
            try {
                $holidays = Yasumi::create('France', $year, 'fr_FR');
                $holidayDates = [];

                foreach ($holidays->getHolidays() as $holiday) {
                    $date = $holiday->format('Y-m-d');
                    $name = $holiday->getName();
                    $holidayDates[$date] = $name;
                }

                return $holidayDates;
            } catch (\Exception $e) {
                Log::error("Erreur lors de la récupération des jours fériés pour l'année {$year}: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Récupère les vacances scolaires par zone (avec cache)
     */
    public function getSchoolHolidays(int $year): array
    {
        $cacheKey = "school_holidays_{$year}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year) {
            try {
                $url = 'https://fr.ftp.opendatasoft.com/openscol/fr-en-calendrier-scolaire/Zone-A-B-C-Corse.ics';
                $content = $this->fetchUrlContent($url);

                $ical = new ICal($content, [
                    'defaultTimeZone' => 'Europe/Paris',
                ]);

                $holidaysByZone = [
                    'A' => [],
                    'B' => [],
                    'C' => [],
                ];

                foreach ($ical->events() as $event) {
                    $startDate = $event->dtstart_array[2];
                    $endDate = $event->dtend_array[2];

                    $zones = [];
                    if (strpos($event->summary, 'A') !== false) {
                        $zones[] = 'A';
                    }
                    if (strpos($event->summary, 'B') !== false) {
                        $zones[] = 'B';
                    }
                    if (strpos($event->summary, 'C') !== false) {
                        $zones[] = 'C';
                    }

                    if (!empty($zones)) {
                        foreach ($zones as $zone) {
                            $holidaysByZone[$zone][] = [
                                'name' => $event->summary,
                                'start' => date('Y-m-d', $startDate),
                                'end' => date('Y-m-d', strtotime('-1 day', $endDate)),
                            ];
                        }
                    }
                }

                return $holidaysByZone;
            } catch (\Exception $e) {
                Log::error("Erreur lors de la récupération des vacances scolaires pour l'année {$year}: " . $e->getMessage());
                return [
                    'A' => [],
                    'B' => [],
                    'C' => [],
                ];
            }
        });
    }

    /**
     * Vérifie si une date est en vacances scolaires
     */
    private function isSchoolHoliday(string $date, array $holidaysByZone): array
    {
        $zonesInHolidays = [];

        foreach ($holidaysByZone as $zone => $holidays) {
            foreach ($holidays as $holiday) {
                if ($date >= $holiday['start'] && $date <= $holiday['end']) {
                    $zonesInHolidays[] = $zone;
                }
            }
        }

        return [!empty($zonesInHolidays), $zonesInHolidays];
    }

    /**
     * Récupère le contenu d'une URL via cURL
     */
    private function fetchUrlContent(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('Erreur cURL : ' . $error);
        }
        
        curl_close($ch);
        return $data;
    }
}

