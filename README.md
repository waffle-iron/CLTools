[![Stories in Ready](https://badge.waffle.io/magneticstain/CLTools.png?label=ready&title=Ready)](https://waffle.io/magneticstain/CLTools)
# CLTools
A set of python and PHP scripts that use Craigslist to help find the perfect apartment.

## Inspired By
* https://www.dataquest.io/blog/apartment-finding-slackbot/

## Requirements
* https://github.com/juliomalegria/python-craigslist
  * `pip install python-craigslist`
* python mysqldb
  * `pip install MySQL-python`
* Debian
  * libmysqlclient-dev
  
## Getting Started
### Install


### Configuration
#### Apache


#### CLTools Database Settings


## Services
### CLStore
CLStore is a set of python scripts and libraries that scrapes CraigsList postings based on our defined parameters and stores them 
for further analysis.

#### Usage
```
usage: clstore.py [-h] -c CONFIGFILE [-s SEARCHSLEEPTIME] [-v|--verbosity (DEBUG|INFO|WARNING|ERROR|CRITICAL)
```

##### Example
```
./clstore.py -c /opt/CLTools/conf/ca_search.cfg -s 15 -v INFO
```

This example starts CLStore using the config file `/opt/CLTools/conf/ca_search.cfg` and a search query sleep time of `15` seconds.
The logs will contain any log messages generated with a severity level of `INFO` and above.

### CLData
CLData is a backend service written in PHP that provides calculations, metrics, and any other raw data that's needed by the web application.
This data can be accessed directly by the user using REST calls, and is also utilized by CLWeb - the CLTools analysis front-end.

#### API
For instructions on using the CLTools API, see our [CLData API doc](https://github.com/magneticstain/CLTools/wiki/CLData-API-Guide).

### CLWeb
CLWeb is the front-end web application for fetching and displaying the listing analysis data to the user in an elegant and easy-to-use way.
This webapp utilizes PHP to provide an HTML5/JS webpage.

## Contributing
If you're interested in contributing to CLTools, please see our [Developer's Guide](https://github.com/magneticstain/CLTools/wiki/Developer's-Guide) in the project wiki.