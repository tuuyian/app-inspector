'use strict';

var fs = require('fs');
var extract = require('ipa-extract-info');
var filename = "uploads/" + process.argv[2]

var fd = fs.openSync(filename, 'r');

extract(fd, function(err, info, raw){
  if (err) throw err;
  const {"0": names} = {...info}
  const infoPlist = Object.assign(
    {},
    {
        names
    }
);
  
  console.log(infoPlist)
  console.log("</br></br>Package Name: " + infoPlist.names["CFBundleIdentifier"] + "</br>" + "Version Code: " + infoPlist.names["CFBundleVersion"]);  // the unparsed plist
});
 