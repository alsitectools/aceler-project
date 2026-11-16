<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTimetable extends Model
{
    use HasFactory;

    protected $table = 'user_timetable';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'range_holidays',
        'range_intensive_workday',
    ];

    public static function timeToDecimal(?string $time): float
    {
        if (empty($time)) {
            return 0.0;
        }

        $parts = explode(':', $time);
        $hours = (int) ($parts[0] ?? 0);
        $minutes = (int) ($parts[1] ?? 0);

        return $hours + ($minutes / 60);
    }

    /**
     * Jornadas intensivas guardadas como JSON {"HH:MM": ["Y-m-d", ...]} => ['Y-m-d' => 'HH:MM']
     */
    public static function parseIntensiveHours(?string $rangeIntensiveWorkday): array
    {
        if (empty($rangeIntensiveWorkday)) {
            return [];
        }

        $decodedIntensive = json_decode($rangeIntensiveWorkday, true);
        if (!is_array($decodedIntensive)) {
            return [];
        }

        $intensiveHoursByDate = [];

        foreach ($decodedIntensive as $hours => $dates) {
            if (!is_array($dates)) {
                continue;
            }

            foreach ($dates as $date) {
                if (empty($date)) {
                    continue;
                }

                $intensiveHoursByDate[$date] = $hours;
            }
        }

        return $intensiveHoursByDate;
    }

    // Horas que el usuario debería imputar ese día (jornada intensiva si aplica, si no la del día de la semana)
    public function expectedHoursOn(string $date): float
    {
        $targetDate = Carbon::parse($date)->toDateString();
        $intensiveHoursByDate = self::parseIntensiveHours($this->range_intensive_workday ?? null);

        if (isset($intensiveHoursByDate[$targetDate])) {
            return self::timeToDecimal($intensiveHoursByDate[$targetDate]);
        }

        $dayOfWeek = strtolower(Carbon::parse($targetDate)->format('l'));
        return self::timeToDecimal($this->$dayOfWeek ?? '00:00');
    }

    public function holidayDates(): array
    {
        if (empty($this->range_holidays)) {
            return [];
        }

        $decodedHolidays = json_decode($this->range_holidays, true);
        if (!is_array($decodedHolidays)) {
            return [];
        }

        $holidayDates = [];
        foreach (array_values($decodedHolidays) as $holidayDate) {
            if (empty($holidayDate)) {
                continue;
            }

            try {
                $holidayDates[] = Carbon::parse($holidayDate)->toDateString();
            } catch (\Throwable $e) {
                continue;
            }
        }

        return array_values(array_unique($holidayDates));
    }

    /**
     * Días laborables de esta semana, anteriores a hoy, sin ninguna hora imputada (los "rojos" de la hoja).
     * Devuelve fechas Y-m-d; vacío si el usuario no tiene horario.
     */
    public static function pastDaysWithoutHoursThisWeek(int $userId): array
    {
        $timeTable = self::where('user_id', $userId)->first();
        if (!$timeTable) {
            return [];
        }

        $today = Carbon::today();
        $monday = $today->copy()->startOfWeek();
        if ($monday->equalTo($today)) {
            return [];
        }

        $loggedDates = Timesheet::where('created_by', $userId)
            ->whereBetween('date', [$monday->toDateString(), $today->copy()->subDay()->toDateString()])
            ->where('time', '>', '00:00:00')
            ->distinct()
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->flip();

        $holidays = array_flip($timeTable->holidayDates());
        $missing = [];

        for ($day = $monday->copy(); $day->lt($today); $day->addDay()) {
            $date = $day->toDateString();
            if (isset($holidays[$date]) || isset($loggedDates[$date])) {
                continue;
            }
            if ($timeTable->expectedHoursOn($date) > 0) {
                $missing[] = $date;
            }
        }

        return $missing;
    }
}
