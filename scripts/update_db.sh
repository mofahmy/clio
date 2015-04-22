#!/bin/sh

SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

sqlite3 ${SCRIPT_DIR}/cfps.db "delete from event;"
ruby ${SCRIPT_DIR}/scrape_cfps.rb
