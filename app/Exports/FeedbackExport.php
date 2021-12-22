<?php

namespace App\Exports;

use App\Models\Feedback;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FeedbackExport implements FromCollection,WithHeadings,WithEvents,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $feedbacks = Feedback::select('name', 'email', 'feedback')
        ->join('users', 'feedback.user_id', '=', 'users.id')
        ->get();

        return $feedbacks;
    }

    public function headings(): array
    {
        return ['Attendee Name', 'Attendee Email', 'Attendee Feedback'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(100);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
    }
}
