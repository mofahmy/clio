#!/bin/sh

sqlite3 /srv/http/clio/cfps.db "delete from event;"
ruby /srv/http/clio/scrape_cfps.rb
