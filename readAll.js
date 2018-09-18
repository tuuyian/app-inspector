const PkgReader = require('reiko-parser');

// for apk
const reader = new PkgReader("apps/demo.apk", 'apk', { withIcon: true });
reader.parse((err, pkgInfo) => {
  if (err) {
    console.error(err);
  } else {
    console.log(pkgInfo); // pkgInfo.icon is encoded to base64
  }
});

/* // for ipa
const reader = new PkgReader("apps/BatteryLife.ipa", 'ipa', { withIcon: false });
console.log("hellop")
reader.parse((err, pkgInfo) => {
  if (err) {
    console.error(err);
  } else {
    console.log(pkgInfo);
  }
});*/