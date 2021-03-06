<?php
if(!isset($lang->attend)) $lang->attend = new stdclass();
$lang->attend->common       = 'Attend';
$lang->attend->personal     = 'My Attend';
$lang->attend->department   = 'Department attend';
$lang->attend->company      = 'Company attend';
$lang->attend->detail       = 'Detail attend';
$lang->attend->edit         = 'Manual sign';
$lang->attend->edited       = 'Signed in';
$lang->attend->leave        = 'Leave';
$lang->attend->leaved       = 'Already leave';
$lang->attend->trip         = 'Trip';
$lang->attend->overtime     = 'Overtime';
$lang->attend->overtimed    = 'Already overtime';
$lang->attend->review       = 'Review attendance';
$lang->attend->settings     = 'Setting';
$lang->attend->export       = 'Export';
$lang->attend->stat         = 'Stat';
$lang->attend->saveStat     = 'Save Stat';
$lang->attend->exportStat   = 'Export Stat';
$lang->attend->detail       = 'Detail Attends';
$lang->attend->exportDetail = 'Export Detail Attends';
$lang->attend->browseReview = 'Review List';

$lang->attend->id            = 'ID';
$lang->attend->date          = 'Date';
$lang->attend->account       = 'User';
$lang->attend->signIn        = 'Sign in';
$lang->attend->signOut       = 'Sign out';
$lang->attend->status        = 'Status';
$lang->attend->ip            = 'IP';
$lang->attend->device        = 'Device';
$lang->attend->desc          = 'description';
$lang->attend->dayName       = 'Day name';
$lang->attend->report        = 'Report';
$lang->attend->AM            = 'AM';
$lang->attend->PM            = 'PM';
$lang->attend->ipList        = 'IP List';
$lang->attend->noAttendUsers = 'Users Without Attendance';

$lang->attend->user          = 'User';
$lang->attend->begin         = 'Begin';
$lang->attend->end           = 'End';
$lang->attend->search        = 'Search';

$lang->attend->manualIn     = 'Manual sign in';
$lang->attend->manualOut    = 'Manual sign out';
$lang->attend->reason       = 'Reason';
$lang->attend->reviewStatus = 'Review status';
$lang->attend->reviewedBy   = 'Reviewed By';
$lang->attend->reviewedDate = 'Reviewed Date';
$lang->attend->deserveDays  = 'Deserved Days';
$lang->attend->actualDays   = 'Actual Days';

$lang->attend->statusList['normal']   = 'Normal';
$lang->attend->statusList['late']     = 'Late';
$lang->attend->statusList['early']    = 'Leave early';
$lang->attend->statusList['both']     = 'Late and Leave early';
$lang->attend->statusList['absent']   = 'Absent';
$lang->attend->statusList['leave']    = 'Ask for leave';
$lang->attend->statusList['trip']     = 'Biz trip';
$lang->attend->statusList['rest']     = 'Rest day';
$lang->attend->statusList['overtime'] = 'Overtime';

$lang->attend->abbrStatusList['normal']   = '√';
$lang->attend->abbrStatusList['late']     = 'Late';
$lang->attend->abbrStatusList['early']    = 'Early';
$lang->attend->abbrStatusList['both']     = 'L+E';
$lang->attend->abbrStatusList['absent']   = 'Absent';
$lang->attend->abbrStatusList['leave']    = 'Leave';
$lang->attend->abbrStatusList['trip']     = 'Biz';
$lang->attend->abbrStatusList['rest']     = 'Rest';
$lang->attend->abbrStatusList['overtime'] = 'Over';

$lang->attend->markStatusList['normal']   = '√';
$lang->attend->markStatusList['late']     = '=';
$lang->attend->markStatusList['early']    = '>';
$lang->attend->markStatusList['both']     = '=>';
$lang->attend->markStatusList['absent']   = 'x';
$lang->attend->markStatusList['leave']    = '!';
$lang->attend->markStatusList['trip']     = '$';
$lang->attend->markStatusList['rest']     = '~';
$lang->attend->markStatusList['overtime'] = '+';

$lang->attend->reasonList['normal'] = 'Normal';
$lang->attend->reasonList['trip']   = 'Biz trip';
$lang->attend->reasonList['leave']  = 'Ask for leave';

$lang->attend->reviewStatusList['wait']   = 'Wait';
$lang->attend->reviewStatusList['pass']   = 'Pass';
$lang->attend->reviewStatusList['reject'] = 'Reject';

$lang->attend->inSuccess  = 'Sign in success';
$lang->attend->inFail     = 'Sign in fail';
$lang->attend->outSuccess = 'Sign out success';
$lang->attend->outFail    = 'Sign out fail';

$lang->attend->signInLimit  = 'Latest time of sign in';
$lang->attend->signOutLimit = 'Earlies time of sign out';
$lang->attend->workingDays  = 'Working days per week';
$lang->attend->workingHours = 'Working hours per day';
$lang->attend->mustSignOut  = 'Must sign out';

$lang->attend->workingDaysList['5']  = "Monday ~ Friday";
$lang->attend->workingDaysList['6']  = "Monday ~ Saturday";
$lang->attend->workingDaysList['7']  = "Monday ~ Sunday";
$lang->attend->workingDaysList['12'] = "Sunday ~ Thursday";
$lang->attend->workingDaysList['13'] = "Sunday ~ Friday";

$lang->attend->mustSignOutList['yes'] = 'need';
$lang->attend->mustSignOutList['no']  = 'not need';

$lang->attend->weeks = array('First week', 'Second week', 'Third week', 'Fourth week', 'Fifth week', 'Sixth week');

$lang->attend->notice['today']    = "<p>Your yestoday's attendance is %s, <a href='%s' %s>Go to edit.</a></p>";
$lang->attend->notice['yestoday'] = "<p>Your today's attendance is %s, <a href='%s' %s>Go to edit.</a></p>";
$lang->attend->notice['absent']   = "No record";

$lang->attend->confirmReview['pass']   = 'Are sure to pass it?';
$lang->attend->confirmReview['reject'] = 'Are sure to reject it?';

$lang->attend->settings   = 'Normal Settings';
$lang->attend->setManager = 'Depatment Manager Settings';

$lang->attend->note = new stdClass();
$lang->attend->note->ip       = "Use comma between two IPs, and support IP segment, for example 192.168.1.*";
$lang->attend->note->allip    = 'All IP';
$lang->attend->note->IPDenied = 'IP denied.';

$lang->attend->h = 'hours';
$lang->attend->m = 'minutes';
$lang->attend->s = 'seconds';
