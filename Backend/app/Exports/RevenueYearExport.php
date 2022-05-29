<?php

namespace App\Exports;
use App\DonHang;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Classes\SalesService;
use App\Classes\Revenue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;
class RevenueYearExport implements FromCollection, WithEvents , WithCustomStartCell, WithMapping ,WithColumnWidths , WithDrawings, WithColumnFormatting , WithStyles,  WithHeadings
{

    protected $year;
    protected $month;
    protected $status;
    protected $status_name;
    function __construct($month, $year,$status=0,$status_name = 'Tất cả')
    {
        $this->year = $year;
        $this->month = $month;
        $this->status = $status;
        $this->status_name = $status_name;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $revenue =SalesService::revenueEachMonthByYear($this->month, $this->year,$this->status);
        $orders =SalesService::numberOrdersPerMonthByYear($this->month, $this->year,$this->status);
        $collection = new Collection();
        $i=0;
        foreach ($revenue as $item){
            $item["timestamp"]=$i+1;

            $item["orders"]=$orders[$i]["amount"];
            $collection->push($item);
            $i++;
        }   
        return $collection;
    }

    public function styles(Worksheet $sheet)    
    {
        
        return [
            2    => ['font' => ['bold' => true, 'italic' => true,'size' => 18]],
            7    => ['font' => ['bold' => true]],
            8    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
            4    => ['font' => ['bold' => true]],
            5    => ['font' => ['bold' => true]],
            6    => ['font' => ['bold' => true]],
            12    => ['font' => ['bold' => true]],
          
        ];
    }
    public function registerEvents(): array
{   
   
    return [
        AfterSheet::class    => function(AfterSheet $event) {
            $max_border = $this->month==0?24:$this->month+12;
            
            $value = 'B12:E'.$max_border;
            $value_row_2 = 'C12:C'.$max_border;
            $value_row_3 = 'D12:D'.$max_border;
            $event->sheet->getStyle('B12:E12')->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ]
            ]);
            $event->sheet->getStyle($value_row_2)->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ]
            ]);
            $event->sheet->getStyle($value_row_3)->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ]
            ]);
            
            $event->sheet->getStyle($value)->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ]
            ]);
            $event->sheet->getStyle('B9:C10')->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ]
            ]);
            for ($i =12; $i<=$max_border;$i++){
                $event->sheet->getStyle('B'.$i.':'.'E'.$i)->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ]);
            }
            $event->sheet->getStyle('B9:W1000')->getAlignment()->setHorizontal('center');

        },
    ];
}
    public function columnFormats(): array
    {
        return [
        ];
    }
    public function startCell(): string
    {
        return 'B2';
    }

    public function columnWidths(): array
    {
        return [
            'B' => 17,        
            'C' => 17,
            'D' => 17,  
            'E' => 17,    
        ];
    }
    public function drawings()
    {
        $img_file='../public/images/logo.png';
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path($img_file));
        $drawing->setHeight(35);
        $drawing->setCoordinates('B2');

        return $drawing;
    }
    public function headings(): array
    {
        $revenue =SalesService::revenueEachMonthByYear($this->month, $this->year,$this->status);
        $orders =SalesService::numberOrdersPerMonthByYear($this->month, $this->year,$this->status);
        $total_revenue =0;
        foreach($revenue as $item){
            $total_revenue+=$item["revenue"];
        }
        $total_orders =0;
        foreach($orders as $item){
            $total_orders+=$item["amount"];
        }
        $name =  auth()->user()? auth()->user()->TenNguoidung: 'Admin';
        
        return [
            ['','     CỬA HÀNG MÁY TÍNH TIẾN VŨ STORE'],
            [''],
            ['Địa chỉ: ','41A Đ. Phú Diễn, Phú Diễn, Bắc Từ Liêm, Hà Nội'],
            ['Nội dung: ','Doanh thu  năm '.$this->year],
            ['Người xuất: ',$name],
            ['Ngày xuất: ', date("H:i d-m-Y")],
            ['Trạng thái: ', $this->status_name],
            ['Tổng doanh thu: ', number_format($total_revenue, 0, '', ',').' VNĐ'],
            ['Tổng đơn hàng: ', $total_orders.' Đơn'],
            [''],
            ['#',
            'Tháng',
            'Doanh thu',
            'Số lượng đơn'
        ]];
    }
    public function map($bill): array
    {
      
        return [
            $bill["timestamp"],
            $bill["month"],
            number_format($bill["revenue"], 0, '', ','),
            number_format($bill["orders"], 0, '', ','),
         
        ];
    }
}
