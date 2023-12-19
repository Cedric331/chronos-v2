<?php

namespace App\Exports;

use App\Models\Calendar;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;


class PlanningsExport implements FromQuery,WithStyles, WithMapping, WithHeadings, ShouldAutoSize, WithEvents, WithCharts
{
    use Exportable, RegistersEventListeners;

    protected User $user;
    protected array $rowTypes = [];
    protected array $chart = [];

    protected array $dayTypeCounts = [
        'Planifié' => 0,
        'Congés Payés' => 0,
        'Congés sans solde' => 0,
        'Repos' => 0,
        'Jour Férié' => 0,
        'Maladie' => 0,
        'CP Matin' => 0,
        'CP Après-midi' => 0,
        'Récup JF' => 0,
    ];

    public function user($user): static
    {
        $this->user = $user;

        return $this;
    }

    public function query()
    {
        $user = $this->user;
        return Calendar::query()
            ->whereHas('plannings', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['plannings' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('date', '>=', Carbon::now()->startOfWeek());
    }

    /**
     * @param $calendar
     * @return array
     */
    public function map($calendar): array
    {
        $rows = [];

        foreach ($calendar->plannings as $planning) {
            $this->rowTypes[] = $planning->type_day;
            $rows[] = [
                $calendar->date_fr_full,
                $planning->type_day,
                $planning->debut_journee,
                $planning->debut_pause,
                $planning->fin_pause,
                $planning->fin_journee,
            ];
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Date',
            'Type de journée',
            'Début journée',
            'Début pause',
            'Fin pause',
            'Fin journée',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        $numberOfColumns = count($this->headings());

        foreach ($this->rowTypes as $index => $typeDay) {
            if (!isset($this->dayTypeCounts[$typeDay])) {
                $this->dayTypeCounts[$typeDay] = 0;
            }
            $this->dayTypeCounts[$typeDay]++;

            $rowNumber = $index + 2;
            $color = $this->getColorForTypeDay($typeDay);

            for ($col = 'A'; $col <= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numberOfColumns); $col++) {
                $styles[$col . $rowNumber] = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => $color],
                    ],
                ];
            }
        }

        return $styles;
    }

    private function getColorForTypeDay($typeDay)
    {
        return match ($typeDay) {
            'Planifié', 'CP Matin', 'CP Après-midi' => 'FF7BED9F',
            'Congés Payés', 'Congés sans solde', 'Récup JF' => 'FF7ED6DF',
            'Repos', 'Jour Férié' => 'FF48DBFB',
            'Maladie' => 'FFFECA57',
            default => 'FFB8E994',
        };
    }

    public function charts()
    {
        return [];
    }

    public function generateChart($sheet, $dataRowCount)
    {
        $dataSeriesLabels = [
            new DataSeriesValues('String', $sheet->getTitle() . '!$J$1', null, 1),
        ];

        $xAxisTickValues = [
            new DataSeriesValues('String', $sheet->getTitle() . '!$I$2:$I$' . ($dataRowCount - 1), null, $dataRowCount - 2),
        ];

        $dataSeriesValues = [
            new DataSeriesValues('Number', $sheet->getTitle() . '!$J$2:$J$' . ($dataRowCount - 1), null, $dataRowCount - 2),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $title = new Title('Graphique par Type de Journée');
        $legend = new Legend();

        $chart = new Chart(
            'Graphique par Type de Journée',
            $title,
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('L2');
        $chart->setBottomRightPosition('U20');

        return $chart;
    }

    public function afterSheet(AfterSheet $event)
    {
        $row = 1;
        $event->sheet->setCellValue('I' . $row, 'Type de Journée');
        $event->sheet->setCellValue('J' . $row, 'Nombre de Jours');
        $row++;

        foreach ($this->dayTypeCounts as $type => $count) {
            $event->sheet->setCellValue('I' . $row, $type);
            $event->sheet->setCellValue('J' . $row, $count);
            $row++;
        }
        $chart = $this->generateChart($event->sheet, $row);
        $event->sheet->getDelegate()->addChart($chart);
    }
}
