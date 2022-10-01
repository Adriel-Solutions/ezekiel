<?php

    namespace native\thirdparties;

    use native\libs\Thirdparty;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    class Excel extends Thirdparty {

        /**
         * Generate an XLSX file from parameters
         */
        public function create_spreadsheet(string $title, array $headers, array $rows, array $style = []) : string {
            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            // Set sheet title
            $sheet->setTitle($title);

            // Fill headers row
            $col = 1;
            foreach($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, 1, $header);


                // Formatting (color)
                $sheet->getCellByColumnAndRow($col, 1)
                      ->getStyle()
                      ->getFill()
                      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()
                      ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);

                // Formatting (alignment)
                $sheet->getCellByColumnAndRow($col, 1)
                      ->getStyle()
                      ->getAlignment()
                      ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                      ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $col++;
            }
            $sheet->getRowDimension('1')->setRowHeight($style['headers_height'] ?: 30);

            // Fill data rows
            $row = 2;
            foreach($rows as $entry)  {
                $col = 1;

                for($col = 1; $col <= count($headers); $col++) {
                    $value = $entry[$col - 1];

                    // Set cell value as ="V" to prevent Excel error "Number stored as string"
                    $sheet->setCellValueByColumnAndRow($col, $row, '="'.$value.'"');
                }

                $row += 1;
            }

            // Auto-size columns
            foreach (range('A', $sheet->getHighestColumn()) as $col) 
               $sheet->getColumnDimension($col)->setAutoSize(true);

            // Export sheet into a string
            $writer = new Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $output = ob_get_contents(); 
            ob_end_clean();

            return $output;
        }
    }
