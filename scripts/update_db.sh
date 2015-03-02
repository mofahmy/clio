#!/bin/sh

db_dir = $1
ruby_dir = $2

sqlite3 ${db_dir}/cfps.db "delete from event;"
ruby ${ruby_dir}/scrape_cfps.rb
