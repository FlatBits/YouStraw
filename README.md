# YouStraw
### Extract a ~~Tube~~ _Straw_ from YouTube

**Important :** Since the data we need is not provided by Google's official API, this library does not require you to 
provide an API key or any other credentials. However, the the library heavily relies on undocumented APIs and DOM 
parsing. For these reason, I do not recommend the use of this library in mission critical environment since it can 
break at any moment from a change in YouTube's code


## Installation
Install the latest version via [Composer](https://getcomposer.org)

```bash
composer require flatbits/youstraw
```

Include Composer's autoload file, and don't forget to add the `use` statements

```php
require_once('./vendor/autoload.php');
use FlatBits\YouStraw\Straw;
```

#### Optional Requirement
* [FFmpeg](https://www.ffmpeg.org/) - Some formats are not directly delivered from YouTube, this library requires 
FFmpeg for those [Converted formats](#converted)

## Usage

##### Download a single video
```php
<?php
require_once('./vendor/autoload.php');
use FlatBits\YouStraw\Format;
use FlatBits\YouStraw\Format\Mp4;
use FlatBits\YouStraw\Straw;

$videoId = 'xEoMC7czIxA';

// Create a Straw, the handle to our video
$straw = new Straw($videoId);

// Download a high quality (720p) mp4 to the video dir.
$straw->download('../cache/video', new Mp4(Format::QUALITY_HIGH));
// Make sure the script has write access to the specified directory.
```

##### Download music from a playlist
```php
<?php
require_once('../vendor/autoload.php');
use FlatBits\YouStraw\Format;
use FlatBits\YouStraw\Format\Mp3;
use FlatBits\YouStraw\StrawCollection;

$playlistId = 'PLDoXbhQs-J6TMo69UeDvlxmDf7nkR8OSm';

// Create the StrawCollection, a helper for batch downloads and playlist parsing
$strawCollection = StrawCollection::fromPlaylist($playlistId);

// Download the collection mp3 files to the music dir.
$strawCollection->downloadAll('../cache/music', new Mp3());
```

Please look in the examples folder or through the source code for further usage.

## Formats

### Natives
##### Those format are directly fetched from YouTube
|Format                     | Available qualities |
|---------------------------|---------------------|
| Mp4                       | High, Medium        |
| WebM                      | Medium              |
| Flv                       | Low                 |
| ThreeGP (3gp)             | Low                 |

### Converted
##### Those format are converted from a video, you will need [FFmpeg](https://www.ffmpeg.org/) on your system to use these formats.
#### Audio
|Format                     | Available qualities |
|---------------------------|---------------------|
| Mp3                       | High, Medium        |
| Flac                      | High, Medium        |

More formats will be added in the future, if a format you want is not listed here, please open an issue.