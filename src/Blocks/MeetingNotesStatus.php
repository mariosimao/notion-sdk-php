<?php

namespace Notion\Blocks;

enum MeetingNotesStatus: string
{
    case TranscriptionNotStarted = "transcription_not_started";
    case TranscriptionPaused = "transcription_paused";
    case TranscriptionInProgress = "transcription_in_progress";
    case TranscriptionFailed = "transcription_failed";
    case SummaryInProgress = "summary_in_progress";
    case NotesReady = "notes_ready";
}
