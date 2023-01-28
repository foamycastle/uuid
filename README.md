# uuid
 Oh, fantastic! Yet another class to generate uuids that are fully compliant with RFC4122.
 We need another one of these php libraries like we need a hangnail but here's my roll.
 It's pretty basic because I'm a relatively new developer so this library is probably ~~riddled
 with errors and farts~~ a totally solid library. 

## Usage

### Version 1 *(Time-based)*
```php
//NOTE: optional node value string MUST be a 12-character hex string
$uuid_timebased = new TimeBasedUUID();
$uuid_timebased = new TimeBasedUUID('0123456789ab');
echo $uuid_timebased;
//output f3b64e06-9f09-11ed-8000-0123456789ab
```
### Version 3 *(Name-based)*
```php
//NOTE: namespace ID MUST be a valid UUID string
//Arguments are required
$uuid_md5based = new MD5BasedUUID('namespace','01234567-89ab-cdef-0123-456789abcdef');
echo $uuid_md5based;
//output efeca3ca-05ff-3d3e-91c4-ce451deb58fc
```
### Version 5 *(Name-based)*
```php
//NOTE: namespace ID MUST be a valid UUID string
//Arguments are required
$uuid_sha1based = new SHA1BasedUUID('namespace','01234567-89ab-cdef-0123-456789abcdef');
echo $uuid_sha1based;
//output 171cac9a-9c41-58fe-8642-c4ca1df98e89
```
### Version 4 *(Completely Random)*
```php
//All bits except for version and variant bits are random
$uuid_randombased=new RandomBasedUUID();
echo $uuid_randombased;
//output d8c9c30a-da4e-41aa-79b3-b232208b7e76
```
 ### Generation
The `generate()` compiles all the elements of the UUID and returns the UUID string. It is important to note that this method is called upon instantiation.
```php
$uuid->generate();
//output 7381adef-23ff-4321-abcd-839201657483
```
### Reuse (value persistence)
Once generated, the UUID value will persist in the object until the `generate()` method is called again. The object can be used as a string.
```php
echo $uuid;
//output 7381adef-23ff-4321-abcd-839201657483
```
 ### Setting the Node ID 
*(only available with version 1)*<br><br>
An optional 48-bit identifier may be supplied, either at the time of object instantiation
or later if or when the need to do so arises. If a node ID isn't supplied, one is randomly generated.  The node ID must be a 12-character hex string. If anything else is supplied, an exception is thrown. Any node ID, whether generated or otherwise is always available via the `getNode()` method. This method is fluent.
```php
$uuid->setNode('abacabfacade');
```
 ### Setting the Namespace
*(only available with versions 3 and 5)*<br><br>
The version 3 and version 5 specifications allow for UUIDs to be generated from hashed namespaces. Namespaces can be provided and the point of instantiation *(see above)* or afterwards. The namespace and can be any string value. If a namespace value is not provided, one is generated and is available via the `getNamespace()` method. This method is fluent.
```php
$uuid->setNamespace("BIG ZADDY");
```
### Setting the Namespace ID
*(only available with versions 3 and 5)*<br><br>
Namespace IDs are UUID strings that are provided to the `UUID` object by the user at instantiation, set by the user after instantiation, or generated automatically. The namespace ID _MUST_ a valid 32-character hex string (hyphens are ok; they're filtered out). This method is fluent.
```php
$uuid->setNamespaceID("73ed81aa-b18c-9acc-8acd-418be9da330e");
```
## Method reference
| Method                          | Return Type | Parameters    | Description                                                                                                                   |
|---------------------------------|-------------|---------------|-------------------------------------------------------------------------------------------------------------------------------|
| `generate()`                    | `string`    | (none)        | Generates and returns a new UUID string.                                                                                      | 
| `getNamespace()`                | `string`    | (none)        | Returns the current namespace string. If a namespace has not been supplied by the user, one is automatically generated.       | 
| `getNamespaceID()`              | `string`    | (none)        | Returns the current namespace ID string. If a namespace ID has not been supplied by the user, one is automatically generated. |
| `getNode()`                     | `string`    | (none)        | Returns the current node ID string.                                                                                           |
| `getVersion()`                  | `int`       | (none)        | Returns the UUID version used to generate strings.                                                                            |
| `setNamespace(string $value)`   | `UUID`      | `namespace`   | Set the namespace string that will be used to hash version 3 and version 5 strings.                                           |
| `setNamespaceID(string $value)` | `UUID`      | `namespaceID` | Set the namespace ID string that will be used to hash version 3 and version 5 strings.                                        |
| `setNode(string $value)`        | `UUID`       | `nodeID`       | Set the node ID that will be used as an identifier in version 1 strings.                                                      |

