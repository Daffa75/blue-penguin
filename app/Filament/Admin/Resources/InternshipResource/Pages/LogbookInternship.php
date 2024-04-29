<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\InternshipStudents;
use App\Models\Lecturer;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Filament\Infolists\Components;

class LogbookInternship extends ManageRelatedRecords
{
    protected static string $resource = InternshipResource::class;

    protected static string $relationship = 'logbooks';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __("Internship Logbook");
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('student_id')
                    ->default(fn () => Student::where('user_id', auth()->user()->id)->first()->id),
                Forms\Components\DatePicker::make('date')
                    ->label(__('Date'))
                    ->default(now())
                    ->columnSpanFull()
                    ->disabled(auth()->user()->role !== '4')
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->label(__('Start Time'))
                    ->seconds(false)
                    ->disabled(auth()->user()->role !== '4')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->label(__('End Time'))
                    ->seconds(false)
                    ->disabled(auth()->user()->role !== '4')
                    ->required(),
                Forms\Components\MarkdownEditor::make('activity')
                    ->label(__('Activity'))
                    ->disableAllToolbarButtons()
                    ->columnSpanFull()
                    ->disabled(auth()->user()->role !== '4')
                    ->required(),
                Repeater::make('feedbacks')
                    ->relationship('feedbacks')
                    ->schema([
                        Forms\Components\Hidden::make('lecturer_id')
                            ->default(fn () => Lecturer::where('user_id', auth()->user()->id)->first()?->id),
                        Forms\Components\MarkdownEditor::make('content')
                            ->disableAllToolbarButtons()
                            ->required()
                    ])
                    ->deletable(false)
                    ->addActionLabel(__('Add Feedback'))
                    ->maxItems(1)
                    ->columnSpanFull()
                    ->hidden(auth()->user()->role !== '3')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->state(function ($record) {
                        $student = $record->student;
                        return "{$student->nim} - {$student->name}";
                    })
                    ->width('200px')
                    ->hidden(auth()->user()->role === '4'),
                TextColumn::make('date')
                    ->label('Date')
                    ->sortable()
                    ->width('150px')
                    ->date('d M Y'),
                TextColumn::make('activity')
                    ->label('Activity')
                    ->wrap()
                    ->lineClamp(5),
                TextColumn::make('feedbacks')
                    ->label('Feedback')
                    ->wrap()
                    ->lineClamp(5)
                    ->formatStateUsing(function ($record) {
                        $data = $record->feedbacks->first();
                        return "<p><b>{$data->lecturer->name}</b> :<br>{$data->content}</p>";
                    })
                    ->html()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')
                    ->label(__('Student'))
                    ->native(false)
                    ->hidden(auth()->user()->role === '4')
                    ->options(function () {
                        $internshipStudent = InternshipStudents::where('internship_id', $this->record->getKey())->get();
                        $students = [];

                        foreach ($internshipStudent as $listStudent) {
                            $students[$listStudent->student_id] = Student::find($listStudent->student_id)->nim . ' - ' . Student::find($listStudent->student_id)->name;
                        }

                        ksort($students);
                        return $students;
                    })
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer' || Filament::getCurrentPanel()->getId() === 'admin'),
                Tables\Actions\Action::make('Generate Word Document')
                    ->label('Generate Document')
                    ->action(fn () => static::generateWordDocument())
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer' || Filament::getCurrentPanel()->getId() === 'admin'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn (Logbook $record) => __("View Activity On ") . date("d F Y", strtotime($record->date))),
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn (Logbook $record) => __("Edit Activity On ") . date("d F Y", strtotime($record->date)))
                    ->label(function () {
                        $panelId = Filament::getCurrentPanel()->getId();
                        return $panelId === 'lecturer' ? 'Add Feedback' : 'Edit';
                    })
                    ->hidden(auth()->user()->role !== '3' && auth()->user()->role !== '4'),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer' || Filament::getCurrentPanel()->getId() === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $student = Student::where('user_id', auth()->user()->id)->first();
                if ($student) {
                    $query->where('student_id', $student->id);
                }

                return $query;
            });
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Grid::make()
                            ->schema([
                                Components\TextEntry::make('student.name')
                                    ->label(__('Student')),
                                Components\TextEntry::make('date')
                                    ->label(__('Date')),
                                Components\TextEntry::make('start_time')
                                    ->label(__('Start Time'))
                                    ->formatStateUsing(fn ($state) => date('H:i', strtotime($state))),
                                Components\TextEntry::make('end_time')
                                    ->label(__('End Time'))
                                    ->formatStateUsing(fn ($state) => date('H:i', strtotime($state)))
                            ])
                    ]),
                Components\Section::make(__("Activity"))
                    ->schema([
                        Components\TextEntry::make('activity')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
                Components\Section::make(__("Feedback"))
                    ->relationship('feedbacks')
                    ->schema([
                        Components\TextEntry::make('content')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull()
                            ->formatStateUsing(function ($record) {
                                $data = $record->feedbacks->first();
                                return "<p><span class='font-bold text-base'>{$data->lecturer->name}</span> :<br>{$data->content}</p>";
                            }),
                    ])
                    ->hidden(fn ($record) => $record->feedbacks->isEmpty())
            ])
            ->columns(3);
    }

    protected static function generateWordDocument()
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontSize(12);
        $phpWord->getSettings()->setZoom(80);


        $section = $phpWord->addSection();

        $headerStyle = ['size' => 14, 'bold' => true];
        $cellHCentered = ['alignment' => 'center', 'spaceBefore' => 200, 'spaceAfter' => 200];
        $cellVCentered = ['valign' => 'center'];

        // Add header
        $section->addText('Logbook Kerja Praktek', $headerStyle, $cellHCentered);
        $section->addText('Departemen Teknik Informatika Universitas Hasanuddin', $headerStyle, $cellHCentered);
        $section->addTextBreak(2);

        $student = Student::where('user_id', auth()->user()->id)->first();
        $internship = Internship::whereHas('students', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->first();
        $otherStudent = $internship->students->where('id', '!=', $student->id)->first();

        $logbooks = Logbook::where('student_id', $student->id)->orderBy('date', 'asc')->get();

        $startDate = static::idDateFormat($internship->start_date);
        $endDate = static::idDateFormat($internship->end_date);

        $section->addText('Nama' . "\t" . "\t" . "\t" . ': ' . $student->name, ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('NIM' . "\t" . "\t" . "\t" . ': ' . $student->nim, ['bold' => true], ['lineHeight' => 1.5]);
        if ($otherStudent) {
            $section->addText('Tim KP' . "\t" . "\t"  . ': ' . $otherStudent->name . ' / ' . $otherStudent->nim, ['bold' => true], ['lineHeight' => 1.5]);
        }
        $section->addText('Lokasi' . "\t" . "\t" . ': ' . $internship->company_name, ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Pembimbing KP' . "\t" . ': ' . $internship->lecturer->name, ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Supervisor' . "\t" . "\t" . ': ' . $internship->supervisor_name, ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Periode KP' . "\t" . "\t" . ': ' . $startDate . ' - ' . $endDate, ['bold' => true], ['lineHeight' => 1.5]);
        $section->addTextBreak(1);

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'alignment' => JcTable::CENTER,
        ]);

        $table->addRow();

        $table->addCell(700, $cellVCentered)->addText('No', ['bold' => true], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Hari/Tanggal', ['bold' => true], $cellHCentered);
        $table->addCell(1000, $cellVCentered)->addText('Waktu', ['bold' => true], $cellHCentered);
        $table->addCell(3600, $cellVCentered)->addText('Uraian Detail Kegiatan', ['bold' => true], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Paraf (Pembimbing KP/Supervisor)', ['bold' => true], ['alignment' => 'center', 'lineHeight' => 1.5, 'spaceBefore' => 200]);
        $table->addCell(700, $cellVCentered)->addText('Ket', ['bold' => true], $cellHCentered);

        $i = 1;

        foreach ($logbooks as $logbook) {
            $date = explode(', ', static::idDateFormat($logbook->date, true));
            $activity = explode("\n", $logbook->activity);

            $table->addRow();
            $table->addCell(700, $cellVCentered)->addText($i, null, $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText($date[0] . ',' . "</w:t><w:br/><w:t>" . $date[1], null, $cellHCentered);
            $table->addCell(1000, $cellVCentered)->addText(date('H:i', strtotime($logbook->start_time)) . ' - ' . date('H:i', strtotime($logbook->end_time)), null, $cellHCentered);
            $activityCell = $table->addCell(3600);
            $activityCell->addText('');
            foreach ($activity as $line) {
                $activityCell->addText($line, null, ['lineHeight' => 1.5]);
            }
            $activityCell->addText('');
            $table->addCell(2000, $cellVCentered)->addText('');
            $table->addCell(700, $cellVCentered)->addText('');
            $i++;
        }

        $fileName = 'logbook_kp_' . $student->nim . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word_doc') . '.docx';
        $phpWord->save($tempFile);

        // Prepare response for downloading the file
        $response = new BinaryFileResponse($tempFile);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }

    protected static function idDateFormat($date, $print_day = false)
    {
        $day = array(
            1 =>    'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu'
        );

        $month = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split = explode('-', $date);
        $indoDateFormat = $split[2] . ' ' . $month[(int)$split[1]] . ' ' . $split[0];

        if ($print_day) {
            $num = date('N', strtotime($date));
            return $day[$num] . ', ' . $indoDateFormat;
        }
        return $indoDateFormat;
    }
}
