# Changelog

All notable changes to `mod_ictexam` are documented here.
This project follows [Semantic Versioning](https://semver.org/).

## [0.2.0] - 2026-08-25

Release preparation for the Moodle plugins directory.

### Added
- GPL v3 header and file-level PHPDoc block in every PHP file.
- `README.md`, `LICENSE` and this changelog.
- `$plugin->supported` declaring Moodle 4.1 to 4.5.

### Changed
- Maturity raised from alpha to beta.
- The privacy provider now implements `\core_privacy\local\metadata\provider` and
  declares the user ID, full name, email address and grade sent to or received from
  the external ICTEXAM service. It previously used `null_provider`, which claimed no
  personal data was involved — inaccurate, since every launch transmits the user's
  name and email.

## [0.1.3] - 2026-06-10

### Added
- Moodle 2 backup and restore classes (`backup/moodle2/`). Without them, and with
  `FEATURE_BACKUP_MOODLE2` declared true, the course recycle bin threw
  `Class "backup_ictexam_activity_task" not found` and ICTEXAM activities could not
  be deleted.

## [0.1.2]

### Fixed
- Grade passback now sends raw marks rather than a percentage. The grade item maximum
  is set to the exam's total, and the score is clamped to that range, so 15 out of 20
  records as `15.00` instead of `75`.

## [0.1.1]

### Added
- `mod_ictexam_set_grade` web service, so scores land in the activity's own grade item
  instead of a second AGS-created column.

## [0.1.0]

### Added
- Initial release: ICTEXAM assessment activity, LTI 1.3 launch, course context passed
  as custom parameters, single grade item per activity.
