These example scripts in this directory serve as example however large parts of
them are very implementation specific. There are the actual required fields
TestSwarms' perspective.

For more information about the fields, view the AddjobPage in your browser.

## Request

* Must be a `POST` request
* To `http://swarm.example.org/api.php?action=addjob`

## Fields

### Authentication

Username with a user that will be the owner of this new job.

* `authUsername`: Matching entry from `users.name` field in the database 
* `authToken`: Matching entry from `users.auth` field in the database

### Job information

* `jobName`: Job name (may contain HTML) (e.g. `Foobar r123` or `Lorem ipsum <a href="..">#h0s4</a>`)
* `runMax`

### Browsers

* `browserSets[]=`: One of `current`, `popular`, `gbs`, `beta`, `mobile`.
   Correspond to the `useragents` table
* `browserSets[]=` ..
* `browserSets[]=` ..

### Runs

Run name/url pairs.

* `runNames[]`: Run name (e.g. "module foo")
* `runUrls[]`: Run URL (absolute url, including http:// or https://)

* `runNames[]`: ..
* `runUrls[]`: ..
