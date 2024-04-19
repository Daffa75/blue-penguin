<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use App\Models\InternshipLogbook;
use App\Models\InternshipStudents;
use App\Models\Student;
use DateTime;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Logbook extends ManageRelatedRecords
{
    protected static string $resource = InternshipResource::class;

    protected static string $relationship = 'logbooks';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Logbook';
    }

    public static function getEloquentQuery(): Builder
    {
        $panelId = Filament::getCurrentPanel()->getId();

        if ($panelId == 'student') {
            $student = Student::where('user_id', auth()->user()->id)->first();
            return parent::getEloquentQuery()->where('student_id', $student->id);
        }

        return parent::getEloquentQuery();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('internship_id')
                    ->default(function () {
                        $student = Student::where('user_id', auth()->user()->id)->first();
                        $kerjaPraktekStudent = InternshipStudents::where('student_id', $student->id)->first();
                        return $kerjaPraktekStudent->internship_id;
                    }),
                Forms\Components\Hidden::make('student_id')
                    ->default(function () {
                        $student = Student::where('user_id', auth()->user()->id)->first();
                        return $student->id;
                    }),
                Forms\Components\DatePicker::make('date')
                    ->label(__('Date'))
                    ->default(now())
                    ->columnSpanFull()
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer')
                    ->required(),
                Forms\Components\MarkdownEditor::make('activity')
                    ->label(__('Activity'))
                    ->disableAllToolbarButtons()
                    ->columnSpanFull()
                    ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'lecturer')
                    ->required(),
                Forms\Components\MarkdownEditor::make('result')
                    ->label(__('Result'))
                    ->columnSpanFull()
                    ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'lecturer')
                    ->required(),
                Forms\Components\MarkdownEditor::make('feedback')
                    ->label(__('Feedback'))
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'student')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('activity')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->state(function (InternshipLogbook $record) {
                        $student = $record->student;
                        return "{$student->nim} - {$student->name}";
                    }),
                TextColumn::make('date')
                    ->label('Date')
                    ->sortable()
                    ->date('d M Y'),
                TextColumn::make('activity')
                    ->wrap()
                    ->label('Activity'),
                    TextColumn::make('result')
                    ->label('Result')
                    ->wrap()
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'student'),
                TextColumn::make('feedback')
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer')
                    ->wrap()
                    ->label('Feedback'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Student')
                    ->default(function () {
                        $panelId = Filament::getCurrentPanel()->getId();
                        if ($panelId === 'student') {
                            $student = Student::where('user_id', auth()->user()->id)->first();
                            return $student->id;
                        }
                    })
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Logbook')
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer' || Filament::getCurrentPanel()->getId() === 'admin'),
                static::generateWordDocumentAction()
                ->label('Generate Document')
                ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer' || Filament::getCurrentPanel()->getId() === 'admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(function () {
                        $panelId = Filament::getCurrentPanel()->getId();
                        return $panelId === 'lecturer' ? 'Add Feedback' : 'Edit';
                    }),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer' || Filament::getCurrentPanel()->getId() === 'admin'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\ForceDeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    public static function generateWordDocumentAction()
    {
        return Tables\Actions\Action::make('Generate Word Document')
            ->action(fn () => static::generateWordDocument());
    }

    protected static function generateWordDocument()
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Add header
        $textRun = $section->addTextRun(['alignment' => 'center']);
        $textRun->addText('Logbook Kerja Praktek', ['bold' => true, 'size' => 16]);
        $section->addTextBreak(2);

        $student = Student::where('user_id', auth()->user()->id)->first();
        $logbooks = InternshipLogbook::where('student_id', $student->id)->get();

        $section->addText('Nama' . "\t" . "\t" . "\t" . ': ' . $student->name, ['bold' => true, 'size' => 12], ['lineHeight' => 1.5]);
        $section->addText('NIM' . "\t" . "\t" . "\t" . ': ' . $student->nim, ['bold' => true, 'size' => 12], ['lineHeight' => 1.5]);
        $section->addText('Program Studi' . "\t" . ': ' . 'Teknik Informatika', ['bold' => true, 'size' => 12], ['lineHeight' => 1.5]);
        $section->addTextBreak(1);

        $table = $section->addTable([
            'alignment' => JcTable::CENTER,
            'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::AUTO,
        ]);

        $table->addRow();
    $cell = $table->addCell();
    $cell->setWidth(1);
    $textRun = $cell->addTextRun(['alignment' => 'center', 'lineHeight' => 1.5]);
    $textRun->addText('Tanggal', ['bold' => true]);

    $cell = $table->addCell();
    $textRun = $cell->addTextRun(['alignment' => 'center']);
    $textRun->addText('Kegiatan', ['bold' => true]);

    $cell = $table->addCell();
    $textRun = $cell->addTextRun(['alignment' => 'center']);
    $textRun->addText('Hasil', ['bold' => true]);

    foreach ($logbooks as $logbook) {
        $table->addRow();
        $cell = $table->addCell();
        $monthNames = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];

        $date = new DateTime($logbook->date);
        $englishMonth = $date->format('F');
        $indonesianMonth = $monthNames[$englishMonth];

        $formattedDate = $date->format('d') . ' ' . $indonesianMonth . ' ' . $date->format('Y');

        $cell->addText($formattedDate);
        $cell->addTextBreak(1);

        foreach ([$logbook->activity, $logbook->result] as $data) {
            $cell = $table->addCell();
            $cell->addText($data, ['alignment' => 'justify']);
            $cell->addTextRun(['alignment' => 'justify']);
            $cell->setWidth(100);
        }
    }

    $tempFile = tempnam(sys_get_temp_dir(), 'word_doc') . '.docx';
    $phpWord->save($tempFile);

    // Prepare response for downloading the file
    $response = new BinaryFileResponse($tempFile);
    $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'kerja_praktek_log.docx'
    );

    return $response;

    }
}
