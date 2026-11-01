<?php

namespace Notion\Agents;

enum PauseReason: string
{
    case RunLimit = "run_limit";
    case CreditLimit = "credit_limit";
    case RunawayCreditUsage = "runaway_credit_usage";
    case WorkspaceCreditLimit = "workspace_credit_limit";
    case OwnerAllowanceLimit = "owner_allowance_limit";
    case FailureLimit = "failure_limit";
    case MarkSessionFailedAutopause = "mark_session_failed_autopause";
    case DisabledFromWorkspaceSettings = "disabled_from_workspace_settings";
    case DisabledFromWorkspaceSettingsButResumable = "disabled_from_workspace_settings_but_resumable";
    case DisabledFromApi = "disabled_from_api";
    case DisabledFromAgentSettings = "disabled_from_agent_settings";
    case DisabledDueToNoMembersWithAccess = "disabled_due_to_no_members_with_access";
    case DisabledDueToLackOfEditors = "disabled_due_to_lack_of_editors";
    case DisabledByNotion = "disabled_by_notion";
    case InternalError = "internal_error";
    case NeedsUserReview = "needs_user_review";
    case ToolUnavailable = "tool_unavailable";
}
