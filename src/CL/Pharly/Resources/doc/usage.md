# Usage

## Creating an archive

Although there are various formats to archive your files with, I've tried my best to make the
actual code needed to archive them the same.

Below is an example of how you could store files in a ``.zip`` archive, the other formats follow the same approach,
just replace the reference to ``zip`` with the format of your choice.

The example below assumes you have a file called ``myfile.txt`` that you want to archive, along with a directory
located under ``my/directory``.

```php
$pharly  = new Pharly();
$archive = $pharly->archive('myarchive.zip');

// let's add a file...
$archive->addFile('myfile.txt');

// and maybe a directory too!
$archive->buildFromDirectory('my/directory');
```

## Extracting from an archive

Following the example above, this example shows you how you could extract files from the archive we just created:

```php
$pharly  = new Pharly();
$pharly->extract('myarchive.zip', 'path/to/destination');
```

...done!
