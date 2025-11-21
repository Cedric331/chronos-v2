<?php

namespace App\Exports;

use App\Models\Calendar;
use App\Models\Team;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamPlanningsExport implements FromCollection, ShouldAutoSize, WithCharts, WithColumnFormatting, WithEvents, WithHeadings, WithMapping, WithStyles
{
    use Exportable, RegistersEventListeners;

    protected Team $team;

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

    public function team(Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function collection()
    {
        $calendars = Calendar::query()
            ->whereHas('plannings', function ($query) {
                $query->where('team_id', $this->team->id);
            })
            ->with(['plannings' => function ($query) {
                $query->where('team_id', $this->team->id)
                    ->with('user');
            }])
            ->where('date', '>=', Carbon::now()->startOfWeek())
            ->orderBy('date')
            ->get();

        $rows = collect();

        foreach ($calendars as $calendar) {
            foreach ($calendar->plannings as $planning) {
                $this->rowTypes[] = $planning->type_day;
                // Nettoyer le nom de l'utilisateur pour éviter les problèmes de filtrage
                $userName = $planning->user ? trim($planning->user->name) : 'N/A';
                $rows->push([
                    'date' => $calendar->date_fr_full,
                    'user' => $userName,
                    'type_day' => $planning->type_day,
                    'debut_journee' => $planning->debut_journee ?? '',
                    'debut_pause' => $planning->debut_pause ?? '',
                    'fin_pause' => $planning->fin_pause ?? '',
                    'fin_journee' => $planning->fin_journee ?? '',
                ]);
            }
        }

        return $rows;
    }

    public function map($row): array
    {
        $this->rowTypes[] = $row['type_day'];

        // S'assurer que le nom utilisateur est une chaîne propre
        $userName = is_string($row['user']) ? trim($row['user']) : (string) ($row['user'] ?? 'N/A');
        if (empty($userName)) {
            $userName = 'N/A';
        }

        return [
            $row['date'],
            $userName, // Forcer en string
            $row['type_day'],
            $row['debut_journee'] ?? '',
            $row['debut_pause'] ?? '',
            $row['fin_pause'] ?? '',
            $row['fin_journee'] ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Utilisateur',
            'Type de journée',
            'Début journée',
            'Début pause',
            'Fin pause',
            'Fin journée',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '@', // Format texte pour la colonne Utilisateur
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Les styles seront appliqués dans afterSheet() après l'insertion du titre
        // pour éviter les problèmes de numérotation des lignes
        return [];
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

    public function generateChart($sheet, $statsEndRow, $statsStartRow = 3)
    {
        $sheetName = $sheet->getTitle();
        $statsHeaderRow = $statsStartRow;
        $statsDataStartRow = $statsStartRow + 1;
        $statsDataEndRow = $statsEndRow - 1;

        $dataSeriesLabels = [
            new DataSeriesValues('String', $sheetName.'!$J$'.$statsHeaderRow, null, 1),
        ];

        $xAxisTickValues = [
            new DataSeriesValues('String', $sheetName.'!$I$'.$statsDataStartRow.':$I$'.$statsDataEndRow, null, $statsDataEndRow - $statsDataStartRow + 1),
        ];

        $dataSeriesValues = [
            new DataSeriesValues('Number', $sheetName.'!$J$'.$statsDataStartRow.':$J$'.$statsDataEndRow, null, $statsDataEndRow - $statsDataStartRow + 1),
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
        $title = new Title('Graphique par Type de Journée - Équipe '.$this->team->name);
        $legend = new Legend;

        $chart = new Chart(
            'Graphique par Type de Journée',
            $title,
            $legend,
            $plotArea
        );

        // Positionner le graphique à côté des statistiques (colonne L)
        $chart->setTopLeftPosition('L'.$statsStartRow);
        $chart->setBottomRightPosition('V'.($statsStartRow + 15));

        return $chart;
    }

    public function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $numberOfColumns = count($this->headings());
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numberOfColumns);

        // Ajouter un titre en haut
        $sheet->insertNewRowBefore(1, 1);
        $sheet->mergeCells('A1:'.$lastColumn.'1');
        $sheet->setCellValue('A1', 'Plannings de l\'équipe : '.$this->team->name);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => 'FF4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFF3F4F6'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(35);

        // Style pour les en-têtes (ligne 2)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->getStyle('A2:'.$lastColumn.'2')->applyFromArray($headerStyle);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // Calculer la dernière ligne de données
        $dataEndRow = $highestRow + 1; // +1 car on a inséré une ligne

        // Définir le format de la colonne B (Utilisateur) comme texte pour TOUTE la colonne
        $sheet->getStyle('B:B')->getNumberFormat()->setFormatCode('@');

        // Réécrire toutes les valeurs de la colonne B en forçant le type texte
        for ($row = 3; $row <= $dataEndRow; $row++) {
            $cellB = $sheet->getCell('B'.$row);
            $currentValue = $cellB->getValue();
            if ($currentValue !== null && $currentValue !== '') {
                // Réécrire la valeur en forçant le type texte et en nettoyant
                $cleanValue = trim((string) $currentValue);
                $cellB->setValueExplicit($cleanValue ?: 'N/A', DataType::TYPE_STRING);
            } else {
                $cellB->setValueExplicit('N/A', DataType::TYPE_STRING);
            }
        }

        // Appliquer les styles aux données
        $rowIndex = 0;
        foreach ($this->rowTypes as $typeDay) {
            $rowNumber = $rowIndex + 3; // +1 titre, +1 en-tête, +1 pour index 0-based
            $color = $this->getColorForTypeDay($typeDay);

            $sheet->getStyle('A'.$rowNumber.':'.$lastColumn.$rowNumber)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => $color],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $rowIndex++;
        }

        // Ajouter des filtres automatiques sur les en-têtes ET toutes les données
        $filterRange = 'A2:'.$lastColumn.$dataEndRow;
        $sheet->setAutoFilter($filterRange);

        // Geler le titre et les en-têtes
        $sheet->freezePane('A3');

        // Ajuster les largeurs de colonnes
        $columnWidths = [
            'A' => 20, // Date
            'B' => 25, // Utilisateur
            'C' => 20, // Type de journée
            'D' => 15, // Début journée
            'E' => 15, // Début pause
            'F' => 15, // Fin pause
            'G' => 15, // Fin journée
        ];

        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Statistiques et graphique - Positionner à côté des données (colonne I)
        $statsStartRow = 3; // Commencer juste après les en-têtes
        $sheet->setCellValue('I'.$statsStartRow, 'Type de Journée');
        $sheet->setCellValue('J'.$statsStartRow, 'Nombre de Jours');

        // Style pour les en-têtes des statistiques
        $sheet->getStyle('I'.$statsStartRow.':J'.$statsStartRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        $statsRow = $statsStartRow + 1;
        foreach ($this->dayTypeCounts as $type => $count) {
            if ($count > 0) {
                $sheet->setCellValue('I'.$statsRow, $type);
                $sheet->setCellValue('J'.$statsRow, $count);
                $sheet->getStyle('I'.$statsRow.':J'.$statsRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $statsRow++;
            }
        }

        // Créer le graphique - Positionner à côté des statistiques
        $chart = $this->generateChart($sheet, $statsRow, $statsStartRow);
        $sheet->addChart($chart);
    }
}
