## Change Log

### 1.5.5
 - Better retry strategy using two different provider (Improve high-availability of the solution, retry is done on algolianet.com)
 - Read operations are performed to APPID-dsn.algolia.net domain first to leverage Distributed Search Network (select the closest location)
 - Improved timeout strategy: increasse timeout after 2 trials & have a different read timeout for search operations

### 1.5.4
 - Added allOptional support for removeWordsIfNoResult

### 1.5.3
 - Added global timeout for a query
 - Fixed missing namespace escaping

### 1.5.2
 - Changed default connect timeout from 1s to 5s (was too agressive and timeout was triggered on some machines)

### 1.5.1
 - Changed default connect timeout from 30s to 1s and add an option to override it

### 1.5.0
 - Nove to .net instead of .io
 - Ability to pass a custom certificate path

### 1.4.1
- Fixed the retry on hosts

### 1.4.0
- Fixed performance issue with curl_multi_select
- Read the API client version from composer.json if possible
- Add setExtraHeader

### 1.3.5
- updated default typoTolerance setting & updated removedWordsIfNoResult documentation
- Added updateACL

### 1.3.4
- Add notes about the JS API client
- Remove automatic affectation of updateObject on batchObjects
- added aroundLatLngViaIP documentation
- Add documentation about removeWordsIfNoResult
- Fixed addUserKey method: it takes an array, not a string

### 1.3.3
- Added check on empty string as index name
- Add tutorial links + minor enhancements

### 1.3.2
- Added documentation of suffix/prefix index name matching in API key (@speedblue)
- Fixed parse error

### 1.3.1
- Added restrictSearchableAttributes

### 1.3.0 
- Fix CA path
- Code reorganization

### 1.2.2
- Add deleteByQuery
- searchDisjunctiveFaceting: do not try to retrieve any attributes in the underlying faceting queries
- Added support of createIfNotExists
- Added createIfNotExists

### 1.2.1
- Fixed array syntax to be compliant with PHP 5.3 and HHVM
- Added disableTypoToleranceOn & altCorrections index settings
- Added getObjects method
- Fixed handling of error message

### 1.2.0
- Added analytics,synonyms,enableSynonymsInHighlight query parameters
- Force CURLOPT_NOSIGNAL to ensure curl is not using UNIX signals to detect timeouts, see http://ravidhavlesha.wordpress.com/2012/01/08/curl-timeout-problem-and-solution/
- Add disjunctive faceting helper
- Add typoTolerance & allowsTyposOnNumericTokens query parameters.
- Fix Exception class instantiation.
- Fixed performance bug with multi_exec

### 1.1.9
- New numericFilters documentation
- Wait until task is published using destination index. (@redox)
- First implementation of curl_multi_*

### 1.1.8
- Change sha256 to hmac with sha256
- Add onlyErrors parametter to getLogs
- Add advancedSyntax query parameter documentation
- Improve handling of http errors

### 1.1.7
- Added deleteObjects
- Ability to generate secured API keys + specify list of indexes targeted by user keys
- Add multipleQueries

### 1.1.6
- Fixed a bug in PHP client that was leading to BAD REQUEST
- Removed debug code

### 1.1.5
- Include package version in the user-agent

### 1.1.2
- Fixed typo
- Try fixing travis build
- Improved test suite
- Include version number in the comment header, bump to 1.1.2

### 1.1.1
- Travis integration
- Add badges
- Expose batch request

### 1.1.0
- Minor fixes

### 1.0.0
- Initial import
