const util = require('util');
const ApkReader = require('adbkit-apkreader')


var filename = "uploads/" + process.argv[2]

ApkReader.open(filename)
  .then(reader => reader.readManifest())
  //.then(manifest => console.log(manifest.toString('utf-8').split('\n')))
  .then(manifest => console.log("Version Code: " + manifest['versionCode'] + "</br>" + "Version Name: " + manifest['versionName'] + "</br>Package: " + manifest['package'] + "</br>" + "Uses Configuration: "  + "</br>Debuggable: " + manifest.application["debuggable"]))
