#!/usr/bin/ruby

require 'net/http'
require 'sqlite3'

$script_dir = File.dirname(__FILE__)

# Read in categories names from a text file (one per line)
def load_categories(file)
  categories = IO.readlines(file)

  categories.each do |cat|
    cat.strip!
  end

  return categories
end

# Return the id of a category in the database given its name
def get_category_id(category)
  db = SQLite3::Database.new "#{$script_dir}/cfps.db"
  category_id = db.get_first_value("select id from category where name = ?", [category])

  return category_id
end

# Return the name of a category in the database given its id
def get_category_name(category_id)
  db = SQLite3::Database.new "#{$script_dir}/cfps.db"
  category = db.get_first_value("select name from category where id = ?", [category_id])

  return categor
end

# Fetch the HTML containing list of CFPs for a category 
def scrape_cfp_list(category)
  category_encoded = category.sub(' ','%20')
  content = ''
  page = 1

  until (content =~ /Expired/)
    uri = "http://www.wikicfp.com/cfp/call?conference=#{category_encoded}&page=#{page}"
    response = Net::HTTP.get_response(URI.parse(uri));
    body = response.body[/.*(Event.*)\|\sPage\s\d\s\|/m,1]

    content << body.split(/<\/table>/).first
    page += 1
  end

  content = content.split(/Expired/).first
  return content
end

# Extract links for each individual CFP (from HTML containing list of CFPs)
def scrape_cfp_links(content)
  links = Array.new

  content.scan(/<a\shref="(.*?)">/) do |capture|
    servlet_query = capture[0]
    links.push "http://www.wikicfp.com#{servlet_query}"
  end

  return links
end

# Scrapes info about a particular CFP and stores it in the database
def scrape_and_store_cfp_info(category_id, link)
  uri = link
  response = Net::HTTP.get_response(URI.parse(uri))
  body = response.body

  # http://xkcd.com/1171/ (s/perl/ruby)
  event_name = body[/<title>(.*?):.*<\/title>/,1].strip
  event_full_name = (body[/<title>.*?:(.*)<\/title>/,1] || event_name).strip
  event_date = body[/When<\/th>\s*<td\salign="center">(.*?)<\/td>/m,1].strip
  event_location = body[/Where<\/th>\s*<td\salign="center">(.*?)<\/td>/m,1].strip

  official_link = (body[/Link:\s<a\shref="(.*?)"/,1] || "N/A").strip
  wikicfp_link = link

  abstract_due = (body[/Abstract\sRegistration\sDue.*?([A-Z]{1}[a-z]{2}\s\d{1,2},\s\d{4}?)/m,1] || "N/A").strip
  submission_due = (body[/Submission\sDeadline.*?([A-Z]{1}[a-z]{2}\s\d{1,2},\s\d{4}?)/m,1] || "N/A").strip
  notification_due = (body[/Notification\sDue.*?([A-Z]{1}[a-z]{2}\s\d{1,2},\s\d{4}?)/m,1] || "N/A").strip
  final_due = (body[/Final\sVersion\sDue.*?([A-Z]{1}[a-z]{2}\s\d{1,2},\s\d{4}?)/m,1] || "N/A").strip


  db = SQLite3::Database.new "#{$script_dir}/cfps.db"
  db.execute("INSERT INTO event (id, category_id, name, full_name, date, location, abstract_due, submission_due, notification_due, final_due, wikicfp_link, official_link)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
             [nil, category_id, event_name, event_full_name, event_date, event_location, abstract_due, submission_due, notification_due, final_due, wikicfp_link, official_link])

end


# Main

puts "Script dir: #{$script_dir}\n"
categories = load_categories("#{$script_dir}/categories_full.txt")

categories.each do |cat|
  puts "Processing category: #{cat}\n"

  category_id = get_category_id(cat)

  content = scrape_cfp_list(cat)
  links = scrape_cfp_links(content)

  links.each do |link|
    scrape_and_store_cfp_info(category_id, link)
  end
end
