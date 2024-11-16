# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1] - 2024-11-16

### Fixed
- Fixed issue where forms that had no configuration setup would error

## [2.0.0] - 2024-08-23

### Added
- Added support for Laravel 11 and Statamic 5.0

### Changed
- Updated to Brevo SDK 2.0
- Updated to Forma 3.0

### Removed
- Removed support for Laravel 9


## [1.1.0] - 2024-03-11

### Added
- Added Brevo PHP SDK
- Added fallback for `SENDINBLUE_API_KEY` environment variable for backwards compatibility

### Changed
- Updated minimum requirements to PHP 8.2, Laravel 9 and Statamic 4.0
- Updated most references from Sendinblue to Brevo

### Fixed
- Fixed issue with Vue-based select dropdowns where they open behind other form elements
- Fixed issue where a form with no name field set but the auto split name field enabled would cause an error

### Removed
- Removed deprecated Sendinblue PHP SDK


## [1.0.3] - 2023-02-22

### Fixed
- Fixed issue with config where only the first form is saved


## [1.0.2] - 2022-12-13

### Fixed
- Fixed error when submitting a contact email address that already exists on Sendinblue
  

## [1.0.1] - 2022-11-17

### Fixed
- Fixed fatal error caused by missing array definition when the field mapping loop runs
  
  
## [1.0.0] - 2022-09-11

### Added
- Added codebase from the SiteRig MailerLite add-on v1.1.0 and updated to work with Sendinblue
