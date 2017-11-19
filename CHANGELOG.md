Changelog
=========
## [1.0.0-beta.6] - 2017-11-19
### Added
- Model query class that returns models 
- Object query class that returns objects

### Fixed
- References of yii\base\Object to yii\base\BaseObject

## [1.0.0-beta.5] - 2017-09-22
### Fixed
- Bug where model service was returning a new record when an existing record exists

### Added
- Various action traits
- An abstract controller to assist with more robust action and response handling
- Various action based js methods to assist with CP calls using correct verbs
- An abstract view handler to assist w/ switching between rendering an admin template and a front-end template
- Filters to assist with preparing data and responses
- Some helpers to help us out

## [1.0.0-beta.4] - 2017-07-10
### Changed
- ArrayHelper::firstValue compatibility with Craft beta.21

## [1.0.0-beta.3] - 2017-05-19
### Added
- New user model trait
 
## [1.0.0-beta.2] - 2017-05-16
### Added
- New model/element/object traits
 
### Changed
- Refactored services and models to user traits.
 
## [1.0.0-beta.1] - 2017-05-15

### Changed
- Model service no longer uses a behavior in favor of trait
- Object service no longer uses a behavior in favor of trait
- Element behavior has been removed in favor of trait

## [1.0.0-beta] - 2017-03-22

Initial release.
