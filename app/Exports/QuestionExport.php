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
    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::table('responses')
        ->select('name', 'question', 'answer')
        ->join('users', 'users.id', 'responses.user_id')
        ->join('questions', 'questions.id', 'responses.question_id')
        ->join('answers', 'answers.id', 'responses.answer_id')
        ->where('responses.user_id', $this->user_id)
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
