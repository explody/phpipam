#!/usr/bin/python3

from sqlalchemy.ext.automap import automap_base
from sqlalchemy import create_engine, MetaData, Table, Column, ForeignKey

import pprint
import simplejson as json

engine = create_engine("mysql://phpipam_dev:dA9xQDh0CFaRue2rFBWyCilPDNRRE4jdwwW@data1-pao.nasa.pivotal.io/phpipam_dev", connect_args={ 'ssl': { 'ca': '/etc/pivotal-combined.pem' } } )

metadata = MetaData()
metadata.reflect(engine)
Base = automap_base(metadata=metadata)


pprint.pprint(metadata.tables.keys())

