<?php

namespace App\Exports;

use App\Models\Calendar;
use App\Models\PaidLeave;
use App\Models\Team;
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


class PaidLeaveExport implements FromQuery,WithStyles, WithMapping, WithHeadings, ShouldAutoSize, WithEvents, WithCharts
{
    use Exportable, RegistersEventListeners;

    protected Team $team;
    protected array $rowTypes = [];
    protected array $dayTypeCounts = [];

    protected array $chart = [];

    public function team($team): static
    {
        $this->team = $team;

        return $this;
    }

    public function query()
    {
        return PaidLeave::with(['calendars', 'user'])
            ->where('team_id', $this->team->id)
            ->orderByRaw("FIELD(status, '" . PaidLeave::STATUS_PENDING . "') DESC")
            ->orderBy('created_at', 'desc');
    }

    /**
     * @param $items
     * @return array
     */
    public function map($item): array
    {
        $rows = [];


            $this->rowTypes[] = $item->status;
            $rows[] = [
                $item->user->name,
                $item->type,
                $item->comment,
                $item->calendars->pluck('date_fr_full')->join(', '),
                $item->status
            ];

        return $rows;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Conseiller',
            'Type',
            'Commentaire',
            'Dates',
            'Statut',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        $numberOfColumns = count($this->headings());

        foreach ($this->rowTypes as $index => $status) {
            if (!isset($this->dayTypeCounts[$status])) {
                $this->dayTypeCounts[$status] = 0;
            }
            $this->dayTypeCounts[$status]++;

            $rowNumber = $index + 2;
            $color = $this->getColorForTypeDay($status);

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
            PaidLeave::STATUS_ACCEPTED => 'FFdcfce7',
            PaidLeave::STATUS_REFUSED => 'FFfee2e2',
            PaidLeave::STATUS_PENDING => 'FFfef9c3',
            default => 'FF000000',
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
        $title = new Title('Graphique par type de demande');
        $legend = new Legend();

        $chart = new Chart(
            'Graphique par type de demande',
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
        $event->sheet->setCellValue('I' . $row, 'Statut');
        $event->sheet->setCellValue('J' . $row, 'Nombre');
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
