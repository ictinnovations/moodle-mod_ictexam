# ICTEXAM activity module for Moodle

`mod_ictexam` adds an ICTEXAM assessment as a native activity in a Moodle course.
Students open the activity and take the exam without leaving Moodle; their score is
written back into a single Moodle grade item for the activity.

## What it does

- Adds an **ICTEXAM assessment** activity type to the course activity chooser.
- Launches a published ICTEXAM paper over **LTI 1.3**, embedded in the course page.
- Creates and owns **one grade item** per activity, and receives the score from ICTEXAM
  through the `mod_ictexam_set_grade` web service. Raw marks are stored, not a
  percentage — 15 out of 20 is recorded as `15.00` against a maximum of 20.
- Passes course context to ICTEXAM as custom LTI parameters: the bound assessment,
  class, subject, the course return URL, and (on IOMAD sites) the user's company.
- Lets the teacher bind the paper either in the activity settings form or, if left
  blank, by choosing from their paper list the first time they open the activity.
- Supports course backup and restore, and the course recycle bin.

## Requirements

- Moodle 4.1 or later (tested through 4.5).
- **An ICTEXAM account.** This plugin is a client for the ICTEXAM assessment service at
  <https://ictlms.net> — it does not deliver or grade exams on its own, and it is not
  useful without a subscription to that service.
- The ICTEXAM backend registered on this site as an **External tool (LTI 1.3)** preset,
  with the AGS and NRPS services enabled.

## Installation

1. Copy this directory to `{moodleroot}/mod/ictexam`.
2. Visit **Site administration → Notifications** to complete the database install.
3. Register ICTEXAM under **Site administration → Plugins → External tool → Manage tools**
   as an LTI 1.3 tool, with *IMS LTI Assignment and Grade Services* and
   *IMS LTI Names and Role Provisioning* enabled.
4. Enable the web service function `mod_ictexam_set_grade` for the ICTEXAM service user,
   so grades can be written back.
5. Add an **ICTEXAM assessment** activity to a course and select the tool you registered.

## Privacy

The plugin stores no personal data in its own database table — the `ictexam` table holds
activity configuration only, and grades are held in the core gradebook.

The activity **does** send personal data to the external ICTEXAM service as part of every
launch: the Moodle user ID, full name and email address of the person opening the
activity. The score returned for the attempt is recorded in the Moodle gradebook. This is
declared through the Privacy API (see `classes/privacy/provider.php`) and appears in the
site's data registry.

Note that name and email are sent on every launch and are not currently configurable per
activity. If your institution needs to suppress them, that has to change in `view.php`,
where the LTI instance is built.

## Grades

The activity owns exactly one grade item (`itemtype=mod`, `itemmodule=ictexam`), created
by `ictexam_add_instance()`. ICTEXAM pushes scores into that item through the plugin's
own web service, and the plugin deliberately does **not** also create an AGS line item —
having both produces a duplicate grade column.

## License

GNU GPL v3 or later — see [LICENSE](LICENSE).

Copyright 2026 ICT Innovations.

Project website

ICTExam https://www.ictlms.net


## Links

**Developed by** [ICT Innovations](https://www.ictinnovations.com), the company behind [ICT Exam](https://www.ictlms.net). More of our open source work is listed at https://ictinnovations.com/projects/.
