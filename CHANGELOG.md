# Changelog — byte8/module-url-rewrite-generator

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
