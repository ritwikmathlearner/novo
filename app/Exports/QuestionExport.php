<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuestionExport implements FromCollection,WithHeadings,WithEvents,WithStyles
{
    protected $attendee_id;
    protected $kol_session_id;

    public function __construct($attendee_id, $kol_session_id)
    {
        $this->attendee_id = $attendee_id;
        $this->kol_session_id = $kol_session_id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::table('responses')
        ->select('name', 'question', 'answer')
        ->join('attendees', 'attendees.id', 'responses.attendee_id')
        ->join('questions', 'questions.id', 'responses.question_id')
        ->join('answers', 'answers.id', 'responses.answer_id')
        ->where('responses.attendee_id', $this->attendee_id)
        ->where('responses.kol_session_id', $this->kol_session_id)
        ->get();

        return $data;
    }

    public function headings(): array
    {
        return ['Attendee Name', 'Question', 'Answer'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(60);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(60);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
    }
}
