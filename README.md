# uuid
 Oh fantastic! Yet another class to generate uuids that are fully compliant with RFC4122.
 We need another one of these php libraries like we need a hangnail. But here's my roll.
 It's pretty basic because I'm a relatively new developer so this library is probably riddled
 with errors and farts. 

## Usage

### Version 1 *(Time-based)*
```php
//Instantiator supplied with [optional] node value string
//NOTE: node value string must be a 12-character hex string

$uuid = UUID::Timebased('0123456789ab');
```
### Version 3 *(Name-based)*
```php
//Instantiator supplied with [optional] namespace string and namespace ID.
//NOTE: namespace ID MUST be a valid UUID string

$uuid = UUID::NameBased_MD5('namespace string','01234567-89ab-cdef-0123-456789abcdef');
```
### Version 5 *(Name-based)*
```php
$uuid = UUID::NameBased_SHA1('namespace string','01234567-89ab-cdef-0123-4567890abcdef');
```
### Version 4 *(Completely Random)*
```php
//All bits except for version and variant bits are random
$uuid = UUID::RandomBased();
```
 ### Generation
Once the `generate()` method has been called, the `$uuid` object may be used as a string. This method stores the generated value internally, as well as returns the value. The generated string will persist until the `generate()` method is called again.
```php
$uuid->generate();
```
 ### Setting the Node ID 
*(only available with version 1)*<br><br>
An optional 48-bit identifier may be supplied, either at the time of object instantiation
or later if or when the need to do so arises. If a node ID isn't supplied, one is randomly generated.  The node ID must be a 12-character hex string. If anything else is supplied, an exception is thrown. Any node ID, whether generated or otherwise is always available via the `getNode()` method.  The `generate()` method is not a necessary call, but it is an option as this method is fluent.
```php
$uuid->setNode('abacabfacade')->generate();
```
 ### Setting the Namespace
*(only available with versions 3 and 5)*<br><br>
The version 3 and version 5 specifications allow for UUIDs to be generated from hashed namespaces. Namespaces can be provided and the point of instantiation *(see above)* or afterwards. The namespace and can be any string value. If a namespace value is not provided, one is generated and is available via the `getNamespace()` method.  The `generate()` method is not a necessary call, but it is an option as this method is fluent.
```php
$uuid->setNamespace("BIG ZADDY")->generate();
```
### Setting the Namespace ID
*(only available with versions 3 and 5)*<br><br>
Namespace IDs are UUID strings that are provided to the `UUID` object by the user at instantiation, set by the user after instantiation, or generated automatically. The namespace ID _MUST_ a valid 32-character hex string (hyphens are ok; they're filtered out). The `generate()` method is not a necessary call, but it is an option as this method is fluent.
```php
$uuid->setNamespaceID("73ed81aa-b18c-9acc-8acd-418be9da330e")->generate();
```
## Method reference
| Method                          | Return Type | Parameters    | Description                                                                                                                   |
|---------------------------------|-------------|---------------|-------------------------------------------------------------------------------------------------------------------------------|
| `getNamespace()`                | `string`    | (none)        | Returns the current namespace string. If a namespace has been supplied by the user, one is automatically generated.           | 
| `getNamespaceID()`              | `string`    | (none)        | Returns the current namespace ID string. If a namespace Id has not been supplied by the user, one is automatically generated. |
| `getNode()`                     | `string`    | (none)        | Returns the current node ID string.                                                                                           |
| `getVersion()`                  | `int`       | (none)        | Returns the UUID version used to generate strings.                                                                            |
| `setNamespace(string $value)`   | `UUID`      | `namespace`   | Set the namespace string that will be used to hash version 3 and version 5 strings.                                           |
| `setNamespaceID(string $value)` | `UUID`      | `namespaceID` | Set the namespace ID string that will be used to hash version 3 and version 5 strings.                                        |
| `setNode(string $value)`         | `UUID`       | `nodeID`       | Set the node ID that will be used as an identifier in version 1 strings.                                                      |

