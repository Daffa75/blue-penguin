<?php

namespace App\Filament\Lecturer\Resources\FinalProjectResource\Pages;

use App\Filament\Lecturer\Resources\FinalProjectResource;
use App\Models\FinalProject;
use App\Models\Lecturer;
use App\Models\Logbook;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class LogbookFinalProject extends ManageRelatedRecords
{
    protected static string $resource = FinalProjectResource::class;

    protected static string $relationship = 'logbooks';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __("Final Project Logbook");
    }

    public function getTitle(): string
    {
        return (__('Final Project Logbook'));
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
                Forms\Components\MarkdownEditor::make('activity')
                    ->required()
                    ->disableAllToolbarButtons()
                    ->columnSpanFull()
                    ->disabled(auth()->user()->role !== '4')
                    ->required(),
                Repeater::make('feedbacks')
                    ->relationship('feedbacks')
                    ->schema([
                        Forms\Components\Hidden::make('lecturer_id')
                            ->default(fn () => Lecturer::where('user_id', auth()->user()->id)->first()?->id)
                            ->live(),
                        Forms\Components\TextInput::make('lecturer_name')
                            ->label('Lecturer')
                            ->formatStateUsing(fn (Get $get) => Lecturer::where('id', $get('lecturer_id'))->first()?->name)
                            ->disabled(true),
                        Forms\Components\MarkdownEditor::make('content')
                            ->disabled(fn (Get $get) => $get('lecturer_id') !== Lecturer::where('user_id', auth()->user()->id)->first()->id)
                            ->disableAllToolbarButtons()
                            ->required()
                    ])
                    ->deletable(false)
                    ->addActionLabel(__('Add Feedback'))
                    ->maxItems(2)
                    ->columnSpanFull()
                    ->hidden(auth()->user()->role !== '3')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->state(function ($record) {
                        $student = $record->student;
                        return "{$student->nim} - {$student->name}";
                    })
                    ->width('200px')
                    ->hidden(auth()->user()->role === '4'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->sortable()
                    ->width('150px')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('activity')
                    ->label('Activity')
                    ->wrap()
                    ->lineClamp(5),
                Tables\Columns\TextColumn::make('feedbacks')
                    ->label('Feedback')
                    ->formatStateUsing(function ($record) {
                        $feedbackList = $record->feedbacks->map(function ($feedback) {
                            return "<p class='line-clamp-5'><b>{$feedback->lecturer->name}</b>:<br> {$feedback->content}</p>";
                        })
                            ->implode('<br>');

                        return $feedbackList;
                    })
                    ->html()
                    ->wrap()
            ])
            ->filters([
                // 
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(auth()->user()->role !== '4'),
                Tables\Actions\Action::make('Generate Word Document')
                    ->label('Generate Document')
                    ->action(fn () => static::generateWordDocument())
                    ->hidden(auth()->user()->role !== '4'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label(function () {
                        $panelId = Filament::getCurrentPanel()->getId();
                        return $panelId === 'lecturer' ? 'Add Feedback' : 'Edit';
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
                                    ->label(__('Date'))
                            ])
                    ]),
                Components\Section::make(__("Activity"))
                    ->schema([
                        Components\TextEntry::make('activity')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
                Components\Section::make(function ($record) {
                    $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                    $supervisorTwo = $finalProject->lecturers->where('pivot.role', 'supervisor 2')->first();

                    return $supervisorTwo ? __('Feedback Supervisor 1') : __('Feedback Supervisor');
                })
                    ->relationship('feedbacks')
                    ->schema([
                        Components\TextEntry::make('content')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull()
                            ->html()
                            ->formatStateUsing(function ($record) {
                                $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                                $supervisorOne = $finalProject->lecturers->where('pivot.role', 'supervisor 1')->first();
                                $feedback = $record->feedbacks->where('lecturer_id', $supervisorOne->id)->first();

                                return "<p><span class='font-bold text-base'>{$supervisorOne->name}</span> :<br> {$feedback->content}</p>";
                            }),
                    ])
                    ->hidden(function ($record) {
                        $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                        $supervisorOne = $finalProject->lecturers->where('pivot.role', 'supervisor 1')->first();
                        $feedback = $record->feedbacks->where('lecturer_id', $supervisorOne->id)->first();

                        return !$feedback;
                    }),
                Components\Section::make(__("Feedback Supervisor 2"))
                    ->relationship('feedbacks')
                    ->schema([
                        Components\TextEntry::make('content')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull()
                            ->formatStateUsing(function ($record) {
                                $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                                $supervisorTwo = $finalProject->lecturers->where('pivot.role', 'supervisor 2')->first();
                                $feedback = $record->feedbacks->where('lecturer_id', $supervisorTwo->id)->first();

                                return "<p><span class='font-bold text-base'>{$supervisorTwo->name}</span> :<br> {$feedback->content}</p>";
                            }),
                    ])
                    ->hidden(function ($record) {
                        $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                        $supervisorTwo = $finalProject->lecturers->where('pivot.role', 'supervisor 2')->first();
                        if (!$supervisorTwo) {
                            return true;
                        }
                        $feedback = $record->feedbacks->where('lecturer_id', $supervisorTwo->id)->first();

                        return !$feedback;
                    }),
            ])
            ->columns(3);
    }

    protected static function generateWordDocument()
    {
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);

        $student = Student::where('user_id', auth()->user()->id)->first();
        $degree = substr($student->nim, 0, 4) === 'D121' || substr($student->nim, 0, 4) === 'D421' ? 'S1' : 'S2';

        // add title
        $section = $phpWord->addSection();
        $cellHCentered = array('alignment' => 'center');
        $cellVCentered = array('valign' => 'center');

        $section->addText(
            'kartu bimbingan skripsi',
            array('size' => 14, 'lineHeight' => 2.0, 'allCaps' => true),
            $cellHCentered
        );

        $section->addText(
            'Prodi ' . $degree . ' Teknik Informatika Universitas Hasanuddin',
            array('lineHeight' => 2.0),
            $cellHCentered
        );

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
        ]);

        $table->addRow();
        $table->addCell(3000)->addText('Stb.', null, $cellHCentered);
        $table->addCell(7000)->addText('Nama Mahasiswa', null, $cellHCentered);

        $table->addRow();
        $table->addCell(3000)->addText($student->nim);
        $table->addCell(7000)->addText($student->name);

        $section->addTextBreak(1);

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000'
        ]);
        $table->addRow();
        $table->addCell(2000, $cellVCentered)->addText('Pembimbing', null, $cellHCentered);
        $table->addCell(4000, $cellVCentered)->addText('Nama Pembimbing', null, $cellHCentered);
        $table->addCell(4000, $cellVCentered)->addText("Paraf dan Tgl. Persetujuan Ujian Akhir", null, $cellHCentered);

        $table->addRow(1000);
        // use foreach to add supervisor
        $supervisorOne = $student->finalProject->lecturers->where('pivot.role', 'supervisor 1');
        foreach ($supervisorOne as $supervisor) {
            $table->addCell(2000, $cellVCentered)->addText('I', null, $cellHCentered);
            $table->addCell(4000, $cellVCentered)->addText($supervisor->name, null, $cellHCentered);
            $table->addCell(4000);
        }

        $supervisorTwo = $student->finalProject->lecturers->where('pivot.role', 'supervisor 2');

        if ($supervisorTwo->count() > 0) {
            $table->addRow(1000);
            foreach ($supervisorTwo as $supervisor) {
                $table->addCell(2000, $cellVCentered)->addText('II', null, $cellHCentered);
                $table->addCell(4000, $cellVCentered)->addText($supervisor->name, null, $cellHCentered);
                $table->addCell(4000);
            }
        }

        $table->addRow();
        $table->addCell(10000, array("gridSpan" => 3))->addText('No. SK Pembimbing');

        // final project title
        $section->addTextBreak(1);

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
        ]);
        $table->addRow();
        $table->addCell(3000, $cellVCentered)->addText('Judul Skripsi', null, $cellHCentered);
        $table->addCell(7000)->addText($student->finalProject->title, array('allCaps' => true));

        // activity log
        $section->addTextBreak(1);

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
        ]);

        $table->addRow();
        $table->addCell(1000, $cellVCentered)->addText('No.', null, $cellHCentered);
        $table->addCell(3000, $cellVCentered)->addText('Tanggal Bimbingan.', null, $cellHCentered);
        $table->addCell(4500, $cellVCentered)->addText('Uraian Kegiatan.', null, $cellHCentered);
        $table->addCell(1500, $cellVCentered)->addText('Paraf Pemb.', null, $cellHCentered);

        $logbooks = $student->finalProject->logbooks->sortBy('date');

        $i = 1;
        foreach ($logbooks as $logbook) {
            $date = explode(', ', static::idDateFormat($logbook->date, true));
            $activity = explode("\n", $logbook->activity);

            $table->addRow(1000);
            $table->addCell(1000, $cellVCentered)->addText($i++, null, $cellHCentered);
            $table->addCell(3000, $cellVCentered)->addText($date[0] . ',' . "</w:t><w:br/><w:t>" . $date[1], null, $cellHCentered);
            $activityCell = $table->addCell(4500, $cellVCentered);
            foreach ($activity as $line) {
                $activityCell->addText($line);
            }
            $table->addCell(1500, $cellVCentered);
        }

        $fileName = 'logbook_final_project_' . $student->nim . '.docx';
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
