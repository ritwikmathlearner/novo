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
    protected $kol_session_id;

    public function __construct($kol_session_id)
    {
        $this->kol_session_id = $kol_session_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $feedbacks = Feedback::select('name', 'phone', 'feedback')
        ->join('attendees', 'feedback.attendee_id', '=', 'attendees.id')
        ->where('feedback.kol_session_id', $this->kol_session_id)
        ->get();

        return $feedbacks;
    }

    public function headings(): array
    {
        return ['Attendee Name', 'Attendee Phone', 'Feedback (Out of 5)'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(40);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
    }
}
