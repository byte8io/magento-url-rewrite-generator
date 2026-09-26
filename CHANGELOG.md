# Changelog — byte8/module-url-rewrite-generator

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.2](https://github.com/byte8io/magento-url-rewrite-generator/compare/v1.0.1...v1.0.2) (2026-09-26)


### Documentation

* add readme for byte8 url rewrite generator ([9c0b8d1](https://github.com/byte8io/magento-url-rewrite-generator/commit/9c0b8d1c28fd4d696d741f05ae1be25f0a1bcadf))
* drop broken logo banner from readme ([6ccbeb7](https://github.com/byte8io/magento-url-rewrite-generator/commit/6ccbeb72b03937800adf6995e4c058c27627efdd))

## [Unreleased]

## [1.0.1] - 2026-09-15
### Fixed
- Fatal reference to non-existent `Byte8\Core\Framework\MessageStorageFactory` in URL rewrite generators

### Changed
- Replace deprecated MessageStorage with MessageCollector (`UrlRewriteInterface::getMessageStorage()` renamed to `getMessageCollector()`)

## [1.0.0] - 2026-04-05
### Changed
- Rebranded from SoftCommerce to Byte8
- Reset versioning for new Byte8 package line
