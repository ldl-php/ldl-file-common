# LDL Framework File Validators Changelog

All changes to this project are documented in this file.

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [vx.x.x] - xxxx-xx-xx

### Added

- feature/1200442297082017 - Add ResetValidatorInterface to HasRegexContentValidator
- feature/1200375818876653 - Add description to validators (fix wrong +rb read mode on HasRegexContentValidator)
- feature/1200203297621236 - Normalize file validators and collections
- feature/1200637529087859 - Use ComparisonOperatorHelper on FileSizeValidator
- feature/1200748544420027 - Add FileTypeHelper & standard exceptions
- feature/1200826040420667 - Add PathValidator / Add PathHelper / Use FileTypeHelper on DirectoryCollection
- feature/1201380558680875 - Add getLines method to ReadableFileCollection and ReadWriteFileCollection / Add File, Directory and Tree 
- feature/1201393911175051 - Create generic file collection / Add useful methods to FileTree::class
- feature/1201480582483921 - Add File::getLinesAsString() : string method
- feature/1201506215455290 - Add File::getExtension() : string method
- feature/1201612851578934 - Add linking methods and Link class / Separate interfaces / Observable pattern on tree
- feature/1201645038896279 - Add date access methods to LDLFileInterface
- feature/1201645038896283 - Add sort by date methods to FileTreeInterface
- feature/1201645038896275 - Add file ownership capabilities to LDLFileInterface

### Changed

- fix/1200713875634262 - Change reset to onBeforeValidate on HasRegexContentValidator. Add getChainItems on each collection
- fix/1200630491660400 - Remove validators config
- fix/1200624131677611 - Fix validators - Remove dumpable and BasicValidatorConfig. Also add a description
- fix/1200410494797363 - Fix HasRegexContentValidator
- fix/1200366404543319 - Fix validators and configs to comply with ldl-validators
- fix/1201302588223604 - Move FileTypeHelper constants to FileTypeConstants
- fix/1201487027413411 - DirectoryCollection validation is added AFTER parent::__construct is called / Add factories to directory & file collections

