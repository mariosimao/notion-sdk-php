<?php

namespace Notion\Databases;

/**
 * Type of a typed database, built from one of Notion's canonical schemas.
 *
 * Only `Tasks`, `Projects` and `Skills` can be used to create databases
 * through the API. The remaining cases may be reported by Notion on
 * databases created in the Notion app.
 */
enum DatabaseType: string
{
    case Tasks = "tasks";
    case Projects = "projects";
    case Sprints = "sprints";
    case Docs = "docs";
    case Wiki = "wiki";
    case Meetings = "meetings";
    case MeetingNotes = "meeting_notes";
    case Skills = "skills";
    case GithubPrs = "github_prs";
    case Unknown = "unknown";

    /** Whether the API accepts this type when creating a database. */
    public function isCreatable(): bool
    {
        return match ($this) {
            self::Tasks, self::Projects, self::Skills => true,
            default => false,
        };
    }
}
