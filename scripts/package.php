#!/usr/bin/env php
<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);
date_default_timezone_set('America/Chicago');
include_once('PEAR/PackageFileManager2.php');
if (class_exists('PEAR_PackageFileManager2') === false) {
    echo "\n\n";
    echo "PackageFileManager2 is not installed.\n\n";
    echo "pear install XML_Serializer-0.20.2\n";
    echo "pear install PEAR_PackageFileManager2\n";
    echo "\n";
    exit(1);
}
PEAR::setErrorHandling(PEAR_ERROR_DIE);

/**
 * Recursively populated $GLOBALS['files']
 *
 * @param string $path The path to glob through.
 *
 * @return void
 * @uses   $GLOBALS['files']
 */
function readDirectory($path)
{
    foreach (glob($path . '/*') as $file) {
        if (!is_dir($file)) {
            $GLOBALS['files'][] = $file;
        } else {
            readDirectory($file);
        }
    }
}

$outsideDir = realpath(dirname(dirname(__FILE__)));

$version = trim(file_get_contents($outsideDir . '/VERSION'));

$api_version = $version;
$api_state = 'beta';

$release_version = $version;
$release_state = 'beta';
$release_notes = "This is an alpha release, see readme.md for examples.";

$summary = "A PHP library for swagger resource generation";

$readme = dirname(__DIR__) . '/readme.md';
$description = file_get_contents($readme);

$package = new PEAR_PackageFileManager2();

$package->setOptions(
    array(
         'filelistgenerator' => 'file',
         'outputdirectory' => dirname(dirname(__FILE__)),
         'simpleoutput' => true,
         'baseinstalldir' => '/',
         'packagedirectory' => $outsideDir,
         'dir_roles' => array(
             'benchmarks' => 'doc',
             'bin' => 'script',
             'examples' => 'doc',
             'library' => 'php',
             'library/Swagger' => 'php',
             'tests' => 'test',
         ),
         'exceptions' => array(
             'CHANGELOG' => 'doc',
             'readme.md' => 'doc',
             'VERSION' => 'doc',
             'LICENSE-2.0.txt' => 'doc',
         ),
         'ignore' => array(
             'build/*',
             'package.xml',
             'build.xml',
             'scripts/*',
             '.git',
             '.gitignore',
             'tests/phpunit.xml',
             'tests/build*',
             '.project',
             '.buildpath',
             'releases',
             '.settings',
             'vendor/*',
             '*.iml',
             'composer.*',
             '*.tgz'
         )
    )
);

$package->setPackage('Swagger');
$package->setSummary($summary);
$package->setDescription($description);
$package->setChannel('zircote.com/pear');
$package->setPackageType('php');
$package->setLicense(
    'Apache 2.0',
    'http://www.apache.org/licenses/LICENSE-2.0'
);

$package->setNotes($release_notes);
$package->setReleaseVersion($release_version);
$package->setReleaseStability($release_state);
$package->setAPIVersion($api_version);
$package->setAPIStability($api_state);
/**
 * Dependencies
 */

$maintainers = array(
    array(
        'name' => 'Robert Allen',
        'user' => 'zircote',
        'email' => 'zircote@gmail.com',
        'role' => 'lead',
    )
);

foreach ($maintainers as $_m) {
    $package->addMaintainer(
        $_m['role'],
        $_m['user'],
        $_m['name'],
        $_m['email']
    );
}

$files = array(); // classes and tests
readDirectory($outsideDir . '/library');
readDirectory($outsideDir . '/tests');

$base = $outsideDir . '/';

foreach ($files as $file) {

    $file = str_replace($base, '', $file);

    $package->addReplacement(
        $file,
        'package-info',
        '@name@',
        'name'
    );

    $package->addReplacement(
        $file,
        'package-info',
        '@package_version@',
        'version'
    );
}

$files = array(); // reset global
readDirectory($outsideDir . '/library');

foreach ($files as $file) {
    $file = str_replace($base, '', $file);
    $package->addInstallAs($file, str_replace('library/', '', $file));
}


$package->setPhpDep('5.3.3');

$package->setPearInstallerDep('1.7.0');

$package->addPackageDepWithChannel('required', 'DoctrineCommon', 'pear.doctrine-project.org');
$package->addPackageDepWithChannel('optional', 'symfony', 'pear.symfony-project.com');

$package->generateContents();
$package->addRelease();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}
