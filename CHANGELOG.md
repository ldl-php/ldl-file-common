# LDL file common CHANGELOG.md

All changes to this project are documented in this file.

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [vx.x.x] - xxxx-xx-xx

### Added

- feature/1201948369036945 - Add DirectoryCollection::getRelativePaths() : iterable
- feature/1201948255840038 - Add Directory::getRealPath() : string
- feature/1201927806182806 - FilePathHelper::getRelativePath(string $from, string $to) : string
- feature/1201913358758253 - Add FileHelper::createSysTempFile() : FileInterface
- feature/1201913358758249 - Add DirectoryHelper::getSysTempDir() : DirectoryInterface
- feature/1201884322133686 - Add DirectoryInterface::mmkdir
- feature/1201707436744968 - Add force options to Directory::create


### Changed

- fix/1201928615675067 - Fix FilePathHelper::getRelativePath
- fix/1201885744311701 - Fix File::copy
- fix/1201885274091941 - Fix Directory::mmkdir / Add mmkdir example
