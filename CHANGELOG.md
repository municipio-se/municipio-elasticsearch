## 0.2.5 (February 11, 2021)

### New features

- Enable sorting of results by specific sort key and sort order

## 0.2.4 (February 5, 2021)

### New features

- Handle 413 HTTP errors when indexing. This means that posts with large file
  attachements will be indexed separately and file contents will be skipped if
  they are still too large to index.
- Create log entries in Stream when indexing.

## 0.2.3 (February 2, 2021)

- Fix `municipio_elasticsearch_autosuggest_options` filter

## 0.2.2 (February 2, 2021)

- Remove jQuery dependency

## 0.2.1 (December 14, 2020)

- Add ability to add suggestions on the Search options page
