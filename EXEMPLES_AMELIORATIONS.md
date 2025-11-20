# Exemples Concrets d'Améliorations - Chronos v2

Ce document contient des exemples de code pour implémenter les améliorations recommandées.

---

## 1. Service Layer - PlanningService

### Avant (dans le contrôleur) :
```php
// app/Http/Controllers/PlanningController.php
public function generate(RequestGeneratePlanning $request)
{
    $dateStart = Carbon::parse($request->dateStart);
    $dateEnd = Carbon::parse($request->dateEnd)->addWeek();
    
    // 50+ lignes de logique métier...
}
```

### Après (avec Service) :
```php
// app/Services/PlanningService.php
<?php

namespace App\Services;

use App\Models\Planning;
use App\Models\Calendar;
use App\Models\Rotation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanningService
{
    public function __construct(
        private PlanningRepository $planningRepository,
        private CalendarRepository $calendarRepository
    ) {}

    public function generatePlanning(
        array $rotations,
        int $userId,
        Carbon $dateStart,
        Carbon $dateEnd,
        bool $typeFix = false
    ): int {
        $countDayGenerate = 0;
        $currentDate = $dateStart->copy();

        while ($currentDate->lte($dateEnd)) {
            foreach ($rotations as $rotation) {
                foreach ($rotation['details'] as $detail) {
                    $this->createPlanning(
                        $detail,
                        $rotation,
                        $userId,
                        $currentDate,
                        $typeFix
                    );
                    $currentDate->addDay();
                    $countDayGenerate++;

                    if ($currentDate->gt($dateEnd)) {
                        return $countDayGenerate;
                    }
                }
            }
        }

        return $countDayGenerate;
    }

    private function createPlanning(
        array $detail,
        array $rotation,
        int $userId,
        Carbon $date,
        bool $typeFix
    ): void {
        $calendar = $this->calendarRepository->findByDate($date);

        if (!$calendar) {
            return;
        }

        $existingPlanning = $this->planningRepository
            ->findByCalendarAndUser($calendar->id, $userId);

        if ($existingPlanning && !$typeFix && 
            in_array($existingPlanning->type_day, config('teams.type_days_fix'))) {
            return;
        }

        $this->planningRepository->updateOrCreate([
            'id' => $existingPlanning?->id,
        ], [
            'type_day' => $detail['is_off'] ? 'Repos' : 'Planifié',
            'debut_journee' => $detail['debut_journee'],
            'debut_pause' => $detail['debut_pause'],
            'fin_pause' => $detail['fin_pause'],
            'fin_journee' => $detail['fin_journee'],
            'is_technician' => $detail['technicien'],
            'telework' => $detail['teletravail'],
            'hours' => $this->calculateWorkHours($detail),
            'calendar_id' => $calendar->id,
            'rotation_id' => $rotation['id'],
            'team_id' => Auth::user()->team_id,
            'user_id' => $userId,
        ]);
    }

    public function calculateWorkHours(array $workDay): ?string
    {
        if ($workDay['is_off'] || !$workDay['debut_journee']) {
            return null;
        }

        $startDay = Carbon::createFromFormat('H:i', 
            str_replace('h', ':', $workDay['debut_journee']));
        $endDay = Carbon::createFromFormat('H:i', 
            str_replace('h', ':', $workDay['fin_journee']));

        $totalWorkHours = $endDay->diffInMinutes($startDay) / 60;

        if (isset($workDay['debut_pause']) && isset($workDay['fin_pause'])) {
            $startBreak = Carbon::createFromFormat('H:i', 
                str_replace('h', ':', $workDay['debut_pause']));
            $endBreak = Carbon::createFromFormat('H:i', 
                str_replace('h', ':', $workDay['fin_pause']));
            
            $breakHours = $endBreak->diffInMinutes($startBreak) / 60;
            $totalWorkHours -= $breakHours;
        }

        $hours = intval($totalWorkHours);
        $minutes = ($totalWorkHours - $hours) * 60;

        return sprintf('%02d', $hours) . 'h' . sprintf('%02d', $minutes);
    }
}
```

### Contrôleur simplifié :
```php
// app/Http/Controllers/PlanningController.php
public function generate(RequestGeneratePlanning $request): JsonResponse
{
    $dateStart = Carbon::parse($request->dateStart);
    $dateEnd = Carbon::parse($request->dateEnd)->addWeek();

    if ($dateStart->gt($dateEnd)) {
        return response()->json('Erreur dans la sélection des dates', 422);
    }

    $countDayGenerate = $this->planningService->generatePlanning(
        $request->rotations,
        $request->user,
        $dateStart,
        $dateEnd,
        $request->type_fix
    );

    activity(Auth::user()->team->name)
        ->event('Mise à jour')
        ->performedOn(User::find($request->user))
        ->log('Un planning a été généré pour ' . User::find($request->user)->name);

    return response()->json($countDayGenerate);
}
```

---

## 2. Repository Pattern - PlanningRepository

```php
// app/Repositories/PlanningRepository.php
<?php

namespace App\Repositories;

use App\Models\Planning;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PlanningRepository
{
    public function findByCalendarAndUser(int $calendarId, int $userId): ?Planning
    {
        return Planning::where('calendar_id', $calendarId)
            ->where('user_id', $userId)
            ->first();
    }

    public function getPlanningsForUser(
        int $userId,
        int $teamId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): Collection {
        $query = Planning::where('user_id', $userId)
            ->where('team_id', $teamId)
            ->with(['calendar', 'eventPlannings']);

        if ($startDate) {
            $query->whereHas('calendar', function ($q) use ($startDate, $endDate) {
                $q->where('date', '>=', $startDate);
                if ($endDate) {
                    $q->where('date', '<=', $endDate);
                }
            });
        }

        return $query->get();
    }

    public function getTeamPlanningsForWeek(
        int $teamId,
        Carbon $startOfWeek,
        Carbon $endOfWeek
    ): Collection {
        $cacheKey = "team_plannings_{$teamId}_{$startOfWeek->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 3600, function () use ($teamId, $startOfWeek, $endOfWeek) {
            return Planning::where('team_id', $teamId)
                ->whereHas('calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->where('date', '>=', $startOfWeek)
                        ->where('date', '<=', $endOfWeek);
                })
                ->with(['calendar', 'eventPlannings', 'user'])
                ->get();
        });
    }
}
```

---

## 3. Suppression de `protected $with` et Eager Loading Explicite

### Avant :
```php
// app/Models/User.php
protected $with = ['team']; // Charge toujours la team, même si pas nécessaire

// app/Models/Planning.php
protected $with = ['rotation']; // Charge toujours la rotation
```

### Après :
```php
// app/Models/User.php
// Supprimer protected $with = ['team'];

// Dans les contrôleurs/services, charger explicitement :
$users = User::with('team')->where('team_id', $teamId)->get();

// Ou avec plusieurs relations :
$users = User::with(['team', 'plannings.calendar'])->get();
```

---

## 4. Cache pour les Jours Fériés et Vacances

```php
// app/Services/HolidayService.php
<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Yasumi\Yasumi;
use ICal\ICal;

class HolidayService
{
    private const CACHE_TTL = 86400; // 24 heures

    public function getFrenchHolidays(int $year): array
    {
        $cacheKey = "french_holidays_{$year}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year) {
            $holidays = Yasumi::create('France', $year, 'fr_FR');
            $holidayDates = [];

            foreach ($holidays->getHolidays() as $holiday) {
                $date = $holiday->format('Y-m-d');
                $name = $holiday->getName();
                $holidayDates[$date] = $name;
            }

            return $holidayDates;
        });
    }

    public function getSchoolHolidays(int $year): array
    {
        $cacheKey = "school_holidays_{$year}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year) {
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
                if (strpos($event->summary, 'A') !== false) $zones[] = 'A';
                if (strpos($event->summary, 'B') !== false) $zones[] = 'B';
                if (strpos($event->summary, 'C') !== false) $zones[] = 'C';

                foreach ($zones as $zone) {
                    $holidaysByZone[$zone][] = [
                        'name' => $event->summary,
                        'start' => date('Y-m-d', $startDate),
                        'end' => date('Y-m-d', strtotime('-1 day', $endDate)),
                    ];
                }
            }

            return $holidaysByZone;
        });
    }

    private function fetchUrlContent(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new \Exception('Erreur cURL : ' . curl_error($ch));
        }
        
        curl_close($ch);
        return $data;
    }
}
```

---

## 5. Utilisation d'Enums PHP 8.1+

```php
// app/Enums/PlanningType.php
<?php

namespace App\Enums;

enum PlanningType: string
{
    case PLANNED = 'Planifié';
    case REST = 'Repos';
    case PAID_LEAVE = 'Congés payés';
    case SICK_LEAVE = 'Arrêt maladie';
    case MORNING_PAID_LEAVE = 'CP matin';
    case AFTERNOON_PAID_LEAVE = 'CP après-midi';
    case TRAINING = 'Formation';
    case HOLIDAY = 'Jour férié';

    public function requiresHours(): bool
    {
        return match($this) {
            self::PLANNED, 
            self::TRAINING, 
            self::MORNING_PAID_LEAVE, 
            self::AFTERNOON_PAID_LEAVE => true,
            default => false,
        };
    }

    public function isFixed(): bool
    {
        return in_array($this, [
            self::HOLIDAY,
            self::PAID_LEAVE,
        ]);
    }
}

// Utilisation :
$type = PlanningType::PLANNED;
if ($type->requiresHours()) {
    // Calculer les heures
}
```

---

## 6. Tests Unitaires - Exemple

```php
// tests/Unit/Services/PlanningServiceTest.php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PlanningService;
use App\Models\Planning;
use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanningServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlanningService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PlanningService::class);
    }

    public function test_calculate_work_hours_without_break(): void
    {
        $workDay = [
            'is_off' => false,
            'debut_journee' => '09h00',
            'fin_journee' => '17h00',
        ];

        $result = $this->service->calculateWorkHours($workDay);

        $this->assertEquals('08h00', $result);
    }

    public function test_calculate_work_hours_with_break(): void
    {
        $workDay = [
            'is_off' => false,
            'debut_journee' => '09h00',
            'fin_journee' => '17h00',
            'debut_pause' => '12h00',
            'fin_pause' => '13h00',
        ];

        $result = $this->service->calculateWorkHours($workDay);

        $this->assertEquals('07h00', $result);
    }

    public function test_calculate_work_hours_returns_null_for_off_day(): void
    {
        $workDay = [
            'is_off' => true,
        ];

        $result = $this->service->calculateWorkHours($workDay);

        $this->assertNull($result);
    }
}
```

---

## 7. Migration vers Pinia (Vue 3)

### Avant (Vuex) :
```javascript
// resources/js/store.js
import { createStore } from 'vuex'

export default createStore({
  state: {
    user: null,
    team: null
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user
    }
  },
  actions: {
    setUser({ commit }, user) {
      commit('SET_USER', user)
    }
  }
})
```

### Après (Pinia) :
```javascript
// resources/js/stores/user.js
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    team: null
  }),
  
  getters: {
    isAdmin: (state) => state.user?.isAdmin ?? false,
    isCoordinateur: (state) => state.user?.isCoordinateur ?? false,
  },
  
  actions: {
    setUser(user) {
      this.user = user
    },
    
    setTeam(team) {
      this.team = team
    },
    
    async fetchUser() {
      const response = await axios.get('/api/user')
      this.setUser(response.data)
    }
  }
})
```

```javascript
// resources/js/app.js
import { createPinia } from 'pinia'

const app = createApp({ render: () => h(App, props) })
    .use(createPinia()) // Au lieu de .use(store)
    // ...
```

---

## 8. Resource Classes pour Standardiser les Réponses API

```php
// app/Http/Resources/PlanningResource.php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanningResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type_day' => $this->type_day,
            'date' => $this->calendar->date,
            'hours' => $this->hours,
            'debut_journee' => $this->debut_journee,
            'fin_journee' => $this->fin_journee,
            'telework' => $this->telework,
            'is_technician' => $this->is_technician,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ],
            'events' => EventPlanningResource::collection($this->eventPlannings),
        ];
    }
}

// Utilisation dans le contrôleur :
return PlanningResource::collection($plannings);
// ou
return new PlanningResource($planning);
```

---

## 9. Migration pour Ajouter des Index

```php
// database/migrations/XXXX_XX_XX_add_indexes_to_plannings_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plannings', function (Blueprint $table) {
            $table->index(['user_id', 'calendar_id']);
            $table->index(['team_id', 'calendar_id']);
            $table->index('type_day');
            $table->index('created_at');
        });

        Schema::table('calendars', function (Blueprint $table) {
            $table->index('date');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('team_id');
            $table->index('account_active');
        });
    }

    public function down(): void
    {
        Schema::table('plannings', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'calendar_id']);
            $table->dropIndex(['team_id', 'calendar_id']);
            $table->dropIndex(['type_day']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('calendars', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['team_id']);
            $table->dropIndex(['account_active']);
        });
    }
};
```

---

## 10. Exception Personnalisée

```php
// app/Exceptions/PlanningException.php
<?php

namespace App\Exceptions;

use Exception;

class PlanningException extends Exception
{
    public static function invalidDateRange(): self
    {
        return new self('La date de début doit être antérieure à la date de fin.', 422);
    }

    public static function userNotFound(int $userId): self
    {
        return new self("L'utilisateur avec l'ID {$userId} n'existe pas.", 404);
    }

    public static function insufficientPermissions(): self
    {
        return new self('Vous n\'avez pas les permissions nécessaires pour effectuer cette action.', 403);
    }
}

// Utilisation :
throw PlanningException::invalidDateRange();
```

---

Ces exemples montrent comment implémenter les améliorations recommandées. Chaque exemple peut être adapté selon les besoins spécifiques du projet.

